<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516090223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ambulance_call_log (id INT AUTO_INCREMENT NOT NULL, ambulance_call_id INT NOT NULL, shift_id INT DEFAULT NULL, reason_for_cancellation_id INT DEFAULT NULL, user_id INT DEFAULT NULL, old_status VARCHAR(255) DEFAULT NULL, new_status VARCHAR(255) DEFAULT NULL, changed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D19F8107C129AB28 (ambulance_call_id), INDEX IDX_D19F8107BB70BC0E (shift_id), INDEX IDX_D19F81077ACBF482 (reason_for_cancellation_id), INDEX IDX_D19F8107A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ambulance_call_log ADD CONSTRAINT FK_D19F8107C129AB28 FOREIGN KEY (ambulance_call_id) REFERENCES calling (id)');
        $this->addSql('ALTER TABLE ambulance_call_log ADD CONSTRAINT FK_D19F8107BB70BC0E FOREIGN KEY (shift_id) REFERENCES med_team (id)');
        $this->addSql('ALTER TABLE ambulance_call_log ADD CONSTRAINT FK_D19F81077ACBF482 FOREIGN KEY (reason_for_cancellation_id) REFERENCES reason_for_cancellation (id)');
        $this->addSql('ALTER TABLE ambulance_call_log ADD CONSTRAINT FK_D19F8107A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ambulance_call_log DROP FOREIGN KEY FK_D19F8107C129AB28');
        $this->addSql('ALTER TABLE ambulance_call_log DROP FOREIGN KEY FK_D19F8107BB70BC0E');
        $this->addSql('ALTER TABLE ambulance_call_log DROP FOREIGN KEY FK_D19F81077ACBF482');
        $this->addSql('ALTER TABLE ambulance_call_log DROP FOREIGN KEY FK_D19F8107A76ED395');
        $this->addSql('DROP TABLE ambulance_call_log');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
