<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231215091711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE med_team (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, doctor_id INT DEFAULT NULL, planned_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(32) NOT NULL, INDEX IDX_3BC06514642B8210 (admin_id), INDEX IDX_3BC0651487F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC06514642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC0651487F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC06514642B8210');
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC0651487F4FB17');
        $this->addSql('DROP TABLE med_team');
    }
}
