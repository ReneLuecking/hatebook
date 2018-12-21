<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180131121235 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_659DF2AAE92F8F78');
        $this->addSql('DROP INDEX IDX_659DF2AAF624B39D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__chat AS SELECT id, sender_id, recipient_id, text, datetime FROM chat');
        $this->addSql('DROP TABLE chat');
        $this->addSql('CREATE TABLE chat (id INTEGER NOT NULL, sender_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL COLLATE BINARY, datetime DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_659DF2AAF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_659DF2AAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO chat (id, sender_id, recipient_id, text, datetime) SELECT id, sender_id, recipient_id, text, datetime FROM __temp__chat');
        $this->addSql('DROP TABLE __temp__chat');
        $this->addSql('CREATE INDEX IDX_659DF2AAE92F8F78 ON chat (recipient_id)');
        $this->addSql('CREATE INDEX IDX_659DF2AAF624B39D ON chat (sender_id)');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('DROP INDEX IDX_9474526C4B89032C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, post_id, user_id, datetime, text FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL, datetime DATETIME NOT NULL, text CLOB NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, post_id, user_id, datetime, text) SELECT id, post_id, user_id, datetime, text FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('DROP INDEX IDX_C67E2A2EA76ED395');
        $this->addSql('DROP INDEX IDX_C67E2A2EF8697D13');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment_hate AS SELECT id, comment_id, user_id FROM comment_hate');
        $this->addSql('DROP TABLE comment_hate');
        $this->addSql('CREATE TABLE comment_hate (id INTEGER NOT NULL, comment_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_C67E2A2EF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C67E2A2EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment_hate (id, comment_id, user_id) SELECT id, comment_id, user_id FROM __temp__comment_hate');
        $this->addSql('DROP TABLE __temp__comment_hate');
        $this->addSql('CREATE INDEX IDX_C67E2A2EA76ED395 ON comment_hate (user_id)');
        $this->addSql('CREATE INDEX IDX_C67E2A2EF8697D13 ON comment_hate (comment_id)');
        $this->addSql('DROP INDEX IDX_FB9F5AA9E92F8F78');
        $this->addSql('DROP INDEX IDX_FB9F5AA97DB3B714');
        $this->addSql('CREATE TEMPORARY TABLE __temp__enemy AS SELECT id, initiator_id, recipient_id, is_accepted FROM enemy');
        $this->addSql('DROP TABLE enemy');
        $this->addSql('CREATE TABLE enemy (id INTEGER NOT NULL, initiator_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, is_accepted BOOLEAN NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_FB9F5AA97DB3B714 FOREIGN KEY (initiator_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FB9F5AA9E92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO enemy (id, initiator_id, recipient_id, is_accepted) SELECT id, initiator_id, recipient_id, is_accepted FROM __temp__enemy');
        $this->addSql('DROP TABLE __temp__enemy');
        $this->addSql('CREATE INDEX IDX_FB9F5AA9E92F8F78 ON enemy (recipient_id)');
        $this->addSql('CREATE INDEX IDX_FB9F5AA97DB3B714 ON enemy (initiator_id)');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, user_id, datetime, text FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER NOT NULL, user_id INTEGER NOT NULL, datetime DATETIME NOT NULL, text CLOB NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post (id, user_id, datetime, text) SELECT id, user_id, datetime, text FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('DROP INDEX IDX_45CA511B93CB796C');
        $this->addSql('DROP INDEX IDX_45CA511B4B89032C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post_file AS SELECT id, post_id, file_id FROM post_file');
        $this->addSql('DROP TABLE post_file');
        $this->addSql('CREATE TABLE post_file (id INTEGER NOT NULL, post_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_45CA511B4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_45CA511B93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post_file (id, post_id, file_id) SELECT id, post_id, file_id FROM __temp__post_file');
        $this->addSql('DROP TABLE __temp__post_file');
        $this->addSql('CREATE INDEX IDX_45CA511B93CB796C ON post_file (file_id)');
        $this->addSql('CREATE INDEX IDX_45CA511B4B89032C ON post_file (post_id)');
        $this->addSql('DROP INDEX IDX_291DEFC9A76ED395');
        $this->addSql('DROP INDEX IDX_291DEFC94B89032C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post_hate AS SELECT id, post_id, user_id FROM post_hate');
        $this->addSql('DROP TABLE post_hate');
        $this->addSql('CREATE TABLE post_hate (id INTEGER NOT NULL, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_291DEFC94B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_291DEFC9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post_hate (id, post_id, user_id) SELECT id, post_id, user_id FROM __temp__post_hate');
        $this->addSql('DROP TABLE __temp__post_hate');
        $this->addSql('CREATE INDEX IDX_291DEFC9A76ED395 ON post_hate (user_id)');
        $this->addSql('CREATE INDEX IDX_291DEFC94B89032C ON post_hate (post_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649EE45BDBF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, picture_id, first_name, last_name, email, birthday, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, picture_id INTEGER DEFAULT NULL, first_name VARCHAR(255) NOT NULL COLLATE BINARY, last_name VARCHAR(255) NOT NULL COLLATE BINARY, email VARCHAR(255) NOT NULL COLLATE BINARY, birthday DATE NOT NULL, password VARCHAR(64) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES file (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, picture_id, first_name, last_name, email, birthday, password) SELECT id, picture_id, first_name, last_name, email, birthday, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE45BDBF ON user (picture_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_659DF2AAF624B39D');
        $this->addSql('DROP INDEX IDX_659DF2AAE92F8F78');
        $this->addSql('CREATE TEMPORARY TABLE __temp__chat AS SELECT id, sender_id, recipient_id, text, datetime FROM chat');
        $this->addSql('DROP TABLE chat');
        $this->addSql('CREATE TABLE chat (id INTEGER NOT NULL, sender_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO chat (id, sender_id, recipient_id, text, datetime) SELECT id, sender_id, recipient_id, text, datetime FROM __temp__chat');
        $this->addSql('DROP TABLE __temp__chat');
        $this->addSql('CREATE INDEX IDX_659DF2AAF624B39D ON chat (sender_id)');
        $this->addSql('CREATE INDEX IDX_659DF2AAE92F8F78 ON chat (recipient_id)');
        $this->addSql('DROP INDEX IDX_9474526C4B89032C');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, post_id, user_id, datetime, text FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL, datetime DATETIME NOT NULL, text CLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO comment (id, post_id, user_id, datetime, text) SELECT id, post_id, user_id, datetime, text FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('DROP INDEX IDX_C67E2A2EF8697D13');
        $this->addSql('DROP INDEX IDX_C67E2A2EA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment_hate AS SELECT id, comment_id, user_id FROM comment_hate');
        $this->addSql('DROP TABLE comment_hate');
        $this->addSql('CREATE TABLE comment_hate (id INTEGER NOT NULL, comment_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO comment_hate (id, comment_id, user_id) SELECT id, comment_id, user_id FROM __temp__comment_hate');
        $this->addSql('DROP TABLE __temp__comment_hate');
        $this->addSql('CREATE INDEX IDX_C67E2A2EF8697D13 ON comment_hate (comment_id)');
        $this->addSql('CREATE INDEX IDX_C67E2A2EA76ED395 ON comment_hate (user_id)');
        $this->addSql('DROP INDEX IDX_FB9F5AA97DB3B714');
        $this->addSql('DROP INDEX IDX_FB9F5AA9E92F8F78');
        $this->addSql('CREATE TEMPORARY TABLE __temp__enemy AS SELECT id, initiator_id, recipient_id, is_accepted FROM enemy');
        $this->addSql('DROP TABLE enemy');
        $this->addSql('CREATE TABLE enemy (id INTEGER NOT NULL, initiator_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, is_accepted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO enemy (id, initiator_id, recipient_id, is_accepted) SELECT id, initiator_id, recipient_id, is_accepted FROM __temp__enemy');
        $this->addSql('DROP TABLE __temp__enemy');
        $this->addSql('CREATE INDEX IDX_FB9F5AA97DB3B714 ON enemy (initiator_id)');
        $this->addSql('CREATE INDEX IDX_FB9F5AA9E92F8F78 ON enemy (recipient_id)');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, user_id, datetime, text FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER NOT NULL, user_id INTEGER NOT NULL, datetime DATETIME NOT NULL, text CLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO post (id, user_id, datetime, text) SELECT id, user_id, datetime, text FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('DROP INDEX IDX_45CA511B4B89032C');
        $this->addSql('DROP INDEX IDX_45CA511B93CB796C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post_file AS SELECT id, post_id, file_id FROM post_file');
        $this->addSql('DROP TABLE post_file');
        $this->addSql('CREATE TABLE post_file (id INTEGER NOT NULL, post_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO post_file (id, post_id, file_id) SELECT id, post_id, file_id FROM __temp__post_file');
        $this->addSql('DROP TABLE __temp__post_file');
        $this->addSql('CREATE INDEX IDX_45CA511B4B89032C ON post_file (post_id)');
        $this->addSql('CREATE INDEX IDX_45CA511B93CB796C ON post_file (file_id)');
        $this->addSql('DROP INDEX IDX_291DEFC94B89032C');
        $this->addSql('DROP INDEX IDX_291DEFC9A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post_hate AS SELECT id, post_id, user_id FROM post_hate');
        $this->addSql('DROP TABLE post_hate');
        $this->addSql('CREATE TABLE post_hate (id INTEGER NOT NULL, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO post_hate (id, post_id, user_id) SELECT id, post_id, user_id FROM __temp__post_hate');
        $this->addSql('DROP TABLE __temp__post_hate');
        $this->addSql('CREATE INDEX IDX_291DEFC94B89032C ON post_hate (post_id)');
        $this->addSql('CREATE INDEX IDX_291DEFC9A76ED395 ON post_hate (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D649EE45BDBF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, picture_id, first_name, last_name, email, password, birthday FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, picture_id INTEGER DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birthday DATE NOT NULL, password VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, picture_id, first_name, last_name, email, password, birthday) SELECT id, picture_id, first_name, last_name, email, password, birthday FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE45BDBF ON user (picture_id)');
    }
}
