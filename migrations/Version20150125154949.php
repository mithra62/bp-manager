<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150125154949 extends AbstractMigration
{
    public static $description = "Migration description";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `ips` ADD `confirm_key` VARCHAR(80) NULL DEFAULT NULL AFTER `description`; ");
        $this->addSql("ALTER TABLE ips DROP INDEX ip_raw;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE `ips` DROP `confirm_key`;");
        $this->addSql("ALTER TABLE `ips` ADD UNIQUE(`ip_raw`);");
    }
}
