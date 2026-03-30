<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225164402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE base (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE med_team ADD base_id INT DEFAULT NULL, ADD car_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC065146967DF41 FOREIGN KEY (base_id) REFERENCES base (id)');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC06514C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_3BC065146967DF41 ON med_team (base_id)');
        $this->addSql('CREATE INDEX IDX_3BC06514C3C6F69F ON med_team (car_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC065146967DF41');
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC06514C3C6F69F');
        $this->addSql('DROP TABLE base');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP INDEX IDX_3BC065146967DF41 ON med_team');
        $this->addSql('DROP INDEX IDX_3BC06514C3C6F69F ON med_team');
        $this->addSql('ALTER TABLE med_team DROP base_id, DROP car_id');
    }
}
