-- MySQL dump 10.13  Distrib 5.7.16, for Linux (x86_64)
--
-- Host: localhost    Database: responses
-- ------------------------------------------------------
-- Server version	5.7.13-0ubuntu0.16.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bar_responses`
--

DROP TABLE IF EXISTS `bar_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bar_responses` (
  `RID` int(11) NOT NULL,
  `offby` int(11) DEFAULT '0',
  `category` varchar(100) DEFAULT NULL,
  `category_index` int(11) NOT NULL DEFAULT '0',
  `response` int(11) NOT NULL,
  `phase` int(11) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bar_responses`
--

LOCK TABLES `bar_responses` WRITE;
/*!40000 ALTER TABLE `bar_responses` DISABLE KEYS */;
INSERT INTO `bar_responses` VALUES (3,-1,'$135 - $150',0,0,0,0),(3,-3,'$151 - $165',1,0,0,0),(3,-8,'$166 - 180',2,0,0,0),(3,-4,'$181 - $195',3,0,0,0),(3,-3,'$196 - $210',4,0,0,0),(3,-1,'$211 - $225',5,0,0,0),(3,0,'$226 - $240',6,0,0,0),(4,-1,'$135 - $150',0,0,0,0),(4,-3,'$151 - $165',1,0,0,0),(4,-8,'$166 - 180',2,0,0,0),(4,-4,'$181 - $195',3,0,0,0),(4,-3,'$196 - $210',4,0,0,0),(4,-1,'$211 - $225',5,0,0,0),(4,0,'$226 - $240',6,0,0,0),(5,-1,'$135 - $150',0,0,0,0),(5,-3,'$151 - $165',1,0,0,0),(5,-8,'$166 - 180',2,0,0,0),(5,-4,'$181 - $195',3,0,0,0),(5,-3,'$196 - $210',4,0,0,0),(5,-1,'$211 - $225',5,0,0,0),(5,0,'$226 - $240',6,0,0,0),(6,-1,'$135 - $150',0,0,0,0),(6,-3,'$151 - $165',1,0,0,0),(6,-8,'$166 - 180',2,0,0,0),(6,-4,'$181 - $195',3,0,0,0),(6,-3,'$196 - $210',4,0,0,0),(6,-1,'$211 - $225',5,0,0,0),(6,0,'$226 - $240',6,0,0,0),(7,-1,'$135 - $150',0,0,0,0),(7,-3,'$151 - $165',1,0,0,0),(7,-8,'$166 - 180',2,0,0,0),(7,-4,'$181 - $195',3,0,0,0),(7,-3,'$196 - $210',4,0,0,0),(7,-1,'$211 - $225',5,0,0,0),(7,0,'$226 - $240',6,0,0,0),(8,-1,'$135 - $150',0,0,0,0),(8,-3,'$151 - $165',1,0,0,0),(8,-8,'$166 - 180',2,0,0,0),(8,-4,'$181 - $195',3,0,0,0),(8,-3,'$196 - $210',4,0,0,0),(8,-1,'$211 - $225',5,0,0,0),(8,0,'$226 - $240',6,0,0,0),(9,-1,'$135 - $150',0,0,0,0),(9,-3,'$151 - $165',1,0,0,0),(9,-8,'$166 - 180',2,0,0,0),(9,-4,'$181 - $195',3,0,0,0),(9,-3,'$196 - $210',4,0,0,0),(9,-1,'$211 - $225',5,0,0,0),(9,0,'$226 - $240',6,0,0,0);
/*!40000 ALTER TABLE `bar_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `responses` (
  `RID` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `total_points` int(11) DEFAULT '0',
  PRIMARY KEY (`RID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `responses`
--

LOCK TABLES `responses` WRITE;
/*!40000 ALTER TABLE `responses` DISABLE KEYS */;
INSERT INTO `responses` VALUES (1,'2016-12-11 21:04:37','2016-12-11 21:05:00',0),(2,'2016-12-11 21:05:11','2016-12-11 21:05:35',2),(3,'2016-12-11 21:06:00','2016-12-11 21:06:25',2),(4,'2016-12-11 21:07:15','2016-12-11 21:07:41',2),(5,'2016-12-11 21:10:07','2016-12-11 21:10:34',2),(6,'2016-12-11 21:13:12','2016-12-11 21:13:38',2),(7,'2016-12-11 21:23:23','2016-12-11 21:23:48',0),(8,'2016-12-11 21:24:17','2016-12-11 21:24:44',2),(9,'2016-12-12 07:48:11','2016-12-12 07:48:38',2);
/*!40000 ALTER TABLE `responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_responses`
--

DROP TABLE IF EXISTS `test_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_responses` (
  `RID` int(11) NOT NULL,
  `sequence` int(11) DEFAULT '0',
  `phase` int(11) DEFAULT '0',
  `response` int(11) NOT NULL,
  `place` int(11) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_responses`
--

LOCK TABLES `test_responses` WRITE;
/*!40000 ALTER TABLE `test_responses` DISABLE KEYS */;
INSERT INTO `test_responses` VALUES (6,0,0,168,3,0),(6,1,0,213,9,0),(6,2,0,158,0,2),(7,0,0,191,7,0),(7,1,0,183,3,0),(7,2,0,192,5,0),(8,0,0,168,3,0),(8,1,0,213,9,0),(8,2,0,158,0,2),(9,0,0,168,3,0),(9,1,0,213,9,0),(9,2,0,158,0,2);
/*!40000 ALTER TABLE `test_responses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-12-13 11:16:23
