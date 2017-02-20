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

CREATE DATABASE IF NOT EXISTS budget_system;
  
USE budget_system; 

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `account_pk` int(11) NOT NULL AUTO_INCREMENT,
  `account_number` varchar(255) NOT NULL,
  `account_type_pk` int(11) NOT NULL,
  `account_name` varchar(45) NOT NULL,
  `user_group` int(11) NOT NULL,
  PRIMARY KEY (`account_pk`),
  UNIQUE KEY `accountid_UNIQUE` (`account_number`)
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accounttype`
--

DROP TABLE IF EXISTS `accounttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounttype` (
  `account_type_pk` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(45) NOT NULL,
  `account_type` varchar(45) NOT NULL,
  `user_group` int(11) NOT NULL,
  PRIMARY KEY (`account_type_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `balances`
--

DROP TABLE IF EXISTS `balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `balances` (
  `balance_pk` int(11) NOT NULL AUTO_INCREMENT,
  `account_pk` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `balance` decimal(18,4) NOT NULL,
  PRIMARY KEY (`balance_pk`),
  UNIQUE KEY `balanceunique` (`account_pk`,`month`,`year`)
) ENGINE=InnoDB AUTO_INCREMENT=2298 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_pk` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(45) NOT NULL,
  `user_group` int(11) NOT NULL,
  PRIMARY KEY (`category_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentmethod`
--

DROP TABLE IF EXISTS `paymentmethod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentmethod` (
  `payment_method_pk` int(11) NOT NULL AUTO_INCREMENT,
  `payment_name` varchar(45) NOT NULL,
  `user_group` int(11) NOT NULL,
  `account_type_pk` int(11) NOT NULL,
  PRIMARY KEY (`payment_method_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasers`
--

DROP TABLE IF EXISTS `purchasers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasers` (
  `purchaser_pk` int(11) NOT NULL AUTO_INCREMENT,
  `purchaser_name` varchar(45) NOT NULL,
  `user_group` int(11) NOT NULL,
  PRIMARY KEY (`purchaser_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relatedtransactions`
--

DROP TABLE IF EXISTS `relatedtransactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relatedtransactions` (
  `related_transaction_pk` int(11) NOT NULL AUTO_INCREMENT,
  `reoccurring_transaction_pk` int(11) NOT NULL,
  `transaction_pk` int(11) NOT NULL,
  PRIMARY KEY (`related_transaction_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=5012 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reoccurringtransactions`
--

DROP TABLE IF EXISTS `reoccurringtransactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reoccurringtransactions` (
  `reoccurring_transaction_pk` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type_pk` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`reoccurring_transaction_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactionhistory`
--

DROP TABLE IF EXISTS `transactionhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactionhistory` (
  `transaction_history_pk` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_pk` int(11) NOT NULL,
  `amount` decimal(18,4) NOT NULL,
  `purchaser_pk` int(11) NOT NULL,
  `payment_method_pk` int(11) NOT NULL,
  `last_modified_date` date NOT NULL,
  `from_account_pk` int(11) NOT NULL,
  `to_account_pk` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `posted` int(11) NOT NULL,
  `posted_date` date NOT NULL,
  `category_pk` int(11) NOT NULL,
  `description` text NOT NULL,
  `user_group` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`transaction_history_pk`),
  KEY `posted` (`posted`),
  KEY `active` (`active`),
  KEY `accounts` (`from_account_pk`,`to_account_pk`),
  KEY `category` (`category_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=8002 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `transaction_pk` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type_pk` int(11) NOT NULL,
  `amount` decimal(18,4) NOT NULL,
  `purchaser_pk` int(11) NOT NULL,
  `payment_method_pk` int(11) NOT NULL,
  `original_date` date NOT NULL,
  `modified_date` date NOT NULL,
  `modified_year` int(11) NOT NULL,
  `modified_y_day` int(11) NOT NULL,
  `from_account_pk` int(11) NOT NULL,
  `to_account_pk` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `posted` int(11) NOT NULL,
  `posted_date` date NOT NULL,
  `category_pk` int(11) NOT NULL,
  `description` text NOT NULL,
  `user_group` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`transaction_pk`),
  KEY `modifieddate` (`modified_year`,`modified_y_day`),
  KEY `posted` (`posted`),
  KEY `active` (`active`),
  KEY `accounts` (`from_account_pk`,`to_account_pk`),
  KEY `category` (`category_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=8072 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactiontype`
--

DROP TABLE IF EXISTS `transactiontype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactiontype` (
  `transaction_type_pk` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(45) NOT NULL,
  `ndays` int(11) NOT NULL,
  `operator` int(11) NOT NULL,
  PRIMARY KEY (`transaction_type_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transfermethod`
--

DROP TABLE IF EXISTS `transfermethod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfermethod` (
  `transfer_method_pk` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_method_name` varchar(45) NOT NULL,
  `user_group` varchar(45) NOT NULL,
  PRIMARY KEY (`transfer_method_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usertable`
--

DROP TABLE IF EXISTS `usertable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertable` (
  `userpk` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup` int(11) NOT NULL,
  `userid` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  `createddate` datetime NOT NULL DEFAULT '2014-01-01 00:00:00',
  `lastmodifieddate` datetime NOT NULL DEFAULT '2014-01-01 00:00:00',
  `emailaddress` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `pin` int(11) DEFAULT NULL,
  PRIMARY KEY (`userpk`),
  UNIQUE KEY `userid_UNIQUE` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usertablebs`
--

DROP TABLE IF EXISTS `usertablebs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertablebs` (
  `userpk` int(11) NOT NULL,
  `username` varchar(45) NOT NULL,
  `usergroup` int(11) NOT NULL,
  PRIMARY KEY (`userpk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-02-20 11:54:12
