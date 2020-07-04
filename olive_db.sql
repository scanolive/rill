-- MySQL dump 10.13  Distrib 5.5.60, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: olive
-- ------------------------------------------------------
-- Server version	5.5.60-0+deb8u1

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
-- Table structure for table `alarms`
--

DROP TABLE IF EXISTS `alarms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alarms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Msg` char(255) NOT NULL,
  `Type` char(20) NOT NULL,
  `IsAlarm` int(10) DEFAULT '1',
  `IsSend` int(10) NOT NULL DEFAULT '0',
  `IsBeOk` int(10) NOT NULL DEFAULT '0',
  `Ipid` int(10) DEFAULT NULL,
  `Gid` int(10) NOT NULL DEFAULT '0',
  `Note` char(120) DEFAULT NULL,
  `CreateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UpdateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `SendTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alarms`
--

LOCK TABLES `alarms` WRITE;
/*!40000 ALTER TABLE `alarms` DISABLE KEYS */;
/*!40000 ALTER TABLE `alarms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bg_result`
--

DROP TABLE IF EXISTS `bg_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bg_result` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Ip` char(40) DEFAULT NULL,
  `Stat` int(10) DEFAULT '0',
  `ShellFile` char(40) DEFAULT NULL,
  `CmdStr` char(120) DEFAULT NULL,
  `MarkId` int(12) NOT NULL,
  `OutStr` text,
  `ErrStr` text,
  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EndTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bg_result`
--

LOCK TABLES `bg_result` WRITE;
/*!40000 ALTER TABLE `bg_result` DISABLE KEYS */;
/*!40000 ALTER TABLE `bg_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `define_cmd`
--

DROP TABLE IF EXISTS `define_cmd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `define_cmd` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `CmdName` char(32) NOT NULL,
  `ShellFileId` int(10) DEFAULT NULL,
  `CmdStr` char(240) DEFAULT NULL,
  `Note` char(240) DEFAULT NULL,
  `AddTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `define_cmd`
--

LOCK TABLES `define_cmd` WRITE;
/*!40000 ALTER TABLE `define_cmd` DISABLE KEYS */;
/*!40000 ALTER TABLE `define_cmd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delipid`
--

DROP TABLE IF EXISTS `delipid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delipid` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ipid` int(10) NOT NULL,
  `DelMon` int(10) NOT NULL DEFAULT '0',
  `DelTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delipid`
--

LOCK TABLES `delipid` WRITE;
/*!40000 ALTER TABLE `delipid` DISABLE KEYS */;
/*!40000 ALTER TABLE `delipid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devinfo`
--

DROP TABLE IF EXISTS `devinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devinfo` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `DevName` char(50) DEFAULT NULL,
  `Ipbak` char(40) DEFAULT NULL,
  `Ipid` int(20) NOT NULL,
  `SN` char(40) DEFAULT NULL,
  `OS` char(60) DEFAULT NULL,
  `Ips` char(60) DEFAULT NULL,
  `Release_Date` char(60) DEFAULT NULL,
  `Model` char(60) DEFAULT NULL,
  `Kernel` char(60) DEFAULT NULL,
  `Disk` char(60) DEFAULT NULL,
  `Memory` char(20) DEFAULT NULL,
  `Cpu_Model` char(60) DEFAULT NULL,
  `Cpu_Pro` int(10) DEFAULT NULL,
  `Cpu_Num` int(10) DEFAULT NULL,
  `Vendor` char(60) DEFAULT NULL,
  `HostName` char(40) DEFAULT NULL,
  `Uptime` int(6) DEFAULT NULL,
  `Place` varchar(100) DEFAULT NULL,
  `Idc` char(40) DEFAULT NULL,
  `Capex_Price` double DEFAULT NULL,
  `Opex_Price` double DEFAULT NULL,
  `Typeid` int(20) DEFAULT NULL,
  `Userid` int(20) DEFAULT NULL,
  `DevDetail` varchar(200) DEFAULT NULL,
  `Description` varchar(500) DEFAULT NULL,
  `CreateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devinfo`
--

LOCK TABLES `devinfo` WRITE;
/*!40000 ALTER TABLE `devinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `devinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `err_logs`
--

DROP TABLE IF EXISTS `err_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `err_logs` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `LogType` char(24) NOT NULL,
  `LogContent` text,
  `AddTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `err_logs`
--

LOCK TABLES `err_logs` WRITE;
/*!40000 ALTER TABLE `err_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `err_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Content` text,
  `Uid` int(10) DEFAULT NULL,
  `TypeLevel` int(10) DEFAULT NULL,
  `CreateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `info_day`
--

DROP TABLE IF EXISTS `info_day`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info_day` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Ipid` int(10) NOT NULL,
  `Data_txt` text NOT NULL,
  `Addtime` char(24) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info_day`
--

LOCK TABLES `info_day` WRITE;
/*!40000 ALTER TABLE `info_day` DISABLE KEYS */;
/*!40000 ALTER TABLE `info_day` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipgroup`
--

DROP TABLE IF EXISTS `ipgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipgroup` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `GroupName` char(40) NOT NULL,
  `Description` varchar(500) DEFAULT NULL,
  `AddTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipgroup`
--

LOCK TABLES `ipgroup` WRITE;
/*!40000 ALTER TABLE `ipgroup` DISABLE KEYS */;
INSERT INTO `ipgroup` VALUES (1,'NoGroup','Default Group','2019-07-24 05:08:56');
/*!40000 ALTER TABLE `ipgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipinfo`
--

DROP TABLE IF EXISTS `ipinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipinfo` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `Ip` varchar(50) NOT NULL,
  `GroupId` int(10) DEFAULT NULL,
  `IsAlive` varchar(10) DEFAULT NULL,
  `Status` int(4) DEFAULT NULL,
  `ClientStatus` int(10) NOT NULL DEFAULT '1',
  `LoadLevel` int(10) DEFAULT '0',
  `DiskLevel` int(10) DEFAULT '0',
  `NetworkLevel` int(10) DEFAULT '0',
  `LoginLevel` int(10) DEFAULT '0',
  `ProcessLevel` int(10) DEFAULT '0',
  `ConnectLevel` int(10) DEFAULT '0',
  `Alarms_Ids` char(100) DEFAULT NULL,
  `Cpu_Pro` int(10) DEFAULT '1',
  `Enable` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Ip` (`Ip`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipinfo`
--

LOCK TABLES `ipinfo` WRITE;
/*!40000 ALTER TABLE `ipinfo` DISABLE KEYS */;
INSERT INTO `ipinfo` VALUES (1,'0.0.0.0',0,NULL,NULL,1,80,85,56000,20,200,3000,NULL,1,1);
/*!40000 ALTER TABLE `ipinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_config`
--

DROP TABLE IF EXISTS `mail_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` char(40) NOT NULL,
  `Host` char(50) NOT NULL,
  `Port` int(10) NOT NULL,
  `Passwd` char(40) NOT NULL,
  `Address` char(40) NOT NULL,
  `SendName` char(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_config`
--

LOCK TABLES `mail_config` WRITE;
/*!40000 ALTER TABLE `mail_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monitor`
--

DROP TABLE IF EXISTS `monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monitor` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `Ipid` int(20) NOT NULL,
  `MonTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `MonText` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ipid` (`Ipid`,`MonTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitor`
--

LOCK TABLES `monitor` WRITE;
/*!40000 ALTER TABLE `monitor` DISABLE KEYS */;
/*!40000 ALTER TABLE `monitor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monweb`
--

DROP TABLE IF EXISTS `monweb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monweb` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `MonName` char(80) NOT NULL,
  `Gid` int(10) NOT NULL,
  `Enable` int(10) NOT NULL DEFAULT '1',
  `AddUid` int(10) NOT NULL,
  `MonUrl` char(240) NOT NULL,
  `RstCode` int(10) DEFAULT NULL,
  `CheckTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monweb`
--

LOCK TABLES `monweb` WRITE;
/*!40000 ALTER TABLE `monweb` DISABLE KEYS */;
/*!40000 ALTER TABLE `monweb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ports`
--

DROP TABLE IF EXISTS `ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ports` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Ipid` int(10) NOT NULL,
  `Port` int(20) NOT NULL,
  `Service` char(40) DEFAULT NULL,
  `IsMon` int(10) NOT NULL DEFAULT '0',
  `Status` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ports`
--

LOCK TABLES `ports` WRITE;
/*!40000 ALTER TABLE `ports` DISABLE KEYS */;
/*!40000 ALTER TABLE `ports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portstat`
--

DROP TABLE IF EXISTS `portstat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portstat` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Ipid` int(10) NOT NULL,
  `ErrPort` char(80) DEFAULT NULL,
  `ErrNum` int(10) NOT NULL DEFAULT '0',
  `UpdateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portstat`
--

LOCK TABLES `portstat` WRITE;
/*!40000 ALTER TABLE `portstat` DISABLE KEYS */;
/*!40000 ALTER TABLE `portstat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sys_config`
--

DROP TABLE IF EXISTS `sys_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Uid` char(24) DEFAULT 'NoUid',
  `CfgType` char(24) NOT NULL,
  `KeyName` char(30) NOT NULL,
  `KeyValue` char(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys_config`
--

LOCK TABLES `sys_config` WRITE;
/*!40000 ALTER TABLE `sys_config` DISABLE KEYS */;
INSERT INTO `sys_config` VALUES (1,'NoUid','public','ctrl_center_enable','NO'),(2,'NoUid','public','ssh_enable','NO'),(3,'NoUid','public','bg_result_pagesize','24'),(4,'NoUid','public','err_logs_pagesize','24'),(5,'NoUid','public','history_pagesize','24'),(6,'NoUid','public','devinfo_pagesize','24'),(7,'NoUid','public','index_pagesize','24'),(8,'NoUid','public','monstate_pagesize','24'),(9,'NoUid','public','monweb_pagesize','24'),(10,'NoUid','public','batchdo_pagesize','24'),(11,'NoUid','public','php_timeout','10'),(12,'NoUid','public','upfile_dir','upload'),(13,'NoUid','public','system_name','RILL运维监控平台'),(14,'NoUid','public','max_upfile_size','20'),(15,'NoUid','public','norun_cmd','init,shutdown,rm'),(16,'NoUid','public','python_server_ip','127.0.0.1'),(17,'NoUid','public','python_server_port','33331'),(18,'NoUid','public','python_oct_cmd_pre','OLIVE_CTRL_CENTER_CMD'),(19,'NoUid','public','python_end_cmd_str','OLIVE_EOC'),(20,'NoUid','public','python_end_str','OLIVE_EOS'),(21,'NoUid','public','python_sep_str','@!@'),(22,'NoUid','public','python_sep_str_se','R!I@L#L'),(23,'NoUid','public','def_mon_ports','80,21,22'),(24,'NoUid','public','monweb_interval','60');
/*!40000 ALTER TABLE `sys_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upfile`
--

DROP TABLE IF EXISTS `upfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upfile` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `FileName` char(80) NOT NULL,
  `SaveName` char(120) DEFAULT NULL,
  `UserId` int(8) DEFAULT NULL,
  `FileDesc` char(240) DEFAULT NULL,
  `FileSize` int(12) NOT NULL,
  `Enable` int(4) NOT NULL DEFAULT '1',
  `AddTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upfile`
--

LOCK TABLES `upfile` WRITE;
/*!40000 ALTER TABLE `upfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `upfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userofgroup`
--

DROP TABLE IF EXISTS `userofgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userofgroup` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Gid` int(10) NOT NULL,
  `Uid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userofgroup`
--

LOCK TABLES `userofgroup` WRITE;
/*!40000 ALTER TABLE `userofgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `userofgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `UserName` char(40) NOT NULL,
  `UserPasswd` char(40) DEFAULT NULL,
  `UserType` char(20) NOT NULL,
  `UserMail` char(40) NOT NULL,
  `UserMobile` char(40) NOT NULL,
  `LoginNum` int(10) DEFAULT '0',
  `IsOnline` int(10) NOT NULL DEFAULT '0',
  `NoticeLevel` int(10) NOT NULL DEFAULT '3',
  `CreateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LastLoginTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DutyDate` char(200) DEFAULT NULL,
  `DutyTime` char(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-25 16:03:30
