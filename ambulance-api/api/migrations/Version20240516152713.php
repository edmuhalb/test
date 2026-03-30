<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516152713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO payment_setting (id, value, title) VALUES
('operator_percent_therapy', 1, 'Процент оператору за терапию'),
('operator_percent_hospital', 1, 'Процент оператору за госпитализацию'),
('operator_percent_coding', 1, 'Процент оператору за кодирование'),
('operator_percent_stationary', 1, 'Процент оператору за стационар');");
    }

    public function down(Schema $schema): void
    {
    }
}
