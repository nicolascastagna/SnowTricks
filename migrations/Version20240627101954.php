<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627101954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick CHANGE category_id category_id INT DEFAULT NULL');
        $result = $this->connection->executeQuery("SELECT COUNT(*) FROM category");
        $categoryCount = $result->fetchOne();

        // if no row exists, insert the categories
        if ($categoryCount == 0) {
            $this->addSql("INSERT INTO category (name) VALUES 
                ('Grabs'), ('Spins'), ('Flips'), ('Slides'), 
                ('Straight Airs'), ('Tweaks et variations'), ('Stalls'), 
                ('Inverted hand plants'), ('Autres'), ('Freestyle')");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick CHANGE category_id category_id INT NOT NULL');
    }
}
