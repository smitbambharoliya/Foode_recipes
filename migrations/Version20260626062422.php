<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260626062422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, base_quantity INT NOT NULL, unit VARCHAR(50) NOT NULL, recipe_id INT DEFAULT NULL, INDEX IDX_6BAF787059D8A214 (recipe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, instructions LONGTEXT NOT NULL, base_servings INT NOT NULL, chef_id INT NOT NULL, INDEX IDX_DA88B137150A48F1 (chef_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone BIGINT NOT NULL, gender VARCHAR(255) NOT NULL, age VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF787059D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137150A48F1 FOREIGN KEY (chef_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF787059D8A214');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137150A48F1');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
