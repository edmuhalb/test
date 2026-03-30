<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205083916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agreement (id INT AUTO_INCREMENT NOT NULL, partner_id INT NOT NULL, starts_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2E655A249393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `row` (id INT AUTO_INCREMENT NOT NULL, agreement_id INT DEFAULT NULL, service_id INT NOT NULL, distance INT DEFAULT NULL, percent DOUBLE PRECISION NOT NULL, repeat_number INT NOT NULL, INDEX IDX_8430F6DB24890B2B (agreement_id), INDEX IDX_8430F6DBED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agreement ADD CONSTRAINT FK_2E655A249393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE `row` ADD CONSTRAINT FK_8430F6DB24890B2B FOREIGN KEY (agreement_id) REFERENCES agreement (id)');
        $this->addSql('ALTER TABLE `row` ADD CONSTRAINT FK_8430F6DBED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agreement DROP FOREIGN KEY FK_2E655A249393F8FE');
        $this->addSql('ALTER TABLE `row` DROP FOREIGN KEY FK_8430F6DB24890B2B');
        $this->addSql('ALTER TABLE `row` DROP FOREIGN KEY FK_8430F6DBED5CA9E6');
        $this->addSql('DROP TABLE agreement');
        $this->addSql('DROP TABLE `row`');
        $this->addSql('DROP TABLE service');
    }
}
