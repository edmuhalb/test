<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120104220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrator_reports_files (administrator_report_id INT NOT NULL, file_object_id INT NOT NULL, INDEX IDX_F4070956EC855241 (administrator_report_id), INDEX IDX_F4070956AD22E95D (file_object_id), PRIMARY KEY(administrator_report_id, file_object_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrator_reports_files ADD CONSTRAINT FK_F4070956EC855241 FOREIGN KEY (administrator_report_id) REFERENCES administrator_reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE administrator_reports_files ADD CONSTRAINT FK_F4070956AD22E95D FOREIGN KEY (file_object_id) REFERENCES file_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE med_team ADD transport_report_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC06514DD11225B FOREIGN KEY (transport_report_id) REFERENCES administrator_reports (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BC06514DD11225B ON med_team (transport_report_id)');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrator_reports_files DROP FOREIGN KEY FK_F4070956EC855241');
        $this->addSql('ALTER TABLE administrator_reports_files DROP FOREIGN KEY FK_F4070956AD22E95D');
        $this->addSql('DROP TABLE administrator_reports_files');
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC06514DD11225B');
        $this->addSql('DROP INDEX UNIQ_3BC06514DD11225B ON med_team');
        $this->addSql('ALTER TABLE med_team DROP transport_report_id');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
