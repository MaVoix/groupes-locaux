ALTER TABLE `user` ADD `key_edit` VARCHAR(255) NOT NULL AFTER `tel`, ADD `key_edit_limit` DATETIME NOT NULL AFTER `key_edit`;