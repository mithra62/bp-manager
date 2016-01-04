<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160110361142 extends AbstractMigration
{
    public static $description = "Migration description";

    public function up(MetadataInterface $schema)
    {
    	//this is the first up() call so we're importing the whole schema
    	$path = realpath(__DIR__.'/../data/install.sql');
    	$sql = file_get_contents($path);
        $this->addSql($sql);
    }

    public function down(MetadataInterface $schema)
    {
    	/**
    	 * @todo abstract removing all database tables WAY LATER DOWN THE ROAD
    	 */
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
