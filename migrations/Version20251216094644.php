<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251216094644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY `FK_D8892622A76ED395`');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY `FK_D8892622E9ED820C`');
        $this->addSql('DROP INDEX IDX_D8892622A76ED395 ON rating');
        $this->addSql('DROP INDEX IDX_D8892622E9ED820C ON rating');
        $this->addSql('ALTER TABLE rating ADD pseudo VARCHAR(255) NOT NULL, ADD message LONGTEXT DEFAULT NULL, ADD rating INT NOT NULL, ADD created_at DATETIME NOT NULL, ADD article_id INT NOT NULL, DROP value, DROP user_id, DROP block_id');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926227294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_D88926227294869C ON rating (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926227294869C');
        $this->addSql('DROP INDEX IDX_D88926227294869C ON rating');
        $this->addSql('ALTER TABLE rating ADD value INT NOT NULL, ADD user_id INT NOT NULL, ADD block_id INT NOT NULL, DROP pseudo, DROP message, DROP rating, DROP created_at, DROP article_id');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT `FK_D8892622A76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT `FK_D8892622E9ED820C` FOREIGN KEY (block_id) REFERENCES block (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D8892622A76ED395 ON rating (user_id)');
        $this->addSql('CREATE INDEX IDX_D8892622E9ED820C ON rating (block_id)');
    }
}
