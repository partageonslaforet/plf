create extension postgis


-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               PostgreSQL 16.0, compiled by Visual C++ build 1935, 64-bit
-- Server OS:                    
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES  */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping structure for table public.plf_spw_territoires_geom
CREATE TABLE IF NOT EXISTS "plf_spw_territoires_geom" (
	"saison" SMALLINT NOT NULL,
	"n_lot" VARCHAR(10) NOT NULL,
	"seq" SMALLINT NOT NULL,
	"geom" geometry NOT NULL,
	PRIMARY KEY ("saison", "n_lot", "seq")
);

-- Data exporting was unselected.

-- Dumping structure for table public.plf_spw_territoires_geomDEG
CREATE TABLE IF NOT EXISTS "plf_spw_territoires_geomDEG" (
	"saison" SMALLINT NOT NULL,
	"n_lot" VARCHAR(10) NOT NULL,
	"seq" SMALLINT NOT NULL,
	"geom" geometry NOT NULL,
	PRIMARY KEY ("saison", "n_lot", "seq")
);

-- Data exporting was unselected.