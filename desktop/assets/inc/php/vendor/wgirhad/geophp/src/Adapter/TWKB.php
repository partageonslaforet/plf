<?php

/**
 * This file contains the BinaryReader class.
 * For more information see the class description below.
 *
 * @author Peter Bathory <peter.bathory@cartographia.hu>
 * @since 2016-02-18
 *
 * This code is open-source and licenced under the Modified BSD License.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace geoPHP\Adapter;

use geoPHP\Exception\InvalidGeometryException;
use geoPHP\Exception\IOException;
use geoPHP\Geometry\Collection;
use geoPHP\Geometry\Geometry;
use geoPHP\Geometry\GeometryCollection;
use geoPHP\Geometry\LineString;
use geoPHP\Geometry\MultiGeometry;
use geoPHP\Geometry\MultiLineString;
use geoPHP\Geometry\MultiPoint;
use geoPHP\Geometry\MultiPolygon;
use geoPHP\Geometry\Point;
use geoPHP\Geometry\Polygon;

/**
 * PHP Geometry <-> TWKB encoder/decoder
 *
 * "Tiny Well-known Binary is a multi-purpose format for serializing vector geometry data into a byte buffer,
 * with an emphasis on minimizing size of the buffer."
 * @see https://github.com/TWKB/Specification/blob/master/twkb.md
 *
 * This implementation supports:
 * - reading and writing all geometry types (1-7)
 * - empty geometries
 * - extended precision (Z, M coordinates; custom precision)
 * Partially supports:
 * - bounding box: can read and write, but don't store readed boxes (API missing)
 * - size attribute: can read and write size attribute, but seeking is not supported
 * - ID list: can read and write, but API is completely missing
 */
class TWKB implements GeoAdapter
{
    /**
     * @var array<string, mixed>
     */
    protected $writeOptions = [
        'decimalDigitsXY'      => 5,
        'decimalDigitsZ'       =>  0,
        'decimalDigitsM'       =>  0,
        'includeSize'          => false,
        'includeBoundingBoxes' => false,
        'xyFactor'             => null,
        'zFactor'              => null,
        'mFactor'              => null,
        'hasZ'                 => null,
        'hasM'                 => null,
    ];

    /**
     * @var Point|null
     */
    private $lastPoint = null;

    /**
     * @var BinaryReader
     */
    private $reader;

    /**
     * @var BinaryWriter
     */
    private $writer;

    /**
     * @var array<string, int> Maps Geometry types to TWKB type codes
     */
    protected static $typeMap = [
            Geometry::POINT               => 1,
            Geometry::LINE_STRING         => 2,
            Geometry::POLYGON             => 3,
            Geometry::MULTI_POINT         => 4,
            Geometry::MULTI_LINE_STRING   => 5,
            Geometry::MULTI_POLYGON       => 6,
            Geometry::GEOMETRY_COLLECTION => 7
    ];

    /**
     * Read TWKB into geometry objects
     *
     * @param string $twkb        Tiny Well-known-binary string
     * @param bool   $isHexString If this is a hexadecimal string that is in need of packing
     *
     * @return Geometry
     *
     * @throws \Exception
     * @throws IOException
     */
    public function read(string $twkb, bool $isHexString = false): Geometry
    {
        if ($isHexString) {
            $twkb = @pack('H*', $twkb);
        }

        if (empty($twkb)) {
            throw new IOException('Cannot read empty TWKB input. Found ' . gettype($twkb));
        }

        $this->reader = new BinaryReader($twkb);

        $geometry = $this->getGeometry();

        $this->reader->close();

        return $geometry;
    }

    protected function getGeometry(): Geometry
    {
        $options = [];
        $type = $this->reader->readUInt8();
        $metadataHeader = $this->reader->readUInt8();

        $geometryType = $type & 0x0F;
        $options['precision'] = BinaryReader::zigZagDecode($type >> 4);
        $options['precisionFactor'] = pow(10, $options['precision']);

        $options['hasBoundingBox'] = ($metadataHeader >> 0 & 1) == 1;
        $options['hasSizeAttribute'] = ($metadataHeader >> 1 & 1) == 1;
        $options['hasIdList'] = ($metadataHeader >> 2 & 1) == 1;
        $options['hasExtendedPrecision'] = ($metadataHeader >> 3 & 1) == 1;
        $options['isEmpty'] = ($metadataHeader >> 4 & 1) == 1;
        $options['unused1'] = ($metadataHeader >> 5 & 1) == 1;
        $options['unused2'] = ($metadataHeader >> 6 & 1) == 1;
        $options['unused3'] = ($metadataHeader >> 7 & 1) == 1;

        if ($options['hasExtendedPrecision']) {
            $extendedPrecision = $this->reader->readUInt8();

            $options['hasZ'] = ($extendedPrecision & 0x01) === 0x01;
            $options['hasM'] = ($extendedPrecision & 0x02) === 0x02;

            $options['zPrecision'] = ($extendedPrecision & 0x1C) >> 2;
            $options['zPrecisionFactor'] = pow(10, $options['zPrecision']);

            $options['mPrecision'] = ($extendedPrecision & 0xE0) >> 5;
            $options['mPrecisionFactor'] = pow(10, $options['mPrecision']);
        } else {
            $options['hasZ'] = false;
            $options['hasM'] = false;
        }
        if ($options['hasSizeAttribute']) {
            $options['remainderSize'] = $this->reader->readUVarInt();
        }
        if ($options['hasBoundingBox']) {
            $dimension = 2 + ($options['hasZ'] ? 1 : 0) + ($options['hasM'] ? 1 : 0);
            $precisions = [
                $options['precisionFactor'],
                $options['precisionFactor'],
                $options['hasZ'] ? $options['zPrecisionFactor'] : 0,
                $options['hasM'] ? $options['mPrecisionFactor'] : 0
            ];
            $bBoxMin = $bBoxMax = [];
            for ($i = 0; $i < $dimension; $i++) {
                $bBoxMin[$i] = $this->reader->readUVarInt() / $precisions[$i];
                $bBoxMax[$i] = $this->reader->readUVarInt() / $precisions[$i] + $bBoxMin[$i];
            }
            /** @noinspection PhpUndefinedVariableInspection (minimum 2 dimension) */
            $options['boundingBox'] = ['minXYZM' => $bBoxMin, 'maxXYZM' => $bBoxMax];
        }

        if ($options['unused1']) {
            $this->reader->readUVarInt();
        }
        if ($options['unused2']) {
            $this->reader->readUVarInt();
        }
        if ($options['unused3']) {
            $this->reader->readUVarInt();
        }

        $this->lastPoint = new Point(0, 0, 0, 0);

        switch ($geometryType) {
            case 1:
                $geometry = $this->getPoint($options);
                break;
            case 2:
                $geometry = $this->getLineString($options);
                break;
            case 3:
                $geometry = $this->getPolygon($options);
                break;
            case 4:
                $geometry = $this->getMulti('Point', $options);
                break;
            case 5:
                $geometry = $this->getMulti('LineString', $options);
                break;
            case 6:
                $geometry = $this->getMulti('Polygon', $options);
                break;
            case 7:
                $geometry = $this->getMulti('Geometry', $options);
                break;
            default:
                throw new IOException(
                    'Geometry type ' . $geometryType .
                        ' (' . (array_search($geometryType, self::$typeMap) ?: 'unknown') . ') not supported'
                );
        }

        return $geometry;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return Point
     * @throws InvalidGeometryException
     */
    protected function getPoint(array $options): Point
    {
        if ($options['isEmpty']) {
            return new Point();
        }
        $x = round(
            $this->lastPoint->x() + $this->reader->readSVarInt() / $options['precisionFactor'],
            $options['precision']
        );
        $y = round(
            $this->lastPoint->y() + $this->reader->readSVarInt() / $options['precisionFactor'],
            $options['precision']
        );
        $z = $options['hasZ'] ? round(
            $this->lastPoint->z() + $this->reader->readSVarInt() / $options['zPrecisionFactor'],
            $options['zPrecision']
        ) : null;
        $m = $options['hasM'] ? round(
            $this->lastPoint->m() + $this->reader->readSVarInt() / $options['mPrecisionFactor'],
            $options['mPrecision']
        ) : null;

        $this->lastPoint = new Point($x, $y, $z, $m);
        return $this->lastPoint;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return LineString
     * @throws InvalidGeometryException
     */
    protected function getLineString(array $options): LineString
    {
        if ($options['isEmpty']) {
            return new LineString();
        }

        $pointCount = $this->reader->readUVarInt();

        $points = [];
        for ($i = 0; $i < $pointCount; $i++) {
            $points[] = $this->getPoint($options);
        }

        return new LineString($points);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return Polygon
     * @throws InvalidGeometryException
     */
    protected function getPolygon(array $options): Polygon
    {
        if ($options['isEmpty']) {
            return new Polygon();
        }

        $ringCount = $this->reader->readUVarInt();

        $rings = [];
        for ($i = 0; $i < $ringCount; $i++) {
            $rings[] = $this->getLineString($options);
        }

        return new Polygon($rings, true);
    }

    /**
     * @param string $type
     * @param array<string, mixed> $options
     *
     * @return MultiGeometry
     * @throws InvalidGeometryException
     */
    protected function getMulti(string $type, array $options): MultiGeometry
    {
        $multiLength = $this->reader->readUVarInt();

        if ($options['hasIdList']) {
            for ($i = 0; $i < $multiLength; $i++) {
                $idList[] = $this->reader->readSVarInt();
            }
        }

        $components = [];
        for ($i = 0; $i < $multiLength; $i++) {
            if ($type !== 'Geometry') {
                $func = 'get' . $type;
                $components[] = $this->$func($options);
            } else {
                $components[] = $this->getGeometry();
            }
        }
        switch ($type) {
            case 'Point':
                return new MultiPoint($components);
            case 'LineString':
                return new MultiLineString($components);
            case 'Polygon':
                return new MultiPolygon($components);
            case 'Geometry':
                return new GeometryCollection($components);
        }
        throw new IOException("Unknown multi-geometry type '$type'.");
    }


/******* WRITER *******/

    /**
     * Serialize geometries into TWKB string.
     *
     * @param Geometry $geometry    The geometry
     * @param bool $writeAsHex      Write the result in binary or hexadecimal system. Default is false.
     * @param int|null $decimalDigitsXY Coordinate precision of X and Y. Default is 5 decimals.
     * @param null $decimalDigitsZ  Coordinate precision of Z. Default is 0 decimal.
     * @param null $decimalDigitsM  Coordinate precision of M. Default is 0 decimal.
     * @param bool $includeSizes    Includes size in bytes of the remainder geometry after the size attribute.
     *                              Default is false.
     * @param bool $includeBoundingBoxes Includes the coordinates of bounding box' two corner. Default is false.
     *
     * @return string binary or hexadecimal representation of TWKB.
     */
    public function write(
        Geometry $geometry,
        bool $writeAsHex = false,
        ?int $decimalDigitsXY = 5,
        ?int $decimalDigitsZ = 0,
        ?int $decimalDigitsM = 0,
        ?bool $includeSizes = false,
        ?bool $includeBoundingBoxes = false
    ): string {
        $this->writer = new BinaryWriter();

        $this->writeOptions = [
                'decimalDigitsXY' => $decimalDigitsXY !== null
                    ? $decimalDigitsXY
                    : $this->writeOptions['decimalDigitsXY'],
                'decimalDigitsZ' => $decimalDigitsZ !== null
                    ? $decimalDigitsZ
                    : $this->writeOptions['decimalDigitsZ'],
                'decimalDigitsM' => $decimalDigitsM !== null
                    ? $decimalDigitsM
                    : $this->writeOptions['decimalDigitsM'],
                'includeSize' => $includeSizes != null
                    ? $includeSizes
                    : $this->writeOptions['includeSize'],
                'includeBoundingBoxes' => $includeBoundingBoxes !== null
                    ? $includeBoundingBoxes
                    : $this->writeOptions['includeBoundingBoxes']
        ];
        $this->writeOptions = array_merge(
            $this->writeOptions,
            [
                'xyFactor' => pow(10, $this->writeOptions['decimalDigitsXY']),
                'zFactor' => pow(10, $this->writeOptions['decimalDigitsZ']),
                'mFactor' => pow(10, $this->writeOptions['decimalDigitsM'])
            ]
        );

        $twkb = $this->writeGeometry($geometry);

        return $writeAsHex ? current(unpack('H*', $twkb)) : $twkb;
    }

    /**
     * @param Geometry $geometry
     *
     * @return string
     */
    protected function writeGeometry(Geometry $geometry): string
    {
        $this->writeOptions['hasZ'] = $geometry->is3D();
        $this->writeOptions['hasM'] = $geometry->isMeasured();

        // Type and precision
        $type = self::$typeMap[$geometry->geometryType()] +
                (BinaryWriter::zigZagEncode($this->writeOptions['decimalDigitsXY']) << 4);
        $twkbHead = $this->writer->writeUInt8($type);

        // Is there extended precision information?
        $metadataHeader = $this->writeOptions['includeBoundingBoxes'] << 0;
        // Is there extended precision information?
        $metadataHeader += $this->writeOptions['includeSize'] << 1;
        // Is there an ID list?
        // TODO: implement this (needs metadata support in geoPHP)
        //$metadataHeader += $this->writeOptions['hasIdList'] << 2;
        // Is there extended precision information?
        $metadataHeader += ($geometry->is3D() || $geometry->isMeasured()) << 3;
        // Is this an empty geometry?
        $metadataHeader += $geometry->isEmpty() << 4;

        $twkbHead .= $this->writer->writeUInt8($metadataHeader);

        $twkbGeom = '';
        if (!$geometry->isEmpty()) {
            $this->lastPoint = new Point(0, 0, 0, 0);

            switch ($geometry->geometryType()) {
                case Geometry::POINT:
                    /** @var Point $geometry */
                    $twkbGeom .= $this->writePoint($geometry);
                    break;
                case Geometry::LINE_STRING:
                    /** @var LineString $geometry */
                    $twkbGeom .= $this->writeLineString($geometry);
                    break;
                case Geometry::POLYGON:
                    /** @var Polygon $geometry */
                    $twkbGeom .= $this->writePolygon($geometry);
                    break;
                case Geometry::MULTI_POINT:
                case Geometry::MULTI_LINE_STRING:
                case Geometry::MULTI_POLYGON:
                case Geometry::GEOMETRY_COLLECTION:
                    /** @var MultiGeometry $geometry */
                    $twkbGeom .= $this->writeMulti($geometry);
                    break;
            }
        }

        if ($this->writeOptions['includeBoundingBoxes']) {
            $bBox = $geometry->getBBox();
            // X
            $twkbBox = $this->writer->writeSVarInt($bBox['minx'] * $this->writeOptions['xyFactor']);
            $twkbBox .= $this->writer->writeSVarInt(($bBox['maxx'] - $bBox['minx']) * $this->writeOptions['xyFactor']);
            // Y
            $twkbBox .= $this->writer->writeSVarInt($bBox['miny'] * $this->writeOptions['xyFactor']);
            $twkbBox .= $this->writer->writeSVarInt(($bBox['maxy'] - $bBox['miny']) * $this->writeOptions['xyFactor']);
            if ($geometry->is3D()) {
                $bBox['minz'] = $geometry->minimumZ();
                $bBox['maxz'] = $geometry->maximumZ();
                $twkbBox .= $this->writer->writeSVarInt(
                    round($bBox['minz'] * $this->writeOptions['zFactor'])
                );
                $twkbBox .= $this->writer->writeSVarInt(
                    round(($bBox['maxz'] - $bBox['minz']) * $this->writeOptions['zFactor'])
                );
            }
            if ($geometry->isMeasured()) {
                $bBox['minm'] = $geometry->minimumM();
                $bBox['maxm'] = $geometry->maximumM();
                $twkbBox .= $this->writer->writeSVarInt($bBox['minm'] * $this->writeOptions['mFactor']);
                $twkbBox .= $this->writer->writeSVarInt(
                    ($bBox['maxm'] - $bBox['minm']) * $this->writeOptions['mFactor']
                );
            }
            $twkbGeom = $twkbBox . $twkbGeom;
        }

        if ($geometry->is3D() || $geometry->isMeasured()) {
            $extendedPrecision = 0;
            if ($geometry->is3D()) {
                $extendedPrecision |= 0x1 | ($this->writeOptions['decimalDigitsZ'] << 2);
            }
            if ($geometry->isMeasured()) {
                $extendedPrecision |= 0x2 | ($this->writeOptions['decimalDigitsM'] << 5);
            }
            $twkbHead .= $this->writer->writeUInt8($extendedPrecision);
        }
        if ($this->writeOptions['includeSize']) {
            $twkbHead .= $this->writer->writeUVarInt(strlen($twkbGeom));
        }

        return $twkbHead . $twkbGeom;
    }

    /**
     * @param Point $geometry
     * @return string
     */
    protected function writePoint(Point $geometry): string
    {
        $x = round($geometry->x() * $this->writeOptions['xyFactor']);
        $y = round($geometry->y() * $this->writeOptions['xyFactor']);
        $z = round($geometry->z() * $this->writeOptions['zFactor']);
        $m = round($geometry->m() * $this->writeOptions['mFactor']);

        $twkb = $this->writer->writeSVarInt($x - $this->lastPoint->x());
        $twkb .= $this->writer->writeSVarInt($y - $this->lastPoint->y());
        if ($this->writeOptions['hasZ']) {
            $twkb .= $this->writer->writeSVarInt($z - $this->lastPoint->z());
        }
        if ($this->writeOptions['hasM']) {
            $twkb .= $this->writer->writeSVarInt($m - $this->lastPoint->m());
        }

        $this->lastPoint = new Point(
            $x,
            $y,
            $this->writeOptions['hasZ'] ? $z : null,
            $this->writeOptions['hasM'] ? $m : null
        );

        return $twkb;
    }

    /**
     * @param LineString $geometry
     * @return string
     */
    protected function writeLineString(LineString $geometry): string
    {
        $twkb = $this->writer->writeUVarInt($geometry->numPoints());
        foreach ($geometry->getComponents() as $component) {
            $twkb .= $this->writePoint($component);
        }
        return $twkb;
    }

    /**
     * @param Polygon $geometry
     * @return string
     */
    protected function writePolygon(Polygon $geometry): string
    {
        $twkb = $this->writer->writeUVarInt($geometry->numGeometries());
        foreach ($geometry->getComponents() as $component) {
            $twkb .= $this->writeLineString($component);
        }
        return $twkb;
    }

    /**
     * @param MultiGeometry $geometry
     * @return string
     */
    protected function writeMulti(MultiGeometry $geometry): string
    {
        $twkb = $this->writer->writeUVarInt($geometry->numGeometries());
        //if ($geometry->hasIdList()) {
        //  foreach ($geometry->getComponents() as $component) {
        //      $this->writer->writeUVarInt($component->getId());
        //  }
        //}
        foreach ($geometry->getComponents() as $component) {
            if ($geometry->geometryType() !== Geometry::GEOMETRY_COLLECTION) {
                $func = 'write' . $component->geometryType();
                $twkb .= $this->$func($component);
            } else {
                $twkb .= $this->writeGeometry($component);
            }
        }
        return $twkb;
    }
}
