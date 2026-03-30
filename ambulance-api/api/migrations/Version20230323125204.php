<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323125204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD admin_id INT NOT NULL, ADD doctor_id INT NOT NULL, ADD title VARCHAR(256) NOT NULL, ADD number_calling VARCHAR(32) NOT NULL, ADD chronic_diseases VARCHAR(255) NOT NULL, ADD lead_type VARCHAR(255) NOT NULL, ADD partner_name VARCHAR(255) NOT NULL, ADD send_phone TINYINT(1) NOT NULL, ADD date_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C573642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C57387F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A606C573642B8210 ON calling (admin_id)');
        $this->addSql('CREATE INDEX IDX_A606C57387F4FB17 ON calling (doctor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C573642B8210');
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C57387F4FB17');
        $this->addSql('DROP INDEX IDX_A606C573642B8210 ON calling');
        $this->addSql('DROP INDEX IDX_A606C57387F4FB17 ON calling');
        $this->addSql('ALTER TABLE calling DROP admin_id, DROP doctor_id, DROP title, DROP number_calling, DROP chronic_diseases, DROP lead_type, DROP partner_name, DROP send_phone, DROP date_time');
    }
}
