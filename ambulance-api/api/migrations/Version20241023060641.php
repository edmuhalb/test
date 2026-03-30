<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023060641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calling ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C5738BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_A606C5738BAC62AF ON calling (city_id)');
        $this->addSql('ALTER TABLE med_team ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC065148BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_3BC065148BAC62AF ON med_team (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C5738BAC62AF');
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC065148BAC62AF');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP INDEX IDX_A606C5738BAC62AF ON calling');
        $this->addSql('ALTER TABLE calling DROP city_id');
        $this->addSql('DROP INDEX IDX_3BC065148BAC62AF ON med_team');
        $this->addSql('ALTER TABLE med_team DROP city_id');
    }
}
