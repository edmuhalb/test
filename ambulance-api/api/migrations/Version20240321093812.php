<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321093812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work_schedule (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, work_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', type VARCHAR(32) NOT NULL, role VARCHAR(64) NOT NULL, INDEX IDX_8F8D9BA78C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_schedule ADD CONSTRAINT FK_8F8D9BA78C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_schedule DROP FOREIGN KEY FK_8F8D9BA78C03F15C');
        $this->addSql('DROP TABLE work_schedule');
    }
}
