
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- customer_last_presence
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_last_presence`;

CREATE TABLE `customer_last_presence`
(
    `customer_id` INTEGER NOT NULL,
    `date` DATETIME NOT NULL,
    PRIMARY KEY (`customer_id`),
    UNIQUE INDEX `customer_last_presence_U_1` (`customer_id`),
    CONSTRAINT `fk_customer_last_presence_customer_id`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
