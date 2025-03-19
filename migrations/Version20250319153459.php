<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319153459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password DROP CONSTRAINT fk_35c246d512469de2');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('CREATE TABLE site (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP INDEX idx_35c246d512469de2');
        $this->addSql('ALTER TABLE password RENAME COLUMN category_id TO site_id');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT FK_35C246D5F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35C246D5F6BD1646 ON password (site_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE password DROP CONSTRAINT FK_35C246D5F6BD1646');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP INDEX UNIQ_35C246D5F6BD1646');
        $this->addSql('ALTER TABLE password RENAME COLUMN site_id TO category_id');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT fk_35c246d512469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_35c246d512469de2 ON password (category_id)');
    }
}
