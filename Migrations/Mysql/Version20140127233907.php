<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140127233907 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		// this up() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE TABLE famelo_saas_domain_model_person (persistence_object_identifier VARCHAR(40) NOT NULL, phone VARCHAR(255) NOT NULL, mobile VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(persistence_object_identifier))");
		$this->addSql("ALTER TABLE famelo_saas_domain_model_person ADD CONSTRAINT FK_66B055F47A46B0A FOREIGN KEY (persistence_object_identifier) REFERENCES typo3_party_domain_model_abstractparty (persistence_object_identifier) ON DELETE CASCADE");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		// this down() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("DROP TABLE famelo_saas_domain_model_person");
	}
}