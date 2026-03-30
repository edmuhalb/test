<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212140637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hospital_hospitals (id INT AUTO_INCREMENT NOT NULL, partner_id INT NOT NULL, external VARCHAR(255) DEFAULT NULL, fio VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, nosology VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, amount INT DEFAULT NULL, INDEX IDX_2A085EBA9393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hospital_hospitals ADD CONSTRAINT FK_2A085EBA9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_hospitals DROP FOREIGN KEY FK_2A085EBA9393F8FE');
        $this->addSql('DROP TABLE hospital_hospitals');
    }
}
