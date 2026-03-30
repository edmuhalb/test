<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250201055838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payroll_employee_call_services ADD amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE payroll_employee_calls ADD amount DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE payroll_kpi_documents CHANGE period_start period_start DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE period_end period_end DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payroll_employee_call_services DROP amount');
        $this->addSql('ALTER TABLE payroll_employee_calls DROP amount');
        $this->addSql('ALTER TABLE payroll_kpi_documents CHANGE period_start period_start DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE period_end period_end DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
