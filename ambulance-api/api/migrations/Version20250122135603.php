<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250122135603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payroll_employee_kpis (id INT AUTO_INCREMENT NOT NULL, calculator_id INT NOT NULL, record_id INT NOT NULL, kpi DOUBLE PRECISION NOT NULL, accrued_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', original_currency VARCHAR(3) NOT NULL COMMENT \'(DC2Type:money_currency)\', original_amount INT NOT NULL, accrued_currency VARCHAR(3) NOT NULL COMMENT \'(DC2Type:money_currency)\', accrued_amount INT NOT NULL, INDEX IDX_4AC7252EACF2C4B8 (calculator_id), INDEX IDX_4AC7252E4DFD750C (record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payroll_kpi_documents (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', period_start DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', period_end DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payroll_kpi_documents_records (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_B2884A34C33F7837 (document_id), INDEX IDX_B2884A348C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payroll_employee_kpis ADD CONSTRAINT FK_4AC7252EACF2C4B8 FOREIGN KEY (calculator_id) REFERENCES payroll_calculator (id)');
        $this->addSql('ALTER TABLE payroll_employee_kpis ADD CONSTRAINT FK_4AC7252E4DFD750C FOREIGN KEY (record_id) REFERENCES payroll_kpi_documents_records (id)');
        $this->addSql('ALTER TABLE payroll_kpi_documents_records ADD CONSTRAINT FK_B2884A34C33F7837 FOREIGN KEY (document_id) REFERENCES payroll_kpi_documents (id)');
        $this->addSql('ALTER TABLE payroll_kpi_documents_records ADD CONSTRAINT FK_B2884A348C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payroll_employee_kpis DROP FOREIGN KEY FK_4AC7252EACF2C4B8');
        $this->addSql('ALTER TABLE payroll_employee_kpis DROP FOREIGN KEY FK_4AC7252E4DFD750C');
        $this->addSql('ALTER TABLE payroll_kpi_documents_records DROP FOREIGN KEY FK_B2884A34C33F7837');
        $this->addSql('ALTER TABLE payroll_kpi_documents_records DROP FOREIGN KEY FK_B2884A348C03F15C');
        $this->addSql('DROP TABLE payroll_employee_kpis');
        $this->addSql('DROP TABLE payroll_kpi_documents');
        $this->addSql('DROP TABLE payroll_kpi_documents_records');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
