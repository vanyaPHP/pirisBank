<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211131534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account CHANGE debit_value debit_value NUMERIC(30, 5) NOT NULL, CHANGE credit_value credit_value NUMERIC(30, 5) NOT NULL, CHANGE balance balance NUMERIC(30, 5) NOT NULL');
        $this->addSql('ALTER TABLE deposit CHANGE amount amount NUMERIC(30, 5) NOT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE amount amount NUMERIC(30, 5) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction CHANGE amount amount NUMERIC(30, 2) NOT NULL');
        $this->addSql('ALTER TABLE account CHANGE debit_value debit_value NUMERIC(30, 2) NOT NULL, CHANGE credit_value credit_value NUMERIC(30, 2) NOT NULL, CHANGE balance balance NUMERIC(30, 2) NOT NULL');
        $this->addSql('ALTER TABLE deposit CHANGE amount amount NUMERIC(30, 2) NOT NULL');
    }
}
