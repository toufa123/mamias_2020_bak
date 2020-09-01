<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200901125026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE baseline_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ch_cookieconsent_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ec_ap_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE timeline__action_component_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE timeline__action_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE timeline__component_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE timeline__timeline_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE ext_sonata_import_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ext_sonata_import_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ext_sonata_import_file (id INT NOT NULL, ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, file VARCHAR(255) NOT NULL, encode VARCHAR(255) NOT NULL, loader_class VARCHAR(255) NOT NULL, status INT NOT NULL, message TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ext_sonata_import_log (id INT NOT NULL, upload_file_id INT DEFAULT NULL, ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status INT NOT NULL, message TEXT DEFAULT NULL, line VARCHAR(255) NOT NULL, foreign_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EADE70EE56FC6382 ON ext_sonata_import_log (upload_file_id)');
        $this->addSql('ALTER TABLE ext_sonata_import_log ADD CONSTRAINT FK_EADE70EE56FC6382 FOREIGN KEY (upload_file_id) REFERENCES ext_sonata_import_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE ch_cookieconsent_log');
        $this->addSql('DROP TABLE vectors_distribution');
        $this->addSql('ALTER TABLE geo_occurence ALTER location TYPE Geometry(Point)');
        $this->addSql('ALTER TABLE geo_occurence ALTER location DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN geo_occurence.location IS \'(DC2Type:point)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ext_sonata_import_log DROP CONSTRAINT FK_EADE70EE56FC6382');
        $this->addSql('DROP SEQUENCE ext_sonata_import_file_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ext_sonata_import_log_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE baseline_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ch_cookieconsent_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ec_ap_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE timeline__action_component_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE timeline__action_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE timeline__component_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE timeline__timeline_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ch_cookieconsent_log (id INT NOT NULL, ip_address VARCHAR(255) NOT NULL, cookie_consent_key VARCHAR(255) NOT NULL, cookie_name VARCHAR(255) NOT NULL, cookie_value VARCHAR(255) NOT NULL, "timestamp" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE vectors_distribution (species_id INT NOT NULL, vector_id INT NOT NULL, PRIMARY KEY(species_id, vector_id))');
        $this->addSql('CREATE INDEX idx_75dbb2dcb2a1d860 ON vectors_distribution (species_id)');
        $this->addSql('CREATE INDEX idx_75dbb2dc94028d28 ON vectors_distribution (vector_id)');
        $this->addSql('ALTER TABLE vectors_distribution ADD CONSTRAINT fk_75dbb2dc94028d28 FOREIGN KEY (vector_id) REFERENCES vector_name (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vectors_distribution ADD CONSTRAINT fk_75dbb2dcb2a1d860 FOREIGN KEY (species_id) REFERENCES country_distribution (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE ext_sonata_import_file');
        $this->addSql('DROP TABLE ext_sonata_import_log');
        $this->addSql('ALTER TABLE geo_occurence ALTER location TYPE Geometry');
        $this->addSql('ALTER TABLE geo_occurence ALTER location DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN geo_occurence.location IS \'(DC2Type:point)(DC2Type:geometry)\'');
    }
}
