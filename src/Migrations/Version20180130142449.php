<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180130142449 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE chat (id INTEGER NOT NULL, sender_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_659DF2AAF624B39D ON chat (sender_id)');
        $this->addSql('CREATE INDEX IDX_659DF2AAE92F8F78 ON chat (recipient_id)');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL, datetime DATETIME NOT NULL, text CLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE TABLE comment_hate (id INTEGER NOT NULL, comment_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C67E2A2EF8697D13 ON comment_hate (comment_id)');
        $this->addSql('CREATE INDEX IDX_C67E2A2EA76ED395 ON comment_hate (user_id)');
        $this->addSql('CREATE TABLE enemy (id INTEGER NOT NULL, initiator_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, is_accepted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB9F5AA97DB3B714 ON enemy (initiator_id)');
        $this->addSql('CREATE INDEX IDX_FB9F5AA9E92F8F78 ON enemy (recipient_id)');
        $this->addSql('CREATE TABLE file (id INTEGER NOT NULL, extension VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE post (id INTEGER NOT NULL, user_id INTEGER NOT NULL, datetime DATETIME NOT NULL, text CLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('CREATE TABLE post_file (id INTEGER NOT NULL, post_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_45CA511B4B89032C ON post_file (post_id)');
        $this->addSql('CREATE INDEX IDX_45CA511B93CB796C ON post_file (file_id)');
        $this->addSql('CREATE TABLE post_hate (id INTEGER NOT NULL, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_291DEFC94B89032C ON post_hate (post_id)');
        $this->addSql('CREATE INDEX IDX_291DEFC9A76ED395 ON post_hate (user_id)');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, picture_id INTEGER NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birthday DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE45BDBF ON user (picture_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE comment_hate');
        $this->addSql('DROP TABLE enemy');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_file');
        $this->addSql('DROP TABLE post_hate');
        $this->addSql('DROP TABLE user');
    }
}
