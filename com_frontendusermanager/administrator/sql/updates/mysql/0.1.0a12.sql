ALTER TABLE `#__frontendusermanager_criterias`
ADD COLUMN `name` VARCHAR(255) NOT NULL AFTER `id`,
ADD COLUMN `customfields` TEXT NOT NULL AFTER `profilefields`;
