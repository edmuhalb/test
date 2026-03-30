<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114134509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE block_description (id VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');

        $this->addSql("INSERT INTO block_description (id)
VALUES 
    ('payroll_kpi_description'),
    ('payroll_metric_description'),
    ('payroll_transport_description')
    ");

        $this->addSql("UPDATE payroll_transport
SET name = 'Платки, Парковки'
WHERE id = 2;
");

        $this->addSql("UPDATE payroll_metric
SET name = 'Час работы врача (днем)'
WHERE id = 5;
");

        $this->addSql("UPDATE payroll_metric
SET name = 'Час работы врача (ночью)'
WHERE id = 6;
");
        $this->addSql("UPDATE payroll_metric
SET name = 'Час работы администратора (днем)'
WHERE id = 7;
");
        $this->addSql("UPDATE payroll_metric
SET name = 'Час работы администратора (ночью)'
WHERE id = 8;
");

        $this->addSql("UPDATE payroll_metric
SET name = 'Неустойка'
WHERE id = 9;
");

        $this->addSql("INSERT INTO payroll_metric (name)
VALUES 
    ('Транспортировка'),
    ('Стационар'),
    ('Кодирование'),
    ('Работа доктора вшивание')
    ");
    }



    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE block_description');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
