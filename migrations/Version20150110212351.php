<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150110212351 extends AbstractMigration
{
    public static $description = "Migration description";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `user_role_permissions` (`name`, `site_area`) VALUES ('access_rest_api', 'rest-api'); ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DELETE FROM `user_role_permissions` WHERE `name` = 'access_rest_api'");
    }
}
