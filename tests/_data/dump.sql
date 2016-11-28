SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `heroes` (
    `hero_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(128) NOT NULL,
    `pseudonym` VARCHAR(128),
    `date_of_birth` DATE,
    `genociders_knocked_down` SMALLINT,
    PRIMARY KEY (`hero_id`),
    UNIQUE KEY (`name`)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

TRUNCATE TABLE `heroes`;

SET FOREIGN_KEY_CHECKS = 1;
