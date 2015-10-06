-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2015 at 09:34 AM
-- Server version: 5.6.25-0ubuntu0.15.04.1
-- PHP Version: 5.6.4-4ubuntu6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `school_stage_selector_ictcollege`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
    `id` int(11) NOT NULL,
    `stagemarkt_id` varchar(16) COLLATE utf8_bin DEFAULT NULL,
    `name` varchar(64) CHARACTER SET utf8 NOT NULL,
    `address` varchar(64) CHARACTER SET utf8 NOT NULL,
    `postcode` char(6) COLLATE utf8_bin NOT NULL,
    `city` varchar(64) CHARACTER SET utf8 NOT NULL,
    `country` char(2) COLLATE utf8_bin NOT NULL,
    `coordinates` point DEFAULT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=16809 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `placement_students`
--

CREATE TABLE IF NOT EXISTS `placement_students` (
    `id` int(11) NOT NULL,
    `position_id` int(11) NOT NULL,
    `student_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE IF NOT EXISTS `positions` (
    `id` int(32) NOT NULL,
    `stagemarkt_id` int(11) DEFAULT NULL,
    `learning_pathway` varchar(8) COLLATE utf8_bin NOT NULL,
    `description` text CHARACTER SET utf8,
    `start` date DEFAULT NULL,
    `end` date DEFAULT NULL,
    `amount` int(3) NOT NULL,
    `company_id` int(11) NOT NULL,
    `study_program_id` int(8) NOT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=33092 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Stand-in structure for view `study_programs`
--
CREATE TABLE IF NOT EXISTS `study_programs` (
     `id` int(11)
    ,`description` text
);
-- --------------------------------------------------------

--
-- Structure for view `study_programs`
--
DROP TABLE IF EXISTS `study_programs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `study_programs` AS select `school_stage_selector_main`.`study_programs`.`id` AS `id`,`school_stage_selector_main`.`study_programs`.`description` AS `description` from `school_stage_selector_main`.`study_programs`;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `stagemarkt_id` (`stagemarkt_id`);

--
-- Indexes for table `placement_students`
--
ALTER TABLE `placement_students`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `stagemarkt_id` (`stagemarkt_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16809;
--
-- AUTO_INCREMENT for table `placement_students`
--
ALTER TABLE `placement_students`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
MODIFY `id` int(32) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33092;--
-- Database: `school_stage_selector_main`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `DISTANCE`(`point1` POINT, `point2` POINT) RETURNS float
DETERMINISTIC
    BEGIN
        RETURN HAVERSINE(point1, point2) * 111.045;
    END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `haversine`(`point1` POINT, `point2` POINT) RETURNS float
NO SQL
DETERMINISTIC
    COMMENT 'Returns the distance in degrees on the Earth             between two known points of latitude and longitude'
    BEGIN
        DECLARE lat1 FLOAT;
        DECLARE lat2 FLOAT;
        DECLARE lon1 FLOAT;
        DECLARE lon2 FLOAT;

        SET lat1 = X(point1);
        SET lat2 = X(point2);
        SET lon1 = Y(point1);
        SET lon2 = Y(point2);

        RETURN DEGREES(ACOS(
                           COS(RADIANS(lat1)) *
                           COS(RADIANS(lat2)) *
                           COS(RADIANS(lon2) - RADIANS(lon1)) +
                           SIN(RADIANS(lat1)) * SIN(RADIANS(lat2))
                       ));
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `study_programs`
--

CREATE TABLE IF NOT EXISTS `study_programs` (
    `id` int(11) NOT NULL,
    `description` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25188 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `study_programs`
--
ALTER TABLE `study_programs`
ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `study_programs`
--
ALTER TABLE `study_programs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25188;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
