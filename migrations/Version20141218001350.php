<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20141218001350 extends AbstractMigration
{
    public static $description = "Removes IM client columns from users table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `users` DROP `jabber`, DROP `aol`, DROP `yahoo`, DROP `google_talk`, DROP `msn`, DROP `ichat`, DROP `skype`; ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
