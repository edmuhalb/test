<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028091052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_schedule ADD city_id INT DEFAULT 1');
        $this->addSql('ALTER TABLE work_schedule ADD CONSTRAINT FK_8F8D9BA78BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_8F8D9BA78BAC62AF ON work_schedule (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_schedule DROP FOREIGN KEY FK_8F8D9BA78BAC62AF');
        $this->addSql('DROP INDEX IDX_8F8D9BA78BAC62AF ON work_schedule');
        $this->addSql('ALTER TABLE work_schedule DROP city_id');
    }
}
