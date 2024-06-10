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
        $configCheck = true;
        if (empty($GLOBALS['TL_FLEXIBLEELEMENT']) || empty($GLOBALS['TL_FLEXIBLEELEMENT']['templates'])) {
            $configCheck = false;
        }

        $result = $this->connection->executeQuery('SELECT id FROM `tl_content` WHERE `type` = "flexibleelement" AND `elementTemplate` IS NOT NULL');

        return $configCheck && !empty($result->fetchAllAssociative());
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

        $result = $this->connection->executeQuery('SELECT id, type, elementTemplate, flexibleImage FROM `tl_content` WHERE `type` = "flexibleelement"');

        foreach ($result->fetchAllAssociative() as $row) {
            $this->connection
                ->prepare('UPDATE `tl_content` SET `type` = ?, `flexibleTemplate` = ?, `flexibleImages` = ? WHERE id = ?')
                ->executeStatement([
                    'flexibleContent',
                    $templates[$row['elementTemplate']] ?? '',
                    $row['flexibleImage'],
                    $row['id'],
                ]);
        }

        return $this->createResult(
            true,
            'Moved from flexibleelement to flexibleContent.'
        );
    }
}
