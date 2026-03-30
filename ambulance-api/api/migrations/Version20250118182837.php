<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118182837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payroll_employee_call_services (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, call_service_id INT NOT NULL, strategy_id INT NOT NULL, accrued_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', accrued_currency VARCHAR(3) NOT NULL COMMENT \'(DC2Type:money_currency)\', accrued_amount INT NOT NULL, INDEX IDX_6B621DDC8C03F15C (employee_id), INDEX IDX_6B621DDCC9C3E87C (call_service_id), INDEX IDX_6B621DDCD5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payroll_employee_calls (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, call_id INT NOT NULL, strategy_id INT NOT NULL, accrued_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', accrued_currency VARCHAR(3) NOT NULL COMMENT \'(DC2Type:money_currency)\', accrued_amount INT NOT NULL, INDEX IDX_D4D6B2AF8C03F15C (employee_id), INDEX IDX_D4D6B2AF50A89B2C (call_id), INDEX IDX_D4D6B2AFD5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payroll_employee_shifts (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, shift_id INT NOT NULL, strategy_id INT NOT NULL, accrued_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', accrued_currency VARCHAR(3) NOT NULL COMMENT \'(DC2Type:money_currency)\', accrued_amount INT NOT NULL, INDEX IDX_267D24098C03F15C (employee_id), INDEX IDX_267D2409BB70BC0E (shift_id), INDEX IDX_267D2409D5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payroll_strategies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, value DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(128) DEFAULT NULL, processor VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payroll_employee_call_services ADD CONSTRAINT FK_6B621DDC8C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payroll_employee_call_services ADD CONSTRAINT FK_6B621DDCC9C3E87C FOREIGN KEY (call_service_id) REFERENCES calling_rows (id)');
        $this->addSql('ALTER TABLE payroll_employee_call_services ADD CONSTRAINT FK_6B621DDCD5CAD932 FOREIGN KEY (strategy_id) REFERENCES payroll_strategies (id)');
        $this->addSql('ALTER TABLE payroll_employee_calls ADD CONSTRAINT FK_D4D6B2AF8C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payroll_employee_calls ADD CONSTRAINT FK_D4D6B2AF50A89B2C FOREIGN KEY (call_id) REFERENCES calling (id)');
        $this->addSql('ALTER TABLE payroll_employee_calls ADD CONSTRAINT FK_D4D6B2AFD5CAD932 FOREIGN KEY (strategy_id) REFERENCES payroll_strategies (id)');
        $this->addSql('ALTER TABLE payroll_employee_shifts ADD CONSTRAINT FK_267D24098C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payroll_employee_shifts ADD CONSTRAINT FK_267D2409BB70BC0E FOREIGN KEY (shift_id) REFERENCES med_team (id)');
        $this->addSql('ALTER TABLE payroll_employee_shifts ADD CONSTRAINT FK_267D2409D5CAD932 FOREIGN KEY (strategy_id) REFERENCES payroll_strategies (id)');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payroll_employee_call_services DROP FOREIGN KEY FK_6B621DDC8C03F15C');
        $this->addSql('ALTER TABLE payroll_employee_call_services DROP FOREIGN KEY FK_6B621DDCC9C3E87C');
        $this->addSql('ALTER TABLE payroll_employee_call_services DROP FOREIGN KEY FK_6B621DDCD5CAD932');
        $this->addSql('ALTER TABLE payroll_employee_calls DROP FOREIGN KEY FK_D4D6B2AF8C03F15C');
        $this->addSql('ALTER TABLE payroll_employee_calls DROP FOREIGN KEY FK_D4D6B2AF50A89B2C');
        $this->addSql('ALTER TABLE payroll_employee_calls DROP FOREIGN KEY FK_D4D6B2AFD5CAD932');
        $this->addSql('ALTER TABLE payroll_employee_shifts DROP FOREIGN KEY FK_267D24098C03F15C');
        $this->addSql('ALTER TABLE payroll_employee_shifts DROP FOREIGN KEY FK_267D2409BB70BC0E');
        $this->addSql('ALTER TABLE payroll_employee_shifts DROP FOREIGN KEY FK_267D2409D5CAD932');
        $this->addSql('DROP TABLE payroll_employee_call_services');
        $this->addSql('DROP TABLE payroll_employee_calls');
        $this->addSql('DROP TABLE payroll_employee_shifts');
        $this->addSql('DROP TABLE payroll_strategies');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
