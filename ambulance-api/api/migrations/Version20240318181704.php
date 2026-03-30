<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318181704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clinic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hospital_hospitals ADD clinic_id INT DEFAULT NULL, ADD phone VARCHAR(11) DEFAULT NULL, ADD additional_amount INT DEFAULT NULL, ADD main_amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hospital_hospitals ADD CONSTRAINT FK_2A085EBACC22AD4 FOREIGN KEY (clinic_id) REFERENCES clinic (id)');
        $this->addSql('CREATE INDEX IDX_2A085EBACC22AD4 ON hospital_hospitals (clinic_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_hospitals DROP FOREIGN KEY FK_2A085EBACC22AD4');
        $this->addSql('DROP TABLE clinic');
        $this->addSql('DROP INDEX IDX_2A085EBACC22AD4 ON hospital_hospitals');
        $this->addSql('ALTER TABLE hospital_hospitals DROP clinic_id, DROP phone, DROP additional_amount, DROP main_amount');
    }
}
