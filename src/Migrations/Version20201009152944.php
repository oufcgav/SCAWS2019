<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201009152944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE goal (id INT AUTO_INCREMENT NOT NULL, match_id INT DEFAULT NULL, scorer VARCHAR(255) NOT NULL, timing VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, INDEX IDX_FCDCEB2E2ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `match` (id INT AUTO_INCREMENT NOT NULL, season_id INT DEFAULT NULL, opponent VARCHAR(255) NOT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', location VARCHAR(255) NOT NULL, competition VARCHAR(255) NOT NULL, INDEX IDX_7A5BC5054EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pint (id INT AUTO_INCREMENT NOT NULL, match_id INT DEFAULT NULL, user VARCHAR(255) NOT NULL, count INT NOT NULL, INDEX IDX_A16B7C232ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prediction (id INT AUTO_INCREMENT NOT NULL, match_id INT DEFAULT NULL, user VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, time VARCHAR(255) NOT NULL, at_match TINYINT(1) NOT NULL, points DOUBLE PRECISION NOT NULL, nice_time VARCHAR(255) NOT NULL, INDEX IDX_36396FC82ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE score (id INT AUTO_INCREMENT NOT NULL, prediction_id INT DEFAULT NULL, goal_id INT DEFAULT NULL, reason INT NOT NULL, points NUMERIC(10, 0) NOT NULL, INDEX IDX_32993751449DFD9E (prediction_id), INDEX IDX_32993751667D1AFE (goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2E2ABEACD6 FOREIGN KEY (match_id) REFERENCES `match` (id)');
        $this->addSql('ALTER TABLE `match` ADD CONSTRAINT FK_7A5BC5054EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE pint ADD CONSTRAINT FK_A16B7C232ABEACD6 FOREIGN KEY (match_id) REFERENCES `match` (id)');
        $this->addSql('ALTER TABLE prediction ADD CONSTRAINT FK_36396FC82ABEACD6 FOREIGN KEY (match_id) REFERENCES `match` (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751449DFD9E FOREIGN KEY (prediction_id) REFERENCES prediction (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751667D1AFE');
        $this->addSql('ALTER TABLE goal DROP FOREIGN KEY FK_FCDCEB2E2ABEACD6');
        $this->addSql('ALTER TABLE pint DROP FOREIGN KEY FK_A16B7C232ABEACD6');
        $this->addSql('ALTER TABLE prediction DROP FOREIGN KEY FK_36396FC82ABEACD6');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751449DFD9E');
        $this->addSql('ALTER TABLE `match` DROP FOREIGN KEY FK_7A5BC5054EC001D1');
        $this->addSql('DROP TABLE goal');
        $this->addSql('DROP TABLE `match`');
        $this->addSql('DROP TABLE pint');
        $this->addSql('DROP TABLE prediction');
        $this->addSql('DROP TABLE score');
        $this->addSql('DROP TABLE season');
    }
}
