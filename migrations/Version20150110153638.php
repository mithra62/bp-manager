<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150110153638 extends AbstractMigration
{
    public static $description = "Migration description";

    public function up(MetadataInterface $schema)
    {
        $this->addSql('ALTER TABLE `files` DROP `owner`;');
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE `files` ADD `owner` INT(10) NOT NULL DEFAULT '0' AFTER `creator`, ADD INDEX (`owner`) ; ");
    }
}
