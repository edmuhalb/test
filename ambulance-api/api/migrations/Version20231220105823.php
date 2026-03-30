<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220105823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calling_rows (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, calling_id INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, INDEX IDX_549F674CED5CA9E6 (service_id), INDEX IDX_549F674C6F1895A1 (calling_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calling_rows ADD CONSTRAINT FK_549F674CED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_services (id)');
        $this->addSql('ALTER TABLE calling_rows ADD CONSTRAINT FK_549F674C6F1895A1 FOREIGN KEY (calling_id) REFERENCES calling (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling_rows DROP FOREIGN KEY FK_549F674CED5CA9E6');
        $this->addSql('ALTER TABLE calling_rows DROP FOREIGN KEY FK_549F674C6F1895A1');
        $this->addSql('DROP TABLE calling_rows');
    }
}
