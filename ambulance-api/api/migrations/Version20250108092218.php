<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250108092218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reason_for_cancellation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calling ADD reason_for_cancellation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C5737ACBF482 FOREIGN KEY (reason_for_cancellation_id) REFERENCES reason_for_cancellation (id)');
        $this->addSql('CREATE INDEX IDX_A606C5737ACBF482 ON calling (reason_for_cancellation_id)');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C5737ACBF482');
        $this->addSql('DROP TABLE reason_for_cancellation');
        $this->addSql('DROP INDEX IDX_A606C5737ACBF482 ON calling');
        $this->addSql('ALTER TABLE calling DROP reason_for_cancellation_id');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
