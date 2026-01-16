<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260116121813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_column DROP FOREIGN KEY `FK_1DADB667D47C2D1B`');
        $this->addSql('DROP INDEX IDX_1DADB667D47C2D1B ON data_column');
        $this->addSql('DROP TABLE dataset');
        $this->addSql('ALTER TABLE data_column CHANGE dataset_id media_id INT NOT NULL');
        $this->addSql('ALTER TABLE data_column ADD CONSTRAINT FK_1DADB667EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_1DADB667EA9FDD75 ON data_column (media_id)');
        $this->addSql('ALTER TABLE media ADD name VARCHAR(255) NOT NULL, ADD uploaded_at DATETIME NOT NULL, ADD mime_type VARCHAR(255) NOT NULL, ADD provider_id INT NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA53A8AA FOREIGN KEY (provider_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10CA53A8AA ON media (provider_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dataset (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, filename VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, uploaded_at DATETIME NOT NULL, provider_id INT NOT NULL, INDEX IDX_B7A041D0A53A8AA (provider_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE data_column DROP FOREIGN KEY FK_1DADB667EA9FDD75');
        $this->addSql('DROP INDEX IDX_1DADB667EA9FDD75 ON data_column');
        $this->addSql('ALTER TABLE data_column CHANGE media_id dataset_id INT NOT NULL');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA53A8AA');
        $this->addSql('DROP INDEX IDX_6A2CA10CA53A8AA ON media');
        $this->addSql('ALTER TABLE media DROP name, DROP uploaded_at, DROP mime_type, DROP provider_id');
        $this->addSql('ALTER TABLE data_column ADD CONSTRAINT `FK_1DADB667D47C2D1B` FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1DADB667D47C2D1B ON data_column (dataset_id)');
    }
}