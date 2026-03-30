<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225220003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD owner_id INT DEFAULT NULL, CHANGE admin_id admin_id INT DEFAULT NULL, CHANGE doctor_id doctor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C5737E3C61F9 FOREIGN KEY (owner_id) REFERENCES calling (id)');
        $this->addSql('CREATE INDEX IDX_A606C5737E3C61F9 ON calling (owner_id)');
        $this->addSql('ALTER TABLE hospital_hospitals ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hospital_hospitals ADD CONSTRAINT FK_2A085EBA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES calling (id)');
        $this->addSql('CREATE INDEX IDX_2A085EBA7E3C61F9 ON hospital_hospitals (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C5737E3C61F9');
        $this->addSql('DROP INDEX IDX_A606C5737E3C61F9 ON calling');
        $this->addSql('ALTER TABLE calling DROP owner_id, CHANGE admin_id admin_id INT NOT NULL, CHANGE doctor_id doctor_id INT NOT NULL');
        $this->addSql('ALTER TABLE hospital_hospitals DROP FOREIGN KEY FK_2A085EBA7E3C61F9');
        $this->addSql('DROP INDEX IDX_2A085EBA7E3C61F9 ON hospital_hospitals');
        $this->addSql('ALTER TABLE hospital_hospitals DROP owner_id');
    }
}
