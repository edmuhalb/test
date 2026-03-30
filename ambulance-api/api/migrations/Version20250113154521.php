<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250113154521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payroll_kpi (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, value DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payroll_metric (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, value DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payroll_transport (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, value DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
        $this->addSql("INSERT INTO payroll_kpi (name)
VALUES 
    ('Персональная заявка'),
    ('Возврат'),
    ('Средний чек'),
    ('Кол-во госпитализаций'),
    ('Переданный повтор'),
    ('Личный коэффициент')
    ");

        $this->addSql("INSERT INTO payroll_metric (name)
VALUES 
    ('Вызов'),
    ('Повтор 1'),
    ('Повтор 2 и последующие'),
    ('Госпитализация'),
    ('Смена Врача'),
    ('Смена Врача'),
    ('Смена Администратора'),
    ('Смена Администратора'),
    ('Неустойка (каждому)')
    ");

        $this->addSql("INSERT INTO payroll_transport (name)
VALUES 
    ('Бензин'),
    ('Платки, ПарковкиПлатки, Парковки'),
    ('Арена авто (масло+ мойка+ аренда)')
    ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE payroll_kpi');
        $this->addSql('DROP TABLE payroll_metric');
        $this->addSql('DROP TABLE payroll_transport');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
