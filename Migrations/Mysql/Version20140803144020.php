<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140803144020 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		// this up() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("CREATE TABLE famelo_saas_domain_model_billing (persistence_object_identifier VARCHAR(40) NOT NULL, plan VARCHAR(40) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, zip VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_AEA26312DD5A5B7D (plan), PRIMARY KEY(persistence_object_identifier))");
		$this->addSql("ALTER TABLE famelo_saas_domain_model_billing ADD CONSTRAINT FK_AEA26312DD5A5B7D FOREIGN KEY (plan) REFERENCES famelo_saas_domain_model_plan (persistence_object_identifier)");
		$this->addSql("ALTER TABLE famelo_saas_domain_model_plan ADD billing VARCHAR(40) DEFAULT NULL");
		$this->addSql("ALTER TABLE famelo_saas_domain_model_plan ADD CONSTRAINT FK_D5E796ABEC224CAA FOREIGN KEY (billing) REFERENCES famelo_saas_domain_model_billing (persistence_object_identifier)");
		$this->addSql("CREATE UNIQUE INDEX UNIQ_D5E796ABEC224CAA ON famelo_saas_domain_model_plan (billing)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		// this down() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("ALTER TABLE famelo_saas_domain_model_plan DROP FOREIGN KEY FK_D5E796ABEC224CAA");
		$this->addSql("DROP TABLE famelo_saas_domain_model_billing");
		$this->addSql("DROP INDEX UNIQ_D5E796ABEC224CAA ON famelo_saas_domain_model_plan");
		$this->addSql("ALTER TABLE famelo_saas_domain_model_plan DROP billing");
	}
}