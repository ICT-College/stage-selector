SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `companies` (
`id` int(11) NOT NULL,
  `stagemarkt_id` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `address` varchar(64) CHARACTER SET utf8 NOT NULL,
  `postcode` varchar(16) COLLATE utf8_bin NOT NULL,
  `city` varchar(64) CHARACTER SET utf8 NOT NULL,
  `country` char(2) COLLATE utf8_bin NOT NULL,
  `coordinates` point DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `website` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `telephone` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2440 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `internship_applications` (
`id` int(11) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `student_id` char(36) COLLATE utf8_bin NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `accepted_coordinator` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','pending_coordinator','completed','denied') COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
) ENGINE=InnoDB AUTO_INCREMENT=2730 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `students` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `student_number` varchar(16) COLLATE utf8_bin NOT NULL,
  `initials` blob,
  `firstname` blob NOT NULL,
  `insertion` blob,
  `lastname` blob NOT NULL,
  `email` blob,
  `address` blob,
  `postcode` blob,
  `city` blob,
  `country` char(2) COLLATE utf8_bin NOT NULL,
  `telephone` blob,
  `gender` char(2) COLLATE utf8_bin NOT NULL,
  `birthday` blob,
  `birthplace` blob,
  `learning_pathway` varchar(4) COLLATE utf8_bin NOT NULL,
  `groupcode` varchar(16) COLLATE utf8_bin NOT NULL,
  `study_program_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
CREATE TABLE IF NOT EXISTS `study_programs` (
`id` int(11)
,`description` text
);DROP TABLE IF EXISTS `study_programs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `study_programs` AS select `school_stage_selector_main`.`study_programs`.`id` AS `id`,`school_stage_selector_main`.`study_programs`.`description` AS `description` from `school_stage_selector_main`.`study_programs`;


ALTER TABLE `companies`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `stagemarkt_id` (`stagemarkt_id`);

ALTER TABLE `internship_applications`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `positions`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `stagemarkt_id` (`stagemarkt_id`);

ALTER TABLE `students`
 ADD PRIMARY KEY (`id`);


ALTER TABLE `companies`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2440;
ALTER TABLE `internship_applications`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
ALTER TABLE `positions`
MODIFY `id` int(32) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2730;
