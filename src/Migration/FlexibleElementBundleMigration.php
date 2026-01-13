<?php

namespace Guave\FlexibleContentBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class FlexibleElementBundleMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        if (empty($GLOBALS['TL_FLEXIBLEELEMENT']) || empty($GLOBALS['TL_FLEXIBLEELEMENT']['templates'])) {
            return false;
        }

        $configCheck = $this->connection->executeQuery('SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = "tl_content" AND COLUMN_NAME = "elementTemplate"');

        if (empty($configCheck->fetchAllAssociative())) {
            return false;
        }

        $result = $this->connection->executeQuery('SELECT id FROM `tl_content` WHERE (`type` = "flexibleelement" AND `elementTemplate` IS NOT NULL) OR (`type` = "flexibleElement" AND `elementTemplate` IS NOT NULL)');

        return !empty($result->fetchAllAssociative());
    }

    public function run(): MigrationResult
    {
        $templates = array_column($GLOBALS['TL_FLEXIBLEELEMENT']['templates'], 'template', 'id');

        if (count($this->connection->executeQuery('SHOW COLUMNS FROM tl_content LIKE "flexibleTemplate"')->fetchAllAssociative()) < 1) {
            $this->connection->executeStatement('ALTER TABLE tl_content ADD COLUMN `flexibleTemplate` VARCHAR(255) DEFAULT "" NOT NULL');
        }

        if (count($this->connection->executeQuery('SHOW COLUMNS FROM tl_content LIKE "flexibleImages"')->fetchAllAssociative()) < 1) {
            $this->connection->executeStatement('ALTER TABLE tl_content ADD COLUMN `flexibleImages` LONGBLOB DEFAULT NULL');
        }

        $result = $this->connection->executeQuery('SELECT id, type, elementTemplate, flexibleImage FROM `tl_content` WHERE `type` = "flexibleelement" OR `type` = "flexibleElement"');


        $statement = $this->connection->prepare('UPDATE `tl_content` SET `type` = ?, `flexibleTemplate` = ?, `flexibleImages` = ? WHERE id = ?');
        foreach ($result->fetchAllAssociative() as $row) {
            $statement->executeStatement([
                'flexibleContent',
                $templates[$row['elementTemplate']] ?? '',
                $row['flexibleImage'],
                $row['id'],
            ]);
        }

        return $this->createResult(
            true,
            'Moved from flexibleElement to flexibleContent.'
        );
    }
}
