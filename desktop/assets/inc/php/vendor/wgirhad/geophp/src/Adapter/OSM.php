<?php

/*
 * @author Báthory Péter
 * @since 2016-02-27
 *
 * This code is open-source and licenced under the Modified BSD License.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace geoPHP\Adapter;

use geoPHP\Exception\FileFormatException;
use geoPHP\Exception\InvalidXmlException;
use geoPHP\Exception\IOException;
use geoPHP\Geometry\Geometry;
use geoPHP\Geometry\GeometryCollection;
use geoPHP\Geometry\Point;
use geoPHP\Geometry\MultiPoint;
use geoPHP\Geometry\LineString;
use geoPHP\Geometry\MultiGeometry;
use geoPHP\Geometry\MultiLineString;
use geoPHP\Geometry\Polygon;
use geoPHP\Geometry\MultiPolygon;

/**
 * PHP Geometry <-> OpenStreetMap XML encoder/decoder
 *
 * This adapter is not ready yet. It lacks a relation writer, and the reader has problems with invalid multipolygons
 * Since geoPHP doesn't support metadata, it cannot read and write OSM tags.
 */
class OSM implements GeoAdapter
{
    const OSM_COORDINATE_PRECISION = '%.7f';
    const OSM_API_URL = 'http://openstreetmap.org/api/0.6/';

    /** @var \DOMDocument */
    protected $xmlObj;

    /**
     * @var array<array{point: Point, assigned: bool, tags: array<mixed>, id?: int, used?: bool}>|array{}
     */
    protected $nodes = [];

    /**
     * @var array<array{nodes: array<mixed>, assigned: bool, tags: array<mixed>, isRng: bool}>|array{}
     */
    protected $ways = [];

    /**
     * @var int
     */
    protected $idCounter = 0;

    /**
     * Read OpenStreetMap XML string into geometry objects
     *
     * @param string $osm An OSM XML string
     *
     * @return Geometry|GeometryCollection
     * @throws \Exception
     */
    public function read(string $osm): Geometry
    {
        // Load into DOMDocument
        $this->xmlObj = new \DOMDocument();
        $loadSuccess = @$this->xmlObj->loadXML($osm);
        if (!$loadSuccess) {
            throw new InvalidXmlException();
        }

        try {
            $geom = $this->geomFromXML();
        } catch (\Exception $e) {
            throw new FileFormatException("Cannot read geometries from OSM XML: " . $e->getMessage());
        }

        return $geom;
    }

    protected function geomFromXML(): Geometry
    {
        $geometries = [];

        // Processing OSM Nodes
        $nodes = [];
        foreach ($this->xmlObj->getElementsByTagName('node') as $node) {
            /** @var \DOMElement $node */
            $lat = $node->attributes->getNamedItem('lat')->nodeValue;
            $lon = $node->attributes->getNamedItem('lon')->nodeValue;
            $id = intval($node->attributes->getNamedItem('id')->nodeValue);
            $tags = [];
            foreach ($node->getElementsByTagName('tag') as $tag) {
                $key = $tag->attributes->getNamedItem('k')->nodeValue;
                if ($key === 'source' || $key === 'fixme' || $key === 'created_by') {
                    continue;
                }
                $tags[$key] = $tag->attributes->getNamedItem('v')->nodeValue;
            }
            $nodes[$id] = [
                    'point' => new Point($lon, $lat),
                    'assigned' => false,
                    'tags' => $tags
            ];
        }
        if (empty($nodes)) {
            return new GeometryCollection();
        }

        // Processing OSM Ways
        $ways = [];
        foreach ($this->xmlObj->getElementsByTagName('way') as $way) {
            /** @var \DOMElement $way */
            $id = intval($way->attributes->getNamedItem('id')->nodeValue);
            $wayNodes = [];
            foreach ($way->getElementsByTagName('nd') as $wayNode) {
                $ref = intval($wayNode->attributes->getNamedItem('ref')->nodeValue);
                if (isset($nodes[$ref])) {
                    $nodes[$ref]['assigned'] = true;
                    $wayNodes[] = $ref;
                }
            }
            $tags = [];
            foreach ($way->getElementsByTagName('tag') as $tag) {
                $key = $tag->attributes->getNamedItem('k')->nodeValue;
                if ($key === 'source' || $key === 'fixme' || $key === 'created_by') {
                    continue;
                }
                $tags[$key] = $tag->attributes->getNamedItem('v')->nodeValue;
            }
            if (count($wayNodes) >= 2) {
                $ways[$id] = [
                        'nodes' => $wayNodes,
                        'assigned' => false,
                        'tags' => $tags,
                        'isRing' => ($wayNodes[0] === $wayNodes[count($wayNodes) - 1])
                ];
            }
        }


        // Processing OSM Relations
        /** @var \DOMElement $relation */
        foreach ($this->xmlObj->getElementsByTagName('relation') as $relation) {
            /** @var Point[] */
            $relationPoints = [];
            /** @var LineString[] */
            $relationLines = [];
            /** @var Polygon[] */
            $relationPolygons = [];

            static $polygonalTypes = ['multipolygon', 'boundary'];
            static $linearTypes = ['route', 'waterway'];
            $relationType = null;
            foreach ($relation->getElementsByTagName('tag') as $tag) {
                if ($tag->attributes->getNamedItem('k')->nodeValue == 'type') {
                    $relationType = $tag->attributes->getNamedItem('v')->nodeValue;
                }
            }

            // Collect relation members
            /** @var array<int, array<mixed>> $relationWays */
            $relationWays = [];
            foreach ($relation->getElementsByTagName('member') as $member) {
                $memberType = $member->attributes->getNamedItem('type')->nodeValue;
                $ref = (int) $member->attributes->getNamedItem('ref')->nodeValue;

                if ($memberType === 'node' &&  isset($nodes[$ref])) {
                    $nodes[$ref]['assigned'] = true;
                    $relationPoints[] = $nodes[$ref]['point'];
                }
                if ($memberType === 'way' &&  isset($ways[$ref])) {
                    $ways[$ref]['assigned'] = true;
                    $relationWays[$ref] = $ways[$ref]['nodes'];
                }
            }

            if (in_array($relationType, $polygonalTypes)) {
                $relationPolygons = $this->processMultipolygon($relationWays, $nodes);
            }
            if (in_array($relationType, $linearTypes)) {
                $relationLines = $this->processRoutes($relationWays, $nodes);
            }

            // Assemble relation geometries
            $geometryCollection = [];
            if (!empty($relationPolygons)) {
                $geometryCollection[] = count($relationPolygons) == 1
                    ? $relationPolygons[0]
                    : new MultiPolygon($relationPolygons);
            }
            if (!empty($relationLines)) {
                $geometryCollection[] = count($relationLines) == 1
                    ? $relationLines[0]
                    : new MultiLineString($relationLines);
            }
            if (!empty($relationPoints)) {
                $geometryCollection[] = count($relationPoints) == 1
                    ? $relationPoints[0]
                    : new MultiPoint($relationPoints);
            }

            if (!empty($geometryCollection)) {
                $geometries[] = count($geometryCollection) == 1
                    ? $geometryCollection[0]
                    : new GeometryCollection($geometryCollection);
            }
        }

        // Process ways
        foreach ($ways as $way) {
            if (
                (!$way['assigned'] || !empty($way['tags']))
                && !isset($way['tags']['boundary'])
                && (!isset($way['tags']['natural'])  || $way['tags']['natural'] !== 'mountain_range')
            ) {
                $linePoints = [];
                foreach ($way['nodes'] as $wayNode) {
                    $linePoints[] = $nodes[$wayNode]['point'];
                }
                $line = new LineString($linePoints);
                if ($way['isRing']) {
                    $polygon = new Polygon([$line]);
                    if ($polygon->isSimple()) {
                        $geometries[] = $polygon;
                    } else {
                        $geometries[] = $line;
                    }
                } else {
                    $geometries[] = $line;
                }
            }
        }

        foreach ($nodes as $node) {
            if (!$node['assigned'] || !empty($node['tags'])) {
                $geometries[] = $node['point'];
            }
        }

        return count($geometries) == 1 ? $geometries[0] : new GeometryCollection($geometries);
    }

    /**
     * @param array<array<mixed>> $relationWays
     * @param array<array<mixed>> $nodes
     * @return LineString[]
     */
    protected function processRoutes(array &$relationWays, array &$nodes): array
    {

        // Construct lines
        /** @var LineString[] $lineStrings */
        $lineStrings = [];
        while (count($relationWays)) {
            $line = array_shift($relationWays);
            if ($line[0] !== $line[count($line) - 1]) {
                do {
                    $waysAdded = 0;
                    foreach ($relationWays as $id => $wayNodes) {
                        // Last node of ring = first node of way => put way to the end of ring
                        if ($line[count($line) - 1] === $wayNodes[0]) {
                            $line = array_merge($line, array_slice($wayNodes, 1));
                            unset($relationWays[$id]);
                            $waysAdded++;
                        // Last node of ring = last node of way => reverse way and put to the end of ring
                        } elseif ($line[count($line) - 1] === $wayNodes[count($wayNodes) - 1]) {
                            $line = array_merge($line, array_slice(array_reverse($wayNodes), 1));
                            unset($relationWays[$id]);
                            $waysAdded++;
                        // First node of ring = last node of way => put way to the beginning of ring
                        } elseif ($line[0] === $wayNodes[count($wayNodes) - 1]) {
                            $line = array_merge(array_slice($wayNodes, 0, count($wayNodes) - 1), $line);
                            unset($relationWays[$id]);
                            $waysAdded++;
                        // First node of ring = first node of way => reverse way and put to the beginning of ring
                        } elseif ($line[0] === $wayNodes[0]) {
                            $line = array_merge(array_reverse(array_slice($wayNodes, 1)), $line);
                            unset($relationWays[$id]);
                            $waysAdded++;
                        }
                    }
                // If line members are not ordered, we need to repeat end matching some times
                } while ($waysAdded > 0);
            }

            // Create the new LineString
            $linePoints = [];
            foreach ($line as $lineNode) {
                $linePoints[] = $nodes[$lineNode]['point'];
            }
            $lineStrings[] = new LineString($linePoints);
        }

        return $lineStrings;
    }

    /**
     * @param array<array<mixed>> $relationWays
     * @param array<array<mixed>> $nodes
     * @return Polygon[]
     */
    protected function processMultipolygon(array &$relationWays, array &$nodes): array
    {
        /* TODO: what to do with broken rings?
         * I propose to force-close if start -> end point distance is less then 10% of line length, otherwise drop it.
         * But if dropped, its inner ring will be outers, which is not good.
         * We should save the role for each ring (outer, inner, mixed) during ring creation
         * and check it during grouping rings.
         */

        // Construct rings
        /** @var Polygon[] $rings */
        $rings = [];
        while (!empty($relationWays)) {
            $ring = array_shift($relationWays);
            if ($ring[0] !== $ring[count($ring) - 1]) {
                do {
                    $waysAdded = 0;
                    foreach ($relationWays as $id => $wayNodes) {
                        // Last node of ring = first node of way => put way to the end of ring
                        if ($ring[count($ring) - 1] === $wayNodes[0]) {
                            $ring = array_merge($ring, array_slice($wayNodes, 1));
                            unset($relationWays[$id]);
                            $waysAdded++;
                        // Last node of ring = last node of way => reverse way and put to the end of ring
                        } elseif ($ring[count($ring) - 1] === $wayNodes[count($wayNodes) - 1]) {
                            $ring = array_merge($ring, array_slice(array_reverse($wayNodes), 1));
                            unset($relationWays[$id]);
                            $waysAdded++;
                        // First node of ring = last node of way => put way to the beginning of ring
                        } elseif ($ring[0] === $wayNodes[count($wayNodes) - 1]) {
                            $ring = array_merge(array_slice($wayNodes, 0, count($wayNodes) - 1), $ring);
                            unset($relationWays[$id]);
                            $waysAdded++;
                        // First node of ring = first node of way => reverse way and put to the beginning of ring
                        } elseif ($ring[0] === $wayNodes[0]) {
                            $ring = array_merge(array_reverse(array_slice($wayNodes, 1)), $ring);
                            unset($relationWays[$id]);
                            $waysAdded++;
                        }
                    }
                // If ring members are not ordered, we need to repeat end matching some times
                } while ($waysAdded > 0 && $ring[0] !== $ring[count($ring) - 1]);
            }

            // Create the new Polygon
            if ($ring[0] === $ring[count($ring) - 1]) {
                $ringPoints = [];
                foreach ($ring as $ringNode) {
                    $ringPoints[] = $nodes[$ringNode]['point'];
                }
                $newPolygon = new Polygon([new LineString($ringPoints)]);
                if ($newPolygon->isSimple()) {
                    $rings[] = $newPolygon;
                }
            }
        }

        // Calculate containment
        $containment = array_fill(0, count($rings), array_fill(0, count($rings), false));
        foreach ($rings as $i => $ring) {
            foreach ($rings as $j => $ring2) {
                if ($i !== $j && $ring->contains($ring2)) {
                    $containment[$i][$j] = true;
                }
            }
        }
        $containmentCount = count($containment);

        /*
        print '&nbsp; &nbsp;';
        for($i=0; $i<count($rings); $i++) {
            print $rings[$i]->getNumberOfPoints() . ' ';
        }
        print "<br>";
        for($i=0; $i<count($rings); $i++) {
            print $rings[$i]->getNumberOfPoints() . ' ';
            for($j=0; $j<count($rings); $j++) {
                print ($containment[$i][$j] ? '1' : '0') . ' ';
            }
            print "<br>";
        }*/

        // Group rings (outers and inners)

        /** @var boolean[] $found */
        $found = array_fill(0, $containmentCount, false);
        $foundCount = 0;
        $round = 0;
        /** @var int[][] $polygonsRingIds */
        $polygonsRingIds = [];
        /** @var Polygon[] $relationPolygons */
        $relationPolygons = [];
        while ($foundCount < $containmentCount && $round < 100) {
            $ringsFound = [];
            for ($i = 0; $i < $containmentCount; $i++) {
                if ($found[$i]) {
                    continue;
                }
                $containCount = 0;
                for ($j = 0; $j < count($containment[$i]); $j++) {
                    if (!$found[$j]) {
                        $containCount += $containment[$j][$i];
                    }
                }
                if ($containCount === 0) {
                    $ringsFound[] = $i;
                }
            }
            if ($round % 2 === 0) {
                $polygonsRingIds = [];
            }
            foreach ($ringsFound as $ringId) {
                $found[$ringId] = true;
                $foundCount++;
                if ($round % 2 === 1) {
                    foreach ($polygonsRingIds as $outerId => $polygon) {
                        if ($containment[$outerId][$ringId]) {
                            $polygonsRingIds[$outerId][] = $ringId;
                        }
                    }
                } else {
                    $polygonsRingIds[$ringId] = [0 => $ringId];
                }
            }
            if ($round % 2 === 1 || $foundCount === $containmentCount) {
                foreach ($polygonsRingIds as $k => $ringGroup) {
                    $linearRings = [];
                    foreach ($ringGroup as $polygonRing) {
                        $linearRings[] = $rings[$polygonRing]->exteriorRing();
                    }
                    $relationPolygons[] = new Polygon($linearRings);
                }
            }
            ++$round;
        }

        return $relationPolygons;
    }



    public function write(Geometry $geometry): string
    {

        $this->processGeometry($geometry);

        $osm = "<?xml version='1.0' encoding='UTF-8'?>\n<osm version='0.6' upload='false' generator='geoPHP'>\n";
        foreach ($this->nodes as $latlon => $node) {
            $latlon = explode('_', $latlon);
            $osm .= "  <node id='{$node['id']}' visible='true' lat='$latlon[0]' lon='$latlon[1]' />\n";
        }
        foreach ($this->ways as $wayId => $way) {
            $osm .= "  <way id='{$wayId}' visible='true'>\n";
            foreach ($way as $nodeId) {
                $osm .= "    <nd ref='{$nodeId}' />\n";
            }
            $osm .= "  </way>\n";
        }

        $osm .= "</osm>";
        return $osm;
    }

    /**
     * @param Geometry $geometry
     */
    protected function processGeometry(Geometry $geometry): void
    {
        if (!$geometry->isEmpty()) {
            switch ($geometry->geometryType()) {
                case Geometry::POINT:
                    /** @var Point $geometry */
                    $this->processPoint($geometry);
                    break;
                case Geometry::LINE_STRING:
                    /** @var LineString $geometry */
                    $this->processLineString($geometry);
                    break;
                case Geometry::POLYGON:
                    /** @var Polygon $geometry */
                    $this->processPolygon($geometry);
                    break;
                case Geometry::MULTI_POINT:
                case Geometry::MULTI_LINE_STRING:
                case Geometry::MULTI_POLYGON:
                case Geometry::GEOMETRY_COLLECTION:
                    /** @var MultiGeometry $geometry */
                    $this->processCollection($geometry);
                    break;
            }
        }
    }

    /**
     * @param Point $point
     * @param bool|false $isWayPoint
     * @return int
     */
    protected function processPoint(Point $point, bool $isWayPoint = false): int
    {
        $nodePosition = sprintf(
            self::OSM_COORDINATE_PRECISION . '_' . self::OSM_COORDINATE_PRECISION,
            $point->y(),
            $point->x()
        );
        if (!isset($this->nodes[$nodePosition])) {
            $this->nodes[$nodePosition] = ['id' => --$this->idCounter, "used" => $isWayPoint];
            return $this->idCounter;
        } else {
            if ($isWayPoint) {
                $this->nodes[$nodePosition]['used'] = true;
            }
            return $this->nodes[$nodePosition]['id'];
        }
    }

    /**
     * @param LineString $line
     */
    protected function processLineString(LineString $line): void
    {
        $processedNodes = [];
        foreach ($line->getPoints() as $point) {
            $processedNodes[] = $this->processPoint($point, true);
        }
        $this->ways[--$this->idCounter] = $processedNodes;
    }

    /**
     * @param Polygon $polygon
     */
    protected function processPolygon(Polygon $polygon): void
    {
        // TODO: Support interior rings
        $this->processLineString($polygon->exteriorRing());
    }

    /**
     * @param MultiGeometry $collection
     */
    protected function processCollection(MultiGeometry $collection): void
    {
        // TODO: multi geometries should be converted to relations
        foreach ($collection->getComponents() as $component) {
            $this->processGeometry($component);
        }
    }

    public static function downloadFromOSMByBbox(float $left, float $bottom, float $right, float $top): string
    {
        $osmFile = file_get_contents(self::OSM_API_URL . "map?bbox={$left},{$bottom},{$right},{$top}");
        if ($osmFile !== false) {
            return $osmFile;
        } else {
            throw new IOException("Failed to download from OSM.");
        }
    }
}
