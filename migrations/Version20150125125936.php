<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150125125936 extends AbstractMigration
{
    public static $description = "Migration description";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `user_role_permissions` (`name`, `site_area`) VALUES ('self_allow_ip', 'ips'); ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DELETE FROM `user_role_permissions` WHERE name='self_allow_ip' AND site_area='ips';");
    }
}
