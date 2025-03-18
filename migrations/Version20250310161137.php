<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310161137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE password (id SERIAL NOT NULL, owner_id INT NOT NULL, category_id INT NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_35C246D57E3C61F9 ON password (owner_id)');
        $this->addSql('CREATE INDEX IDX_35C246D512469DE2 ON password (category_id)');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT FK_35C246D57E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT FK_35C246D512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE password DROP CONSTRAINT FK_35C246D57E3C61F9');
        $this->addSql('ALTER TABLE password DROP CONSTRAINT FK_35C246D512469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE password');
    }
}
