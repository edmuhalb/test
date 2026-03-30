<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240616011038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrator_reports_mileage_receipts (administrator_report_id INT NOT NULL, media_object_id INT NOT NULL, INDEX IDX_39225C05EC855241 (administrator_report_id), INDEX IDX_39225C0564DE5A5 (media_object_id), PRIMARY KEY(administrator_report_id, media_object_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE administrator_reports_parking_fees_receipts (administrator_report_id INT NOT NULL, media_object_id INT NOT NULL, INDEX IDX_63DD3A7EEC855241 (administrator_report_id), INDEX IDX_63DD3A7E64DE5A5 (media_object_id), PRIMARY KEY(administrator_report_id, media_object_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrator_reports_mileage_receipts ADD CONSTRAINT FK_39225C05EC855241 FOREIGN KEY (administrator_report_id) REFERENCES administrator_reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE administrator_reports_mileage_receipts ADD CONSTRAINT FK_39225C0564DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE administrator_reports_parking_fees_receipts ADD CONSTRAINT FK_63DD3A7EEC855241 FOREIGN KEY (administrator_report_id) REFERENCES administrator_reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE administrator_reports_parking_fees_receipts ADD CONSTRAINT FK_63DD3A7E64DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrator_reports_mileage_receipts DROP FOREIGN KEY FK_39225C05EC855241');
        $this->addSql('ALTER TABLE administrator_reports_mileage_receipts DROP FOREIGN KEY FK_39225C0564DE5A5');
        $this->addSql('ALTER TABLE administrator_reports_parking_fees_receipts DROP FOREIGN KEY FK_63DD3A7EEC855241');
        $this->addSql('ALTER TABLE administrator_reports_parking_fees_receipts DROP FOREIGN KEY FK_63DD3A7E64DE5A5');
        $this->addSql('DROP TABLE administrator_reports_mileage_receipts');
        $this->addSql('DROP TABLE administrator_reports_parking_fees_receipts');
    }
}
