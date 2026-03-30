<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227080334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling_rows CHANGE calling_id calling_id INT NOT NULL');
        $this->addSql('ALTER TABLE calling_rows ADD CONSTRAINT FK_549F674C6F1895A1 FOREIGN KEY (calling_id) REFERENCES calling (id)');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling_rows DROP FOREIGN KEY FK_549F674C6F1895A1');
        $this->addSql('ALTER TABLE calling_rows CHANGE calling_id calling_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
