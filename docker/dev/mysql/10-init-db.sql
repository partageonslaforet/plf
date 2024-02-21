-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for plf
CREATE DATABASE IF NOT EXISTS `dev-plf` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `dev-plf`;

-- Dumping structure for table plf.plf_cgt_itineraires
CREATE TABLE IF NOT EXISTS `plf_cgt_itineraires` (
  `itineraire_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SEQ` tinyint NOT NULL,
  `organisme` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `localite` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commune` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `urlweb` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idreco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `distance` decimal(5,1) DEFAULT NULL,
  `typecirc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signaletique` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hdifmin` smallint DEFAULT NULL,
  `hdifmax` smallint DEFAULT NULL,
  `gpx_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgped` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgpedpmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgpedpou` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgtrail` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgtrailpmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgtrailpou` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgequ` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgatt` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgvtc` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgvtcpmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgvtt` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgvttpmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgenduro` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgfreestyle` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusggravel` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgvelotour` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgvelotourpmr` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgvtc_a29b` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgxc` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgauto` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgmoto` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgnwalk` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgski` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infusgskialpin` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`itineraire_id`) USING BTREE,
  UNIQUE KEY `uk_nom_seq` (`nom`,`SEQ`) USING BTREE,
  KEY `itineraire_id` (`itineraire_id`),
  KEY `commune` (`commune`),
  KEY `localite` (`localite`)
) ENGINE=InnoDB AUTO_INCREMENT=2125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Dumping structure for table plf.plf_infos
CREATE TABLE IF NOT EXISTS `plf_infos` (
  `infos_id` int NOT NULL AUTO_INCREMENT,
  `Infos_Name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Infos_Date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Infos_Value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`infos_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table plf.plf_infos: ~6 rows (approximately)
INSERT INTO `plf_infos` (`infos_id`, `Infos_Name`, `Infos_Date`, `Infos_Value`) VALUES
	(1, 'cron_chasses', '1999-01-01 00:00:00', 'N/A'),
	(2, 'cron_territoires', '1999-01-01 00:00:00', 'N/A'),
	(3, 'cron_itineraires_step1', '1999-01-01 00:00:00', 'N/A'),
	(4, 'cron_cc', '1999-01-01 00:00:00', 'N/A'),
	(5, 'cron_itineraires_step2', '1999-01-01 00:00:00', 'N/A'),
	(6, 'cron_cantonnement', '1999-01-01 00:00:00', 'N/A'),
	(7, 'cron_clean_temporary_files', '1999-01-01 00:00:00', 'N/A');

-- Dumping structure for table plf.plf_spw_cantonnements
CREATE TABLE IF NOT EXISTS `plf_spw_cantonnements` (
  `cantonnement_id` int NOT NULL AUTO_INCREMENT,
  `CAN` smallint NOT NULL,
  `PREPOSE` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CANTON` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TEL_CAN` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GEOM` mediumblob,
  PRIMARY KEY (`cantonnement_id`) USING BTREE,
  UNIQUE KEY `CAN` (`CAN`) USING BTREE,
  KEY `cantonnement_id` (`cantonnement_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Dumping structure for table plf.plf_spw_cantonnements_adresses
CREATE TABLE IF NOT EXISTS `plf_spw_cantonnements_adresses` (
  `num_canton` smallint unsigned NOT NULL,
  `direction` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attache` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CP` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localite` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rue` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  PRIMARY KEY (`num_canton`),
  UNIQUE KEY `num_canton` (`num_canton`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table plf.plf_spw_cantonnements_adresses: ~33 rows (approximately)
INSERT INTO `plf_spw_cantonnements_adresses` (`num_canton`, `direction`, `email`, `attache`, `CP`, `localite`, `rue`, `numero`, `latitude`, `longitude`) VALUES
	(611, 'MONS', 'cantonnement.nature.forets.chimay@spw.wallonie.be', 'Thomas PUISSANT ', '5660', 'COUVIN', 'Route de Dailly', '1', 50.05284, 4.49495),
	(612, 'MONS\n', 'cantonnement.nature.forets.mons@spw.wallonie.be', 'Jean-François DULIERE ', '7000', 'MONS', 'Rue Achille Legrand', '16', 50.4496732, 3.9458946),
	(613, 'MONS', 'cantonnement.nature.forets.thuin@spw.wallonie.be', 'Eric DECLERCQ', '6530', 'THUIN', 'Chemin de l\'Ermitage', '1b', 50.331531, 4.2930111),
	(614, 'MONS', 'cantonnement.nature.forets.nivelles@spw.wallonie.be', 'Tom DRYGALSKI ', '1400', 'NIVELLES', 'avenue Jean Monnet ', '12 Bte 2 A', 50.6182181, 4.3283286),
	(711, 'DINANT', 'cantonnement.nature.forets.beauraing@spw.wallonie.be', 'Olivier HUART ', '5570', 'BARONVILLE', 'Rue Vieille', '58', 50.1275346, 4.9433258),
	(712, 'DINANT', 'cantonnement.nature.forets.dinant@spw.wallonie.be', 'Rémy HAAS', '5500', 'DINANT', 'Rue Alexandre Daoust', '14', 50.2542503, 4.917498),
	(713, 'DINANT', 'cantonnement.nature.forets.rochefort@spw.wallonie.be\n\n', 'Thibaut GHEYSEN ', '5580', 'ROCHEFORT', 'Rue de la Sauvenière', '16', 50.1597058, 5.2222437),
	(721, ' NAMUR', 'cantonnement.nature.forets.viroinval@spw.wallonie.be', 'François DELACRE', '5670', 'NISMES', 'Rue Saint-Roch', '60', 50.0774814, 4.5486447),
	(722, ' NAMUR', 'cantonnement.nature.forets.couvin@spw.wallonie.be', 'Jean LAROCHE ', '5670', 'NISMES', 'Rue Saint-Roch', '60', 50.0774814, 4.5486447),
	(723, ' NAMUR', 'cantonnement.nature.forets.philippeville@spw.wallonie.be', 'Quentin MATHY ', '5600', 'PHILIPPEVILLE', 'Rue du Moulin', '198', 50.1808, 4.5923),
	(724, ' NAMUR', 'cantonnement.nature.forets.namur@spw.wallonie.be', 'LEMAIRE Pascal', '5000', 'NAMUR', 'Avenue Reine Astrid', '39', 50.4626438, 4.8541304),
	(811, 'LIEGE', 'cantonnement.nature.forets.aywaille@spw.wallonie.be', 'Catherine BARVAUX ', '4920', 'SOUGNE-REMOUCHAMPS', 'Rue du Halage', '47', 50.4812494, 5.6968524),
	(812, 'LIEGE', 'cantonnement.nature.forets.liege@spw.wallonie.be', 'Nicolas DELHAYE ', '4000', 'LIEGE', 'Montagne Sainte-Walburge', '2 Bât II', 50.647756, 5.5664964),
	(813, 'LIEGE\n', 'cantonnement.nature.forets.spa@spw.wallonie.be', 'Nicolas DENUIT', '4845', 'JALHAY', 'Balmoral', '41', 50.5089796, 5.892994),
	(821, 'MALMEDY-BULLANGE\n\n', 'cantonnement.nature.forets.bullange@spw.wallonie.be', 'Christophe PANKERT ', '4760', 'BÜLLINGEN/BULLANGE', 'Sankt Vither Strasse', '1', 50.40432, 6.2524396),
	(822, 'MALMEDY-BULLANGE', 'cantonnement.nature.forets.elsenborn@spw.wallonie.be', 'René DAHMEN ', '4750', 'ELSENBORN', 'Unter den Linden', '5', 50.456709, 6.2208042),
	(823, 'MALMEDY-BULLANGE', 'cantonnement.nature.forets.malmedy@spw.wallonie.be', 'Joël VERDIN ', '4960', 'MALMEDY', 'Avenue Mon-Bijou', '8', 50.4167, 6.0333),
	(824, 'MALMEDY-BULLANGE', 'cantonnement.nature.forets.saintvith@spw.wallonie.be', 'Pascal MERTES', '4780', 'SAINT-VITH', 'Klosterstrasse', '32b', 50.2765847, 6.1257259),
	(831, 'LIEGE', 'cantonnement.nature.forets.verviers@spw.wallonie.be', 'Yves PIEPER', '4800', 'VERVIERS', 'Rue de Dinant', '11', 50.5872287, 5.8565752),
	(832, 'MALMEDY-BULLANGE', 'cantonnement.nature.forets.eupen@spw.wallonie.be', 'Maxim PHILIPPS ', '4700', 'EUPEN', 'Haasstrasse', '7', 50.6236911, 6.0370624),
	(911, 'Arlon', 'cantonnement.nature.forets.arlon@spw.wallonie.be', 'Florian NAISSE', '6700', 'Arlon', 'Place Didier', '45', 49.6847144, 5.813325),
	(912, 'Arlon', 'cantonnement.nature.forets.habay@spw.wallonie.be', 'Patrick VERTE', '6720', 'HABAY-LA-NEUVE', 'Rue de l\'Hôtel de Ville', '8', 49.7288825, 5.6480018),
	(913, 'Arlon', 'cantonnement.nature.forets.virton@spw.wallonie.be', 'David STORMS ', '6760', 'Virton', 'Rue Croix Le Maire\n', '17', 49.5674538, 5.5295788),
	(921, 'DINANT', 'cantonnement.nature.forets.bievre@spw.wallonie.be', 'Stéphan ADANT', '5555', 'BIEVRE', 'Rue du centre', '4', 49.941165, 5.0177286),
	(922, 'NEUCHATEAU', 'cantonnement.nature.forets.bouillon@spw.wallonie.be', 'Pierre GIGOUNON', '6830', 'BOUILLON', 'Rue de l’Ange Gardien', '9', 49.7964688, 5.0730275),
	(931, 'MARCHE EN FAMENNE', 'cantonnement.nature.forets.laroche@spw.wallonie.be', '\nSandrine LAMOTTE', '6980', 'LA ROCHE-EN-ARDENNE', 'Rue du Val du Bronze\n', '9', 50.18361, 5.57547),
	(932, 'MARCHE-EN-FAMENNE', 'cantonnement.nature.forets.marche@spw.wallonie.be', 'Damien ROUVROY', '6900', 'MARCHE-EN-FAMENNE', 'Rue du Carmel (Marloie)', '1', 50.2007464, 5.3129003),
	(933, 'VIELSAM', 'cantonnement.nature.forets.vielsalm@spw.wallonie.be', 'Jean-Claude ADAM ', '6690', 'VIELSALM', 'Place de Salm', '2 Bte 0A', 50.2846424, 5.9144125),
	(942, 'Arlon', 'cantonnement.nature.forets.florenville@spw.wallonie.be', 'Nathalie LEMOINE', '6820', 'FLORENVILLE', 'Rue de Neufchâteau', '1', 49.703824, 5.328533),
	(943, 'NEUFCHATEAU', 'cantonnement.nature.forets.neufchateau@spw.wallonie.be', 'Benjamin DE POTTER', '6840', 'NEUFCHATEAU', 'Chaussée d\'Arlon', '50/1', 49.8667, 5.3833),
	(951, 'NEUFCHATEAU', 'cantonnement.nature.forets.libin@spw.wallonie.be', 'Elise SPEYBROUCK ', '6890', 'LIBIN', 'rue de Villance', '90', 49.9772235, 5.239248),
	(952, 'MARCHE EN FAMENNE', 'cantonnement.nature.forets.nassogne@spw.wallonie.be', 'Stéphane ABRAS', '6953', 'FORRIERES', 'Place des Martyrs', '13', 50.1317662, 5.2777506),
	(953, 'NEUFCHATEAU', 'cantonnement.nature.forets.sainthubert@spw.wallonie.be', 'Benoît THIRIONET', '6870', 'SAINT-HUBERT', 'Avenue Nestor Martin', '10A', 50.0265332, 5.3654631);

-- Dumping structure for table plf.plf_spw_cc
CREATE TABLE IF NOT EXISTS `plf_spw_cc` (
  `cc_id` int NOT NULL AUTO_INCREMENT,
  `OBJECTID` smallint NOT NULL,
  `N_AGREMENT` smallint NOT NULL,
  `DENOMINATION` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ABREVIATION` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RUE_CC` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NUM_CC` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CP_CC` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOCALITE_CC` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NOM_PSDT` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PRENOM_PSDT` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NOM_SECR` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PRENOM_SECR` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUPERFICIE` decimal(8,2) DEFAULT '0.00',
  `LIEN_CARTE` varchar(510) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GEOM` mediumblob,
  PRIMARY KEY (`cc_id`) USING BTREE,
  KEY `cc_id` (`cc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table plf.plf_spw_cc: ~46 rows (approximately)

-- Dumping structure for table plf.plf_spw_cc_adresses
CREATE TABLE IF NOT EXISTS `plf_spw_cc_adresses` (
  `Code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_internet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  PRIMARY KEY (`Code`),
  UNIQUE KEY `UK_plf_cc_Code` (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table plf.plf_spw_cc_adresses: ~49 rows (approximately)
INSERT INTO `plf_spw_cc_adresses` (`Code`, `email`, `site_internet`, `logo`, `latitude`, `longitude`) VALUES
	('CC2O', 'xx.xx', NULL, NULL, NULL, NULL),
	('CC3P', NULL, NULL, NULL, NULL, NULL),
	('CC4R', NULL, NULL, NULL, NULL, NULL),
	('CCARCO', NULL, NULL, NULL, NULL, NULL),
	('CCAREL', NULL, NULL, NULL, NULL, NULL),
	('CCB', NULL, NULL, NULL, NULL, NULL),
	('CCBH', NULL, NULL, NULL, NULL, NULL),
	('CCBPME', NULL, NULL, NULL, NULL, NULL),
	('CCBS', NULL, NULL, NULL, NULL, NULL),
	('CCBSJ', NULL, NULL, NULL, NULL, NULL),
	('CCBT', NULL, NULL, NULL, NULL, NULL),
	('CCCC', NULL, NULL, NULL, NULL, NULL),
	('CCCL', NULL, NULL, NULL, NULL, NULL),
	('CCCW', NULL, NULL, NULL, NULL, NULL),
	('CCDYOR', NULL, NULL, NULL, NULL, NULL),
	('CCFA', NULL, NULL, NULL, NULL, NULL),
	('CCFARM', NULL, NULL, NULL, NULL, NULL),
	('CCFM', NULL, NULL, NULL, NULL, NULL),
	('CCG', NULL, NULL, NULL, NULL, NULL),
	('CCGBCCV', NULL, NULL, NULL, NULL, NULL),
	('CCH', NULL, NULL, NULL, NULL, NULL),
	('CCHA', NULL, NULL, NULL, NULL, NULL),
	('CCHERM', NULL, NULL, NULL, NULL, NULL),
	('CCHFE', NULL, NULL, NULL, NULL, NULL),
	('CCHL', NULL, NULL, NULL, NULL, NULL),
	('CCHS', NULL, NULL, NULL, NULL, NULL),
	('CCL', NULL, NULL, NULL, NULL, NULL),
	('CCLA', NULL, NULL, NULL, NULL, NULL),
	('CCN', NULL, NULL, NULL, NULL, NULL),
	('CCO', NULL, NULL, NULL, NULL, NULL),
	('CCOC', NULL, NULL, NULL, NULL, NULL),
	('CCPA', NULL, NULL, NULL, NULL, NULL),
	('CCPC', NULL, NULL, NULL, NULL, NULL),
	('CCPH', NULL, NULL, NULL, NULL, NULL),
	('CCPV', NULL, NULL, NULL, NULL, NULL),
	('CCRP', NULL, NULL, NULL, NULL, NULL),
	('CCS', NULL, NULL, NULL, NULL, NULL),
	('CCSAL', NULL, NULL, NULL, NULL, NULL),
	('CCSE', NULL, NULL, NULL, NULL, NULL),
	('CCSSS', NULL, NULL, NULL, NULL, NULL),
	('CCT', NULL, NULL, NULL, NULL, NULL),
	('CCVH', NULL, NULL, NULL, NULL, NULL),
	('CFCFC', NULL, NULL, NULL, NULL, NULL),
	('CFCS', NULL, NULL, NULL, NULL, NULL),
	('GICMHP', NULL, NULL, NULL, NULL, NULL),
	('UGCSH', NULL, NULL, NULL, NULL, NULL),
	('UGCTF', NULL, NULL, NULL, NULL, NULL),
	('UGCVE', NULL, NULL, NULL, NULL, NULL),
	('UGCVV', NULL, NULL, NULL, NULL, NULL);

-- Dumping structure for table plf.plf_spw_cc_concordance
CREATE TABLE IF NOT EXISTS `plf_spw_cc_concordance` (
  `cc_id` int DEFAULT NULL,
  `nugc` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table plf.plf_spw_cc_concordance: ~52 rows (approximately)
INSERT INTO `plf_spw_cc_concordance` (`cc_id`, `nugc`) VALUES
	(20, 1),
	(45, 2),
	(34, 3),
	(33, 4),
	(13, 5),
	(43, 6),
	(6, 7),
	(16, 8),
	(2, 9),
	(5, 10),
	(12, 11),
	(36, 12),
	(15, 13),
	(NULL, 14),
	(11, 15),
	(17, 16),
	(18, 17),
	(21, 18),
	(38, 21),
	(46, 22),
	(22, 23),
	(26, 24),
	(35, 25),
	(14, 26),
	(39, 27),
	(19, 28),
	(8, 29),
	(1, 30),
	(3, 31),
	(4, 32),
	(23, 33),
	(24, 34),
	(7, 35),
	(9, 36),
	(42, 38),
	(40, 40),
	(25, 41),
	(28, 42),
	(30, 43),
	(29, 44),
	(31, 45),
	(32, 46),
	(27, 47),
	(44, 48),
	(47, 51),
	(48, 53),
	(37, 54),
	(NULL, 55),
	(10, 56),
	(41, 57),
	(NULL, 58),
	(NULL, 99);

-- Dumping structure for table plf.plf_spw_chasses
CREATE TABLE IF NOT EXISTS `plf_spw_chasses` (
  `chasse_id` int NOT NULL AUTO_INCREMENT,
  `SAISON` smallint NOT NULL,
  `N_LOT` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NUM` int NOT NULL,
  `MODE_CHASSE` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DATE_CHASSE` date NOT NULL,
  `FERMETURE` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `KEYG` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`chasse_id`) USING BTREE,
  UNIQUE KEY `KEYG_DATE_NUM` (`KEYG`,`DATE_CHASSE`,`NUM`) USING BTREE,
  KEY `chasse_id` (`chasse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8633 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Dumping structure for table plf.plf_spw_territoires
CREATE TABLE IF NOT EXISTS `plf_spw_territoires` (
  `OBJECTID` int DEFAULT NULL,
  `SAISON` smallint NOT NULL,
  `N_LOT` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `KEYG` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SEQ` tinyint NOT NULL,
  `SERVICE` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NUGC` smallint DEFAULT NULL,
  `TITULAIRE_ADH_UGC` tinyint(1) NOT NULL,
  `DATE_MAJ` date DEFAULT NULL,
  PRIMARY KEY (`N_LOT`,`SAISON`,`SEQ`) USING BTREE,
  UNIQUE KEY `uk_Saison_lot_seq` (`SAISON`,`N_LOT`,`SEQ`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table plf.plf_spw_territoires: ~4,367 rows (approximately)

-- Dumping structure for view plf.view_spw_cantonnements
-- Creating temporary table to overcome VIEW dependency errors


-- plf.view_spw_cantonnements source

create view `view_spw_cantonnements` as
select
    `plf_spw_cantonnements`.`cantonnement_id` as `cantonnement_id`,
    `plf_spw_cantonnements`.`CAN` as `CAN`,
    `plf_spw_cantonnements`.`PREPOSE` as `PREPOSE`,
    `plf_spw_cantonnements`.`CANTON` as `CANTON`,
    `plf_spw_cantonnements`.`TEL_CAN` as `TEL_CAN`,
    `plf_spw_cantonnements`.`GEOM` as `GEOM`,
    `plf_spw_cantonnements_adresses`.`direction` as `direction`,
    `plf_spw_cantonnements_adresses`.`email` as `email`,
    `plf_spw_cantonnements_adresses`.`attache` as `attache`,
    `plf_spw_cantonnements_adresses`.`CP` as `CP`,
    `plf_spw_cantonnements_adresses`.`localite` as `localite`,
    `plf_spw_cantonnements_adresses`.`rue` as `rue`,
    `plf_spw_cantonnements_adresses`.`numero` as `numero`,
    `plf_spw_cantonnements_adresses`.`latitude` as `latitude`,
    `plf_spw_cantonnements_adresses`.`longitude` as `longitude`
from
    (`plf_spw_cantonnements`
left join `plf_spw_cantonnements_adresses` on
    ((`plf_spw_cantonnements`.`CAN` = `plf_spw_cantonnements_adresses`.`num_canton`)));

-- Dumping structure for view plf.view_spw_cc
-- Creating temporary table to overcome VIEW dependency errors



-- plf.view_spw_cc source

create view `view_spw_cc` as
select
    `plf_spw_cc`.`cc_id` as `cc_id`,
    `plf_spw_cc_concordance`.`nugc` as `nugc_CC`,
    `plf_spw_cc`.`N_AGREMENT` as `N_AGREMENT_CC`,
    `plf_spw_cc`.`DENOMINATION` as `DENOMINATION_CC`,
    `plf_spw_cc`.`ABREVIATION` as `ABREVIATION_CC`,
    `plf_spw_cc`.`RUE_CC` as `RUE_CC`,
    `plf_spw_cc`.`NUM_CC` as `NUM_CC`,
    `plf_spw_cc`.`CP_CC` as `CP_CC`,
    `plf_spw_cc`.`LOCALITE_CC` as `LOCALITE_CC`,
    `plf_spw_cc`.`NOM_PSDT` as `NOM_PSDT_CC`,
    `plf_spw_cc`.`PRENOM_PSDT` as `PRENOM_PSDT_CC`,
    `plf_spw_cc`.`NOM_SECR` as `NOM_SECR_CC`,
    `plf_spw_cc`.`PRENOM_SECR` as `PRENOM_SECR_CC`,
    `plf_spw_cc`.`SUPERFICIE` as `SUPERFICIE_CC`,
    `plf_spw_cc`.`LIEN_CARTE` as `LIEN_CARTE_CC`,
    `plf_spw_cc_adresses`.`email` as `email_CC`,
    `plf_spw_cc_adresses`.`site_internet` as `site_internet_CC`,
    `plf_spw_cc_adresses`.`logo` as `logo_CC`,
    `plf_spw_cc_adresses`.`latitude` as `latitude_CC`,
    `plf_spw_cc_adresses`.`longitude` as `longitude_CC`
from
    ((`plf_spw_cc`
left join `plf_spw_cc_adresses` on
    ((`plf_spw_cc_adresses`.`Code` = `plf_spw_cc`.`ABREVIATION`)))
join `plf_spw_cc_concordance` on
    ((`plf_spw_cc_concordance`.`cc_id` = `plf_spw_cc`.`cc_id`)));


create view `view_spw_chasses` as
select
    `plf_spw_chasses`.`SAISON` as `SAISON`,
    `plf_spw_chasses`.`N_LOT` as `N_LOT`,
    `plf_spw_chasses`.`NUM` as `NUM`,
    `plf_spw_chasses`.`MODE_CHASSE` as `MODE_CHASSE`,
    `plf_spw_chasses`.`chasse_id` as `chasse_id`,
    `plf_spw_chasses`.`DATE_CHASSE` as `DATE_CHASSE`,
    `plf_spw_chasses`.`FERMETURE` as `FERMETURE`,
    `plf_spw_chasses`.`KEYG` as `KEYG`,
    `plf_spw_territoires`.`SEQ` as `SEQ`,
    `plf_spw_territoires`.`NUGC` as `NUGC`
from
    (`plf_spw_chasses`
left join `plf_spw_territoires` on
    (((`plf_spw_chasses`.`SAISON` = `plf_spw_territoires`.`SAISON`)
        and (`plf_spw_chasses`.`N_LOT` = `plf_spw_territoires`.`N_LOT`))));


		

-- Dumping structure for view plf.view_spw_cantonnements
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_spw_cantonnements`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_spw_cantonnements` AS select `plf_spw_cantonnements`.`cantonnement_id` AS `cantonnement_id`,`plf_spw_cantonnements`.`CAN` AS `CAN`,`plf_spw_cantonnements`.`PREPOSE` AS `PREPOSE`,`plf_spw_cantonnements`.`CANTON` AS `CANTON`,`plf_spw_cantonnements`.`TEL_CAN` AS `TEL_CAN`,`plf_spw_cantonnements`.`GEOM` AS `GEOM`,`plf_spw_cantonnements_adresses`.`direction` AS `direction`,`plf_spw_cantonnements_adresses`.`email` AS `email`,`plf_spw_cantonnements_adresses`.`attache` AS `attache`,`plf_spw_cantonnements_adresses`.`CP` AS `CP`,`plf_spw_cantonnements_adresses`.`localite` AS `localite`,`plf_spw_cantonnements_adresses`.`rue` AS `rue`,`plf_spw_cantonnements_adresses`.`numero` AS `numero`,`plf_spw_cantonnements_adresses`.`latitude` AS `latitude`,`plf_spw_cantonnements_adresses`.`longitude` AS `longitude` from (`plf_spw_cantonnements` left join `plf_spw_cantonnements_adresses` on((`plf_spw_cantonnements`.`CAN` = `plf_spw_cantonnements_adresses`.`num_canton`)));

-- Dumping structure for view plf.view_spw_cc
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_spw_cc`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_spw_cc` AS select `plf_spw_cc`.`cc_id` AS `cc_id`,`plf_spw_cc_concordance`.`nugc` AS `nugc_CC`,`plf_spw_cc`.`N_AGREMENT` AS `N_AGREMENT_CC`,`plf_spw_cc`.`DENOMINATION` AS `DENOMINATION_CC`,`plf_spw_cc`.`ABREVIATION` AS `ABREVIATION_CC`,`plf_spw_cc`.`RUE_CC` AS `RUE_CC`,`plf_spw_cc`.`NUM_CC` AS `NUM_CC`,`plf_spw_cc`.`CP_CC` AS `CP_CC`,`plf_spw_cc`.`LOCALITE_CC` AS `LOCALITE_CC`,`plf_spw_cc`.`NOM_PSDT` AS `NOM_PSDT_CC`,`plf_spw_cc`.`PRENOM_PSDT` AS `PRENOM_PSDT_CC`,`plf_spw_cc`.`NOM_SECR` AS `NOM_SECR_CC`,`plf_spw_cc`.`PRENOM_SECR` AS `PRENOM_SECR_CC`,`plf_spw_cc`.`SUPERFICIE` AS `SUPERFICIE_CC`,`plf_spw_cc`.`LIEN_CARTE` AS `LIEN_CARTE_CC`,`plf_spw_cc_adresses`.`email` AS `email_CC`,`plf_spw_cc_adresses`.`site_internet` AS `site_internet_CC`,`plf_spw_cc_adresses`.`logo` AS `logo_CC`,`plf_spw_cc_adresses`.`latitude` AS `latitude_CC`,`plf_spw_cc_adresses`.`longitude` AS `longitude_CC` from ((`plf_spw_cc` left join `plf_spw_cc_adresses` on((`plf_spw_cc_adresses`.`Code` = `plf_spw_cc`.`ABREVIATION`))) join `plf_spw_cc_concordance` on((`plf_spw_cc_concordance`.`cc_id` = `plf_spw_cc`.`cc_id`)));

-- Dumping structure for view plf.view_spw_chasses
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_spw_chasses`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_spw_chasses` AS select `plf_spw_chasses`.`SAISON` AS `SAISON`,`plf_spw_chasses`.`N_LOT` AS `N_LOT`,`plf_spw_chasses`.`NUM` AS `NUM`,`plf_spw_chasses`.`MODE_CHASSE` AS `MODE_CHASSE`,`plf_spw_chasses`.`chasse_id` AS `chasse_id`,`plf_spw_chasses`.`DATE_CHASSE` AS `DATE_CHASSE`,`plf_spw_chasses`.`FERMETURE` AS `FERMETURE`,`plf_spw_chasses`.`KEYG` AS `KEYG`,`plf_spw_territoires`.`SEQ` AS `SEQ`,`plf_spw_territoires`.`NUGC` AS `NUGC` from (`plf_spw_chasses` left join `plf_spw_territoires` on(((`plf_spw_chasses`.`SAISON` = `plf_spw_territoires`.`SAISON`) and (`plf_spw_chasses`.`N_LOT` = `plf_spw_territoires`.`N_LOT`))));

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
