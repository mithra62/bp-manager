<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150125003019 extends AbstractMigration
{
    public static $description = "Migration description";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `notes` ADD `hashed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `description`; ");
        $this->addSql("ALTER TABLE `bookmarks` ADD `hashed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `description`; ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE `notes` DROP `hashed`;");
        $this->addSql("ALTER TABLE `bookmarks` DROP `hashed`;");
    }
}
