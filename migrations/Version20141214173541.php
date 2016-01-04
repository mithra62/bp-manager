<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20141214173541 extends AbstractMigration
{
    public static $description = "Adds the Account Invites table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE IF NOT EXISTS `account_invites` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `user_id` int(10) NOT NULL,
					  `account_id` int(10) NOT NULL,
					  `verification_hash` varchar(100) NOT NULL,
					  `last_modified` datetime NOT NULL,
					  `created_date` datetime NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `verification_hash` (`verification_hash`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
		);
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DROP TABLE account_invites");
    }
}
