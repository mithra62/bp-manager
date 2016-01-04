<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20141215143333 extends AbstractMigration
{
    public static $description = "Updates user_accounts table to add a creation date to it";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `user_accounts` ADD `created_date` DATETIME NULL DEFAULT NULL AFTER `account_id`; ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql('ALTER TABLE `user_accounts` DROP `created_date`;');
    }
}
