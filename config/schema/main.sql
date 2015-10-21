SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DELIMITER $$
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

CREATE TABLE IF NOT EXISTS `shards` (
`id` int(11) NOT NULL,
  `subdomain` varchar(16) COLLATE utf8_bin NOT NULL,
  `datasource` varchar(16) COLLATE utf8_bin NOT NULL,
  `secured_datasource` varchar(16) COLLATE utf8_bin NOT NULL,
  `selector` varchar(64) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `study_programs` (
`id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25193 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `student_id` char(36) COLLATE utf8_bin DEFAULT NULL,
  `firstname` varchar(64) CHARACTER SET utf8 NOT NULL,
  `insertion` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `lastname` varchar(64) CHARACTER SET utf8 NOT NULL,
  `email` varchar(128) COLLATE utf8_bin NOT NULL,
  `student_number` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
  `learning_pathway` varchar(4) COLLATE utf8_bin DEFAULT NULL,
  `study_program_id` int(11) DEFAULT NULL,
  `groupcode` varchar(16) COLLATE utf8_bin NOT NULL,
  `password` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `activation_token` char(32) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


ALTER TABLE `shards`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `study_programs`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`), ADD UNIQUE KEY `student_id` (`student_id`), ADD UNIQUE KEY `student_number` (`student_number`);


ALTER TABLE `shards`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
ALTER TABLE `study_programs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25193;
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=94;
