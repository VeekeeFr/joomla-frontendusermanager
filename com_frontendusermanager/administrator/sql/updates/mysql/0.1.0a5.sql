ALTER TABLE `#__frontendusermanager_criterias`
ADD COLUMN `excludedFields` VARCHAR(255) NOT NULL AFTER `profilefields`;

ALTER TABLE `#__frontendusermanager_criterias`
ADD COLUMN `permissions` VARCHAR(255) NOT NULL AFTER `profilefields`;
