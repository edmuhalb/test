<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231019103543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team_locations (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, lon VARCHAR(16) NOT NULL, lat VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A0E3545D296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_teams (id INT AUTO_INCREMENT NOT NULL, lon VARCHAR(16) NOT NULL, lat VARCHAR(16) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_locations ADD CONSTRAINT FK_A0E3545D296CD8AE FOREIGN KEY (team_id) REFERENCES team_teams (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_locations DROP FOREIGN KEY FK_A0E3545D296CD8AE');
        $this->addSql('DROP TABLE team_locations');
        $this->addSql('DROP TABLE team_teams');
    }
}
