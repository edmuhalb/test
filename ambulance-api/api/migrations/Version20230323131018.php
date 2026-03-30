<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323131018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling CHANGE chronic_diseases chronic_diseases VARCHAR(255) DEFAULT NULL, CHANGE lead_type lead_type VARCHAR(255) DEFAULT NULL, CHANGE partner_name partner_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling CHANGE chronic_diseases chronic_diseases VARCHAR(255) NOT NULL, CHANGE lead_type lead_type VARCHAR(255) NOT NULL, CHANGE partner_name partner_name VARCHAR(255) NOT NULL');
    }
}
