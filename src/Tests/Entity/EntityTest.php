<?php

namespace Karkov\Kcms\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageContent;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Entity\Site;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Modules\TextModule;
use Karkov\Kcms\Service\Helper\DateTimer;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    private $site01;
    private $site02;
    private $pageSlug01;
    private $pageSlug02;
    private $parentPage;
    private $page;
    private $parentContent;
    private $content01;
    private $content02;
    private $contentLocal;
    private $pageContent01;
    private $pageContent02;
    private $pageContent03;
    private $rootTreeContent;

    public function setUp(): void
    {
        // Given
        $this->site01 = new Site();
        $this->site01
            ->setDomain('domain.net')
            ->setIsEnable(true)
        ;

        $this->site02 = new Site();
        $this->site02
            ->setDomain('website.org')
            ->setIsEnable(true)
        ;

        $this->pageSlug01 = new PageSlug();
        $this->pageSlug01
            ->setSlug('/une-url')
            ->setLocal('fr_FR')
        ;

        $this->pageSlug02 = new PageSlug();
        $this->pageSlug02
            ->setSlug('/a-slug')
            ->setLocal('en_UK')
        ;

        $this->parentPage = new Tree();
        $this->parentPage
            ->setName('root')
            ->setId(1)
            ->setType('page')
        ;

        $this->page = new Page();
        $this->page
            ->setTitle('a page')
            ->setTemplate('a_twig_template.html.twig')
            ->setParent($this->parentPage)
            ->addSite($this->site01)
            ->addSite($this->site02)
            ->addPageSlug($this->pageSlug01)
            ->addPageSlug($this->pageSlug02)
        ;

        $this->parentPage->addPage($this->page);

        $this->rootTreeContent = new Tree();
        $this->rootTreeContent
            ->setName('root')
            ->setId(2)
            ->setType('content')
        ;

        $this->parentContent = new Tree();
        $this->parentContent
            ->setName('parent content')
            ->setId(3)
            ->setType('content')
            ->setParent($this->rootTreeContent)
        ;
        $this->rootTreeContent->addChild($this->parentContent);

        $this->content01 = new Content();
        $this->content01
            ->setTitle('a content')
            ->setModule(TextModule::class)
            ->setParent($this->parentContent)
        ;

        $this->content02 = new Content();
        $this->content02
            ->setTitle('another content')
            ->setModule(TextModule::class)
            ->setParent($this->parentContent)
        ;

        $this->parentContent->addContent($this->content01);
        $this->parentContent->addContent($this->content02);

        $this->contentLocal = new ContentLocal();
        $this->contentLocal
            ->setLocal('fr_FR')
            ->setRawContent('<p>hello</p>')
            ->setContent($this->content01)
        ;
        $this->content01->addContentLocal($this->contentLocal);

        $this->pageContent01 = new PageContent();
        $this->pageContent01
            ->setContent($this->content01)
            ->setZone(5)
            ->setRank(1)
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
        ;

        $this->pageContent02 = new PageContent();
        $this->pageContent02
            ->setContent($this->content02)
            ->setZone(5)
            ->setRank(2)
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
        ;

        $this->pageContent03 = new PageContent();
        $this->pageContent03
            ->setContent($this->content01)
            ->setZone(6)
            ->setRank(1)
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
        ;

        $this->content01->addPageContent($this->pageContent01);
        $this->page->addPageContent($this->pageContent01);

        $this->content02->addPageContent($this->pageContent02);
        $this->page->addPageContent($this->pageContent02);

        $this->content01->addPageContent($this->pageContent03);
        $this->page->addPageContent($this->pageContent03);
    }

    public function testPageContentIsCorrectlyPopulated()
    {
        // Given setUp

        // Then
        $expectedPageContent = new ArrayCollection();
        $expectedPageContent->add($this->pageContent01);
        $expectedPageContent->add($this->pageContent02);
        $this->assertEquals($expectedPageContent, $this->page->getValidPageContentsPerZone(5));
        $this->assertEquals(new ArrayCollection(), $this->page->getValidPageContentsPerZone(4));

        $this->assertEquals('Page #a page - Zone #5 - Rank #1', (string) $this->pageContent01);
        $this->assertEquals(null, $this->pageContent01->getId());
    }

    public function testContentLocalIsCorrectlyPopulated()
    {
        // Given setUp

        // Then
        $this->assertNull($this->contentLocal->getId());
        $this->assertEquals('fr_FR', $this->contentLocal->getLocal());
    }

    public function testPageIsCorrectlyPopulated()
    {
        // Given setUp

        // Then
        $expectedSites = new ArrayCollection();
        $expectedSites->add($this->site01);
        $expectedSites->add($this->site02);
        $this->assertEquals($expectedSites, $this->page->getSites());

        $this->assertEquals('a_twig_template.html.twig', $this->page->getTemplate());

        $expectedPageContent = new ArrayCollection();
        $expectedPageContent->add($this->pageContent01);
        $expectedPageContent->add($this->pageContent02);
        $expectedPageContent->add($this->pageContent03);

        $this->assertEquals($expectedPageContent, $this->page->getValidPageContents());

        $this->assertEquals('a page', (string) $this->page);
    }

    public function testPageSlugIsCorrectlyPopulated()
    {
        // Given setUp

        //Then
        $expectedSlugs = new ArrayCollection();
        $expectedSlugs->add($this->pageSlug01);
        $expectedSlugs->add($this->pageSlug02);
        $this->assertEquals($expectedSlugs, $this->page->getPageSlugs());

        $this->assertEquals('fr_FR', $this->pageSlug01->getLocal());
        $this->assertEquals('en_UK', $this->pageSlug02->getLocal());
        $this->assertNull($this->pageSlug02->getId());
        $this->assertEquals('fr_FR/une-url', (string) $this->pageSlug01);
    }

    public function testSiteIsCorrectlyPopulated()
    {
        $this->assertNull($this->site01->getId());

        $expectedSites = new ArrayCollection();
        $expectedSites->add($this->page);
        $this->assertEquals($expectedSites, $this->site01->getPages());
        $this->assertEquals('domain.net', (string) $this->site01);
    }

    public function testTreeIsCorrectlyPopulated()
    {
        // Given setUp

        // Then
        $this->assertEquals('content', $this->parentContent->getType());
        $this->assertEquals('parent content', $this->parentContent->getName());

        $this->assertEquals(new ArrayCollection(), $this->parentContent->getPages());
        $this->assertEquals(new ArrayCollection(), $this->parentPage->getContents());

        $expectedPages = new ArrayCollection();
        $expectedPages->add($this->page);
        $this->assertEquals($expectedPages, $this->parentPage->getPages());

        $this->assertEquals('content::parent content', (string) $this->parentContent);
        $this->assertEquals('page::root', (string) $this->parentPage);
    }

    public function testContentIsCorrectlyPopulated()
    {
        $this->assertEquals('a content', (string) $this->content01);

        $expectedPageContent = new ArrayCollection();
        $expectedPageContent->add($this->pageContent01);
        $expectedPageContent->add($this->pageContent03);

        $this->assertEquals($expectedPageContent, $this->content01->getValidPageContents());
        $this->assertNull($this->content01->getRenderedContent());
    }

    public function testRemoveContentFromTree()
    {
        // Given setUp

        // When
        $this->parentContent->removeContent($this->content01);

        // Then
        $expectedContents = new ArrayCollection();
        $expectedContents->add($this->content02);

        $this->assertEquals(count($expectedContents), count($this->parentContent->getContents()));
        $this->assertEquals($expectedContents->first(), $this->parentContent->getContents()->first());
    }

    public function testRemovePageFromTree()
    {
        // Given setUp

        // When
        $this->parentPage->removePage($this->page);

        // Then
        $this->assertEquals(new ArrayCollection(), $this->parentPage->getPages());
    }

    public function testRemoveChildFromTree()
    {
        // Given setUp

        // When
        $this->rootTreeContent->removeChild($this->parentContent);

        // Then
        $this->assertEquals(new ArrayCollection(), $this->rootTreeContent->getChildren());
    }

    public function testRemoveSiteFromPage()
    {
        // Given setUp

        // When
        $this->page->removeSite($this->site01);

        // Then
        $expectedSites = new ArrayCollection();
        $expectedSites->add($this->site02);
        $this->assertEquals(count($expectedSites), count($this->page->getSites()));
        $this->assertEquals($expectedSites->first(), $this->page->getSites()->first());
    }

    public function testRemovePageSlugFromPage()
    {
        // Given setUp

        // When
        $this->page->removePageSlug($this->pageSlug01);

        // Then
        $expectedSlugs = new ArrayCollection();
        $expectedSlugs->add($this->pageSlug02);
        $this->assertEquals(count($expectedSlugs), count($this->page->getPageSlugs()));
        $this->assertEquals($expectedSlugs->first(), $this->page->getPageSlugs()->first());
    }

    public function testRemovePageContent()
    {
        // Given setUp

        // When
        $this->page->removePageContent($this->pageContent02);

        // Then
        $this->assertEquals([$this->pageContent01], $this->page->getPageContentsByZone(5));
        $this->assertEquals([$this->pageContent03], $this->page->getPageContentsByZone(6));
    }

    public function testRemovePageFromSite()
    {
        // Given setUp

        // When
        $this->site02->removePage($this->page);

        // Then
        $this->assertEquals(new ArrayCollection(), $this->site02->getPages());
    }

    public function testRemovePageContentFromContent()
    {
        // Given setUp

        // When
        $this->content01->removePageContent($this->pageContent01);

        // Then
        $expectedPageContents = new ArrayCollection();
        $expectedPageContents->add($this->pageContent03);
        $this->assertEquals(count($expectedPageContents), count($this->content01->getPageContents()));
        $this->assertEquals($expectedPageContents->first(), $this->content01->getPageContents()->first());
    }

    public function testRemoveContentLocalFromContent()
    {
        // Given setUp

        // When
        $this->content01->removeContentLocal($this->contentLocal);

        // Then
        $this->assertEquals(new ArrayCollection(), $this->content01->getContentLocals());
    }
}
