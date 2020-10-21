<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201021144211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move reset to match';
    }

    public function up(Schema $schema): void
    {
        $match = $schema->getTable('match');
        $match->addColumn('reset', 'boolean', ['default' => false]);
        $match->addUniqueIndex(['date'], 'index_unique_date');
        $prediction = $schema->getTable('prediction');
        $prediction->dropColumn('reset');
    }

    public function down(Schema $schema): void
    {
        $match = $schema->getTable('match');
        $match->dropColumn('reset');
        $match->dropIndex('index_unique_date');
        $prediction = $schema->getTable('prediction');
        $prediction->addColumn('reset', 'boolean', ['default' => false]);
    }
}
