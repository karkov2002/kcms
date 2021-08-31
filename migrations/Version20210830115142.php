<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Karkov\Kcms\Entity\Tree;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20210830115142 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription() : string
    {
        return 'Create all Kcms tables';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE kcms_content (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, title VARCHAR(255) NOT NULL, module VARCHAR(255) NOT NULL, INDEX IDX_E9BB803D727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_content_local (id INT AUTO_INCREMENT NOT NULL, content_id INT NOT NULL, html_pattern_id INT DEFAULT NULL, local VARCHAR(255) DEFAULT NULL, raw_content LONGTEXT DEFAULT NULL, INDEX IDX_64ACE22184A0A3ED (content_id), INDEX IDX_64ACE22132D8CEEF (html_pattern_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_html_pattern (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, title VARCHAR(255) NOT NULL, pattern LONGTEXT DEFAULT NULL, INDEX IDX_55F0CC84727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, template VARCHAR(255) NOT NULL, INDEX IDX_8E81CFE3727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page_site (page_id INT NOT NULL, site_id INT NOT NULL, INDEX IDX_30CBFED2C4663E4 (page_id), INDEX IDX_30CBFED2F6BD1646 (site_id), PRIMARY KEY(page_id, site_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page_content (id INT AUTO_INCREMENT NOT NULL, content_id INT DEFAULT NULL, page_id INT NOT NULL, zone INT NOT NULL, rank INT NOT NULL, date_start DATETIME DEFAULT NULL, date_end DATETIME DEFAULT NULL, INDEX IDX_66BDD5BA84A0A3ED (content_id), INDEX IDX_66BDD5BAC4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page_slug (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, local VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_C1156C54C4663E4 (page_id), UNIQUE INDEX slug_unique (slug, local), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_site (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) DEFAULT NULL, is_enable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_tree (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2DB5271F727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(100) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE kcms_content ADD CONSTRAINT FK_E9BB803D727ACA70 FOREIGN KEY (parent_id) REFERENCES kcms_tree (id)');
        $this->addSql('ALTER TABLE kcms_content_local ADD CONSTRAINT FK_64ACE22184A0A3ED FOREIGN KEY (content_id) REFERENCES kcms_content (id)');
        $this->addSql('ALTER TABLE kcms_content_local ADD CONSTRAINT FK_64ACE22132D8CEEF FOREIGN KEY (html_pattern_id) REFERENCES kcms_html_pattern (id)');
        $this->addSql('ALTER TABLE kcms_html_pattern ADD CONSTRAINT FK_55F0CC84727ACA70 FOREIGN KEY (parent_id) REFERENCES kcms_tree (id)');
        $this->addSql('ALTER TABLE kcms_page ADD CONSTRAINT FK_8E81CFE3727ACA70 FOREIGN KEY (parent_id) REFERENCES kcms_tree (id)');
        $this->addSql('ALTER TABLE kcms_page_site ADD CONSTRAINT FK_30CBFED2C4663E4 FOREIGN KEY (page_id) REFERENCES kcms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kcms_page_site ADD CONSTRAINT FK_30CBFED2F6BD1646 FOREIGN KEY (site_id) REFERENCES kcms_site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kcms_page_content ADD CONSTRAINT FK_66BDD5BA84A0A3ED FOREIGN KEY (content_id) REFERENCES kcms_content (id)');
        $this->addSql('ALTER TABLE kcms_page_content ADD CONSTRAINT FK_66BDD5BAC4663E4 FOREIGN KEY (page_id) REFERENCES kcms_page (id)');
        $this->addSql('ALTER TABLE kcms_page_slug ADD CONSTRAINT FK_C1156C54C4663E4 FOREIGN KEY (page_id) REFERENCES kcms_page (id)');
        $this->addSql('ALTER TABLE kcms_tree ADD CONSTRAINT FK_2DB5271F727ACA70 FOREIGN KEY (parent_id) REFERENCES kcms_tree (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE kcms_content_local DROP FOREIGN KEY FK_64ACE22184A0A3ED');
        $this->addSql('ALTER TABLE kcms_page_content DROP FOREIGN KEY FK_66BDD5BA84A0A3ED');
        $this->addSql('ALTER TABLE kcms_content_local DROP FOREIGN KEY FK_64ACE22132D8CEEF');
        $this->addSql('ALTER TABLE kcms_page_site DROP FOREIGN KEY FK_30CBFED2C4663E4');
        $this->addSql('ALTER TABLE kcms_page_content DROP FOREIGN KEY FK_66BDD5BAC4663E4');
        $this->addSql('ALTER TABLE kcms_page_slug DROP FOREIGN KEY FK_C1156C54C4663E4');
        $this->addSql('ALTER TABLE kcms_page_site DROP FOREIGN KEY FK_30CBFED2F6BD1646');
        $this->addSql('ALTER TABLE kcms_content DROP FOREIGN KEY FK_E9BB803D727ACA70');
        $this->addSql('ALTER TABLE kcms_html_pattern DROP FOREIGN KEY FK_55F0CC84727ACA70');
        $this->addSql('ALTER TABLE kcms_page DROP FOREIGN KEY FK_8E81CFE3727ACA70');
        $this->addSql('ALTER TABLE kcms_tree DROP FOREIGN KEY FK_2DB5271F727ACA70');
        $this->addSql('DROP TABLE kcms_content');
        $this->addSql('DROP TABLE kcms_content_local');
        $this->addSql('DROP TABLE kcms_html_pattern');
        $this->addSql('DROP TABLE kcms_page');
        $this->addSql('DROP TABLE kcms_page_site');
        $this->addSql('DROP TABLE kcms_page_content');
        $this->addSql('DROP TABLE kcms_page_slug');
        $this->addSql('DROP TABLE kcms_site');
        $this->addSql('DROP TABLE kcms_tree');
        $this->addSql('DROP TABLE user');
    }

    public function postUp(Schema $schema): void
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $contentRootTree = new Tree();
        $contentRootTree
            ->setType('content')
            ->setName('root')
        ;

        $pageRootTree = new Tree();
        $pageRootTree
            ->setType('page')
            ->setName('root')
        ;

        $patternhtmlRootTree = new Tree();
        $patternhtmlRootTree
            ->setType('patternhtml')
            ->setName('root')
        ;

        $entityManager->persist($contentRootTree);
        $entityManager->persist($pageRootTree);
        $entityManager->persist($patternhtmlRootTree);

        $entityManager->flush();
    }
}
