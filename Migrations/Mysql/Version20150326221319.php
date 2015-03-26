<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20150326221319 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		// this up() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("CREATE TABLE famelo_saas_domain_model_credituse (persistence_object_identifier VARCHAR(40) NOT NULL, plan VARCHAR(40) DEFAULT NULL, created DATETIME NOT NULL, reference VARCHAR(255) NOT NULL, INDEX IDX_BB3B7DEBDD5A5B7D (plan), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
		$this->addSql("ALTER TABLE famelo_saas_domain_model_credituse ADD CONSTRAINT FK_BB3B7DEBDD5A5B7D FOREIGN KEY (plan) REFERENCES famelo_saas_domain_model_plan (persistence_object_identifier)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		// this down() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("DROP TABLE famelo_saas_domain_model_credituse");
	}
}