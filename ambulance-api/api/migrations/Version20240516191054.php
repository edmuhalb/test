<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516191054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD operator_reward_therapy INT DEFAULT 0 NOT NULL, ADD operator_reward_hospital INT DEFAULT 0 NOT NULL, ADD operator_reward_coding INT DEFAULT 0 NOT NULL, ADD operator_reward_stationary INT DEFAULT 0 NOT NULL, ADD operator_reward_total INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP operator_reward_therapy, DROP operator_reward_hospital, DROP operator_reward_coding, DROP operator_reward_stationary, DROP operator_reward_total');
    }
}
