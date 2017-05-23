ALTER TABLE `group` ADD `cheque_payable_to` VARCHAR(255) NULL DEFAULT NULL AFTER `bic`;
ALTER TABLE `group` ADD `map_url` VARCHAR(255) NULL DEFAULT NULL AFTER `presentation`;