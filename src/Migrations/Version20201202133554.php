<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202133554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add table to store points table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE table_entry (id INT AUTO_INCREMENT NOT NULL, match_id INT DEFAULT NULL, user VARCHAR(255) NOT NULL, played INT NOT NULL, pints INT NOT NULL, bonus_points INT NOT NULL, points DOUBLE PRECISION NOT NULL, current_position INT NOT NULL, INDEX IDX_27DD50082ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE table_entry ADD CONSTRAINT FK_27DD50082ABEACD6 FOREIGN KEY (match_id) REFERENCES `match` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE table_entry');
    }
}
