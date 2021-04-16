<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Karkov\Kcms\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version0 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    const ADMIN = [
        'firstname' => 'Admin',
        'email' => 'admin@kcms.com',
        'password' => '$2y$13$MgSOp93VS8i3/e9fYc6Qf.5ZEEEvigwCP0aSoLwejspXa422t1Yv.', //admin
        'roles' => ['ROLE_ADMIN_KCMS', 'ROLE_ADMIN'],
    ];

    public function getDescription(): string
    {
        return 'Generate all Kcms tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE kcms_page (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page_site (page_id INT NOT NULL, site_id INT NOT NULL, INDEX IDX_30CBFED2C4663E4 (page_id), INDEX IDX_30CBFED2F6BD1646 (site_id), PRIMARY KEY(page_id, site_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page_content (id INT AUTO_INCREMENT NOT NULL, local VARCHAR(255) DEFAULT NULL, module VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page_slug (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, local VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_C1156C54C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_page_zone (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, zone INT NOT NULL, rank INT NOT NULL, date_start DATETIME DEFAULT NULL, date_end DATETIME DEFAULT NULL, INDEX IDX_F9633731C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_site (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) DEFAULT NULL, is_enable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcms_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(100) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1718AF8AE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE kcms_page_site ADD CONSTRAINT FK_30CBFED2C4663E4 FOREIGN KEY (page_id) REFERENCES kcms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kcms_page_site ADD CONSTRAINT FK_30CBFED2F6BD1646 FOREIGN KEY (site_id) REFERENCES kcms_site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kcms_page_slug ADD CONSTRAINT FK_C1156C54C4663E4 FOREIGN KEY (page_id) REFERENCES kcms_page (id)');
        $this->addSql('ALTER TABLE kcms_page_zone ADD CONSTRAINT FK_F9633731C4663E4 FOREIGN KEY (page_id) REFERENCES kcms_page (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kcms_page_site DROP FOREIGN KEY FK_30CBFED2C4663E4');
        $this->addSql('ALTER TABLE kcms_page_slug DROP FOREIGN KEY FK_C1156C54C4663E4');
        $this->addSql('ALTER TABLE kcms_page_zone DROP FOREIGN KEY FK_F9633731C4663E4');
        $this->addSql('ALTER TABLE kcms_page_site DROP FOREIGN KEY FK_30CBFED2F6BD1646');
        $this->addSql('DROP TABLE kcms_page');
        $this->addSql('DROP TABLE kcms_page_site');
        $this->addSql('DROP TABLE kcms_page_content');
        $this->addSql('DROP TABLE kcms_page_slug');
        $this->addSql('DROP TABLE kcms_page_zone');
        $this->addSql('DROP TABLE kcms_site');
        $this->addSql('DROP TABLE kcms_user');
    }

    public function postUp(Schema $schema): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $user = new User();
        $user
            ->setFirstname(self::ADMIN['firstname'])
            ->setEmail(self::ADMIN['email'])
            ->setRoles(self::ADMIN['roles'])
            ->setSalt('')
            ->setPassword(self::ADMIN['password'])
        ;

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
