CREATE TABLE `#__courses`
(
	`course_id` INT(11) NOT NULL AUTO_INCREMENT,
	`coursename` VARCHAR(50),
	`description` VARCHAR(50),
	`levelid` INT(11),
	`trainer` VARCHAR(50),
	`coursedate` DATE,
	`coursetime` TIME,
	`duration` VARCHAR(50),
	`bikes` INT(11),
	`createdbyid` INT(11),
	`lastmodified` TIMESTAMP DEFAULT now(),
	PRIMARY KEY (`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__courses_map`
(
	`map_id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL DEFAULT='0',
	`course_id` INT(11) NOT NULL DEFAULT='0',
	`contactnumber` VARCHAR(50) NOT NULL DEFAULT='',
	`created` TIMESTAMP DEFAULT now(),
	PRIMARY KEY (`map_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__courses_level`
(
	`level_id` INT(11) NOT NULL AUTO_INCREMENT,
	`level`	VARCHAR(50) NOT NULL DEFAULT='',
	PRIMARY KEY (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__courses_level`
(
	level
)
VALUES
(
	'Einsteiger'
);

INSERT INTO `#__courses_level`
(
	level
)
VALUES
(
	'Fortgeschritten'
);

INSERT INTO `#__courses_level`
(
	level
)
VALUES
(
	'Profi'
);