-- MariaDB dump 10.19  Distrib 10.4.18-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: intern-db
-- ------------------------------------------------------
-- Server version	10.4.18-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `order_status`
--

DROP TABLE IF EXISTS `order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status`
--

LOCK TABLES `order_status` WRITE;
/*!40000 ALTER TABLE `order_status` DISABLE KEYS */;
INSERT INTO `order_status` VALUES (3,'delivered'),(1,'processing'),(2,'shipped');
/*!40000 ALTER TABLE `order_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_sku` varchar(20) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `status_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_to_user` (`user_email`),
  KEY `fk_to_product` (`product_sku`),
  KEY `fk_to_order_status` (`status_id`),
  CONSTRAINT `fk_to_order_status` FOREIGN KEY (`status_id`) REFERENCES `order_status` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_to_product` FOREIGN KEY (`product_sku`) REFERENCES `products` (`sku`) ON UPDATE CASCADE,
  CONSTRAINT `fk_to_user` FOREIGN KEY (`user_email`) REFERENCES `user` (`email`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (12,'sku4','user2@abc.com',2),(13,'sku3','user2@abc.com',3),(14,'sku5','user2@abc.com',2),(15,'sku3','user2@abc.com',1),(16,'sku3','user2@abc.com',3),(17,'sku3','user2@abc.com',1),(18,'sku4','user2@abc.com',1),(19,'sku4','user2@abc.com',1),(20,'sku4','user2@abc.com',1),(21,'sku4','user2@abc.com',1),(22,'sku5','user2@abc.com',1),(23,'sku3','user2@abc.com',1),(24,'sku3','as@gmail.com',1),(25,'sku8','as@gmail.com',1);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `name` varchar(50) NOT NULL,
  `sku` varchar(20) NOT NULL,
  `description` varchar(500) NOT NULL,
  `category` varchar(20) NOT NULL,
  `price` decimal(10,3) NOT NULL,
  `imageUrl` varchar(100) NOT NULL,
  PRIMARY KEY (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES ('product3','sku3','new new description3 description3 description3 description3 description3 description3 description3 description3 description3 description3new new new description3 description3 description3 description3 description3 description3 description3 description3 description3 description3 description3new new new description3 description3 description3 description3 description3 description3','cat3',3.000,'http://intern.local/images/16198490751074894859.jpg'),('name_update','sku4','description_updated4','cat4',9.330,'http://intern.local/images/1619331442235544098.PNG'),('product5','sku5','description5','cat3_new',33.998,'http://intern.local/images/16196079981477929693.png'),('product7','sku7','description 7','cat7',7.000,'http://intern.local/images/1619679683206087938.PNG'),('product8','sku8','description 7','cat8',23.000,'http://intern.local/images/1619849270225354443.png');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('Abdulla Al Mamun','as@gmail.com','$2y$10$alUw/oRJjkojxWSD1RkRR.3otKfp6QvFtuWdIeaPfV6Gizazt27sW','customer'),('user1','user1@abc.com','$2y$10$iDWZhrWdZnFuIiK4.qP66Ox9/wg6hjGNZTs5.lzWXLi7ZvwSbTNbq','admin'),('user2','user2@abc.com','$2y$10$8J.yh83qQEdoiw/rm0F0guqiro5kAj.qOK6vNUYlwM2KmFl9rrmtS','customer'),('user3','user3@abc.com','$2y$10$IewybyXCtdvMmZ84RNE30u7CethUbjCW3njOCyvQg7OU.qzieEmmK','customer'),('user3','user3@asdf.com','$2y$10$9UV3D4ikWAbyXIc.JXFpLe0XE.CWOvHsN8gyrfPtXiEORvHRWqy9W','customer');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-02 10:32:17
