-- MySQL dump 10.13  Distrib 5.5.32, for Win32 (x86)
--
-- Host: localhost    Database: caiji
-- ------------------------------------------------------
-- Server version	5.5.32-log

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
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '父id',
  `name` varchar(255) DEFAULT '' COMMENT '科室名称',
  `collect_url` varchar(255) DEFAULT '' COMMENT '采集url',
  PRIMARY KEY (`id`),
  KEY `departments_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disease`
--

DROP TABLE IF EXISTS `disease`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disease` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `departments_id` int(10) unsigned DEFAULT '0' COMMENT '科室id',
  `part_id` int(10) unsigned DEFAULT '0' COMMENT '部位id',
  `disease_name` varchar(255) DEFAULT '' COMMENT '疾病名称',
  `introduction` text COMMENT '简介',
  `is_yibao` tinyint(4) DEFAULT '0' COMMENT '医保疾病：0 - 否， 1 - 是',
  `ratio` varchar(255) DEFAULT '' COMMENT '患病比例',
  `susceptible` varchar(255) DEFAULT '' COMMENT '易感人群',
  `infection_way` varchar(255) DEFAULT '' COMMENT '传染方式',
  `clinic_department` varchar(255) DEFAULT '' COMMENT '就诊科室',
  `therapy_method` varchar(255) DEFAULT '' COMMENT '治疗方式',
  `treatment_cycle` varchar(255) DEFAULT '' COMMENT '治疗周期',
  `cure_rate` varchar(255) DEFAULT '' COMMENT '治愈率',
  `drug_ids` varchar(255) DEFAULT '' COMMENT '常用药品，保存药品的id以逗号隔开',
  `warm_prompt` varchar(255) DEFAULT '' COMMENT '温馨提示',
  `expense` varchar(255) DEFAULT '' COMMENT '治疗费用',
  `cause` text COMMENT '病因',
  `prevent` text COMMENT '预防',
  `neopathy_ids` varchar(255) DEFAULT '' COMMENT '存并发症状id值，多个id以逗号分隔',
  `neopathy_names` varchar(255) DEFAULT '' COMMENT '冗余字段，由于查找疾病开始时找疾病id容易无限递归，先保存疾病的名称，等采集完写个脚本把疾病名称改成id',
  `neopathy` text COMMENT '并发症',
  `tushuojibing` text COMMENT '图片集以json存放',
  `symptom_ids` varchar(255) DEFAULT '' COMMENT '存储症状id值，过个id以逗号分隔',
  `symptom` text COMMENT '症状',
  `inspect_ids` varchar(255) DEFAULT '' COMMENT '存储检查id值，过个id以逗号分隔',
  `inspect` text COMMENT '检查',
  `diagnosis` text COMMENT '诊断鉴别',
  `treat` text COMMENT '治疗',
  `nursing` text COMMENT '护理',
  `collect_url` varchar(255) DEFAULT '' COMMENT '采集url',
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_name` (`disease_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5713 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disease_symptom`
--

DROP TABLE IF EXISTS `disease_symptom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disease_symptom` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `disease_id` int(10) unsigned DEFAULT '0' COMMENT '疾病id',
  `symptom_id` int(10) unsigned DEFAULT '0' COMMENT '症状id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37464 DEFAULT CHARSET=utf8 COMMENT='疾病症状关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `drug`
--

DROP TABLE IF EXISTS `drug`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drug` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `full_name` varchar(100) DEFAULT '' COMMENT '全称',
  `drug_name` varchar(100) DEFAULT '' COMMENT '药品名字',
  `recipe` varchar(20) DEFAULT '' COMMENT '药品处方',
  `drug_type` varchar(20) DEFAULT '' COMMENT '中西药',
  `medicare_type` varchar(20) DEFAULT '' COMMENT '医保类型',
  `production_place` varchar(20) DEFAULT '' COMMENT '产地：国产、进口',
  `approval_number` varchar(50) DEFAULT '' COMMENT '批准文号',
  `production_enterprise` varchar(50) DEFAULT '' COMMENT '生产企业',
  `purpose` text COMMENT '功能主治',
  `bases` varchar(1000) DEFAULT '' COMMENT '主要成分',
  `packing` varchar(255) DEFAULT '' COMMENT '包装规格',
  `usage` text COMMENT '用法用量',
  `untoward_effect` text COMMENT '不良反应',
  `need_attention` text COMMENT '注意事项',
  `taboo` text COMMENT '禁忌',
  `women_use` text COMMENT '孕妇用药',
  `children_use` text COMMENT '儿童用药',
  `older_user` text COMMENT '老年用药',
  `drug_interactions` text COMMENT '药物相互作用',
  `drug_action` text COMMENT '药理作用',
  `collect_url` varchar(255) DEFAULT '' COMMENT '采集url',
  PRIMARY KEY (`id`),
  UNIQUE KEY `full_name` (`full_name`),
  KEY `drug_name` (`drug_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5970 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inspect`
--

DROP TABLE IF EXISTS `inspect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `inspect_name` varchar(255) DEFAULT '' COMMENT '检查名称',
  `reference_price` varchar(255) DEFAULT '' COMMENT '参考价',
  `introduce` varchar(1000) DEFAULT '' COMMENT '介绍',
  `normal_value` varchar(1000) DEFAULT '' COMMENT '正常值',
  `clinical_value` varchar(1000) DEFAULT '' COMMENT '临床意义',
  `need_attention` varchar(1000) DEFAULT '' COMMENT '注意事项',
  `checking_process` text COMMENT '检查过程',
  `collect_url` varchar(255) DEFAULT '' COMMENT '采集url',
  PRIMARY KEY (`id`),
  UNIQUE KEY `inspect_name` (`inspect_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3564 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `part`
--

DROP TABLE IF EXISTS `part`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `part` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '父id',
  `name` varchar(255) DEFAULT '' COMMENT '部位名称',
  `collect_url` varchar(255) DEFAULT '' COMMENT '采集url',
  PRIMARY KEY (`id`),
  KEY `part_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `symptom`
--

DROP TABLE IF EXISTS `symptom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `symptom` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `symptom_name` varchar(255) DEFAULT '' COMMENT '症状名称',
  `introduce` text COMMENT '介绍',
  `pathogen` text COMMENT '病因',
  `prevent` text COMMENT '预防',
  `inspect_ids` varchar(255) DEFAULT '' COMMENT '检查id，多个id以逗号分隔',
  `inspect` text COMMENT '检查描述',
  `antidiastole` text COMMENT '鉴别诊断',
  `drug_ids` varchar(255) DEFAULT '' COMMENT '药品id',
  `collect_url` varchar(255) DEFAULT '' COMMENT '采集url',
  PRIMARY KEY (`id`),
  UNIQUE KEY `symptom_name` (`symptom_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6309 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-28  9:07:38
