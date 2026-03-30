<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250119101443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payroll_employee_call_services DROP FOREIGN KEY FK_6B621DDCD5CAD932');
        $this->addSql('ALTER TABLE payroll_employee_calls DROP FOREIGN KEY FK_D4D6B2AFD5CAD932');
        $this->addSql('ALTER TABLE payroll_employee_shifts DROP FOREIGN KEY FK_267D2409D5CAD932');
        $this->addSql('CREATE TABLE payroll_calculator (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, value DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(128) NOT NULL, processor VARCHAR(255) NOT NULL, target VARCHAR(128) NOT NULL, sort INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE payroll_strategies');
        $this->addSql('DROP INDEX IDX_6B621DDCD5CAD932 ON payroll_employee_call_services');
        $this->addSql('ALTER TABLE payroll_employee_call_services DROP strategy_id');
        $this->addSql('DROP INDEX IDX_D4D6B2AFD5CAD932 ON payroll_employee_calls');
        $this->addSql('ALTER TABLE payroll_employee_calls CHANGE strategy_id calculator_id INT NOT NULL');
        $this->addSql('ALTER TABLE payroll_employee_calls ADD CONSTRAINT FK_D4D6B2AFACF2C4B8 FOREIGN KEY (calculator_id) REFERENCES payroll_calculator (id)');
        $this->addSql('CREATE INDEX IDX_D4D6B2AFACF2C4B8 ON payroll_employee_calls (calculator_id)');
        $this->addSql('DROP INDEX IDX_267D2409D5CAD932 ON payroll_employee_shifts');
        $this->addSql('ALTER TABLE payroll_employee_shifts CHANGE strategy_id calculator_id INT NOT NULL');
        $this->addSql('ALTER TABLE payroll_employee_shifts ADD CONSTRAINT FK_267D2409ACF2C4B8 FOREIGN KEY (calculator_id) REFERENCES payroll_calculator (id)');
        $this->addSql('CREATE INDEX IDX_267D2409ACF2C4B8 ON payroll_employee_shifts (calculator_id)');
        $this->addSql('ALTER TABLE service_services ADD employee_payroll_calculator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service_services ADD CONSTRAINT FK_8A44BA54A3E0F43 FOREIGN KEY (employee_payroll_calculator_id) REFERENCES payroll_calculator (id)');
        $this->addSql('CREATE INDEX IDX_8A44BA54A3E0F43 ON service_services (employee_payroll_calculator_id)');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payroll_employee_calls DROP FOREIGN KEY FK_D4D6B2AFACF2C4B8');
        $this->addSql('ALTER TABLE payroll_employee_shifts DROP FOREIGN KEY FK_267D2409ACF2C4B8');
        $this->addSql('ALTER TABLE service_services DROP FOREIGN KEY FK_8A44BA54A3E0F43');
        $this->addSql('CREATE TABLE payroll_strategies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, value DOUBLE PRECISION DEFAULT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(128) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, processor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE payroll_calculator');
        $this->addSql('ALTER TABLE payroll_employee_call_services ADD strategy_id INT NOT NULL');
        $this->addSql('ALTER TABLE payroll_employee_call_services ADD CONSTRAINT FK_6B621DDCD5CAD932 FOREIGN KEY (strategy_id) REFERENCES payroll_strategies (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6B621DDCD5CAD932 ON payroll_employee_call_services (strategy_id)');
        $this->addSql('DROP INDEX IDX_D4D6B2AFACF2C4B8 ON payroll_employee_calls');
        $this->addSql('ALTER TABLE payroll_employee_calls CHANGE calculator_id strategy_id INT NOT NULL');
        $this->addSql('ALTER TABLE payroll_employee_calls ADD CONSTRAINT FK_D4D6B2AFD5CAD932 FOREIGN KEY (strategy_id) REFERENCES payroll_strategies (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D4D6B2AFD5CAD932 ON payroll_employee_calls (strategy_id)');
        $this->addSql('DROP INDEX IDX_267D2409ACF2C4B8 ON payroll_employee_shifts');
        $this->addSql('ALTER TABLE payroll_employee_shifts CHANGE calculator_id strategy_id INT NOT NULL');
        $this->addSql('ALTER TABLE payroll_employee_shifts ADD CONSTRAINT FK_267D2409D5CAD932 FOREIGN KEY (strategy_id) REFERENCES payroll_strategies (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_267D2409D5CAD932 ON payroll_employee_shifts (strategy_id)');
        $this->addSql('DROP INDEX IDX_8A44BA54A3E0F43 ON service_services');
        $this->addSql('ALTER TABLE service_services DROP employee_payroll_calculator_id');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
