CREATE TABLE IF NOT EXISTS `#__frontendusermanager_criterias` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`managers_list` TEXT  NOT NULL ,
`managed_list` TEXT  NOT NULL ,
`usergroups` TEXT NOT NULL ,
`languages` TEXT  NOT NULL ,
`profilefields` TEXT NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci;
