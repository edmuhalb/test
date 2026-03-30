<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320165323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_hospitals ADD hospitalized_by_id INT DEFAULT NULL, ADD discharged_by_id INT DEFAULT NULL, ADD hospitalized_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD discharged_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE hospital_hospitals ADD CONSTRAINT FK_2A085EBA5D7B9F8D FOREIGN KEY (hospitalized_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hospital_hospitals ADD CONSTRAINT FK_2A085EBA13E5B903 FOREIGN KEY (discharged_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2A085EBA5D7B9F8D ON hospital_hospitals (hospitalized_by_id)');
        $this->addSql('CREATE INDEX IDX_2A085EBA13E5B903 ON hospital_hospitals (discharged_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_hospitals DROP FOREIGN KEY FK_2A085EBA5D7B9F8D');
        $this->addSql('ALTER TABLE hospital_hospitals DROP FOREIGN KEY FK_2A085EBA13E5B903');
        $this->addSql('DROP INDEX IDX_2A085EBA5D7B9F8D ON hospital_hospitals');
        $this->addSql('DROP INDEX IDX_2A085EBA13E5B903 ON hospital_hospitals');
        $this->addSql('ALTER TABLE hospital_hospitals DROP hospitalized_by_id, DROP discharged_by_id, DROP hospitalized_at, DROP discharged_at');
    }
}
