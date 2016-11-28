SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `heroes` (
    `name` VARCHAR(64) NOT NULL,
    `a_string` VARCHAR(128),
    `an_integer` SMALLINT,
    PRIMARY KEY (`name`)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

TRUNCATE TABLE `heroes`;

CREATE TABLE IF NOT EXISTS `heroes_multi_column_index` (
    `name1` VARCHAR(64) NOT NULL,
    `name2` VARCHAR(64) NOT NULL,
    `a_string` VARCHAR(128),
    `an_integer` SMALLINT,
    PRIMARY KEY (`name1`, `name2`)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

TRUNCATE TABLE `heroes_multi_column_index`;

CREATE TABLE IF NOT EXISTS `heroes_multiple_unique_indexes` (
    `name` VARCHAR(64) NOT NULL,
    `unique_thing` VARCHAR(64) NOT NULL,
    `an_integer` SMALLINT,
    PRIMARY KEY (`name`),
    UNIQUE KEY (`unique_thing`)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

TRUNCATE TABLE `heroes_multiple_unique_indexes`;

SET FOREIGN_KEY_CHECKS = 1;
