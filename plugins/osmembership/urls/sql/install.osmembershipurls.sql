CREATE TABLE IF NOT EXISTS `#__osmembership_urls` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `plan_id` INT NULL,
  `url` TEXT NULL,
  PRIMARY KEY(`id`)
) DEFAULT CHARSET=utf8 ;