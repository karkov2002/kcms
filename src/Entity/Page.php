<?php

namespace Karkov\Kcms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Karkov\Kcms\Repository\PageRepository;
use Karkov\Kcms\Service\Helper\DateTimer;

/**
 * @ORM\Entity(repositoryClass=PageRepository::class)
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $template;

    /**
     * @ORM\ManyToMany(targetEntity=Site::class, inversedBy="pages")
     */
    private $sites;

    /**
     * @ORM\OneToMany(targetEntity=PageSlug::class, mappedBy="page", orphanRemoval=true)
     */
    private $pageSlugs;

    /**
     * @ORM\OneToMany(targetEntity=PageContent::class, mappedBy="page", orphanRemoval=true, fetch="EAGER")
     */
    private $pageContents;

    /**
     * @ORM\ManyToOne(targetEntity=Tree::class, inversedBy="pages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
        $this->pageSlugs = new ArrayCollection();
        $this->pageContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Site[]
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->addPage($this);
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        $this->sites->removeElement($site);

        return $this;
    }

    /**
     * @return Collection|PageSlug[]
     */
    public function getPageSlugs(): Collection
    {
        return $this->pageSlugs;
    }

    public function addPageSlug(PageSlug $pageSlug): self
    {
        if (!$this->pageSlugs->contains($pageSlug)) {
            $this->pageSlugs[] = $pageSlug;
            $pageSlug->setPage($this);
        }

        return $this;
    }

    public function removePageSlug(PageSlug $pageSlug): self
    {
        if ($this->pageSlugs->removeElement($pageSlug)) {
            if ($pageSlug->getPage() === $this) {
                $pageSlug->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PageContent[]
     */
    public function getPageContents(): Collection
    {
        $criteria = Criteria::create();
        $criteria->orderBy(['zone' => Criteria::ASC, 'rank' => Criteria::ASC]);

        return $this->pageContents->matching($criteria);
    }

    /**
     * @return Collection|PageContent[]
     */
    public function getValidPageContents(): Collection
    {
        $now = (new DateTimer())->get();

        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->lte('date_start', $now));
        $criteria->orWhere(Criteria::expr()->isNull('date_start'));
        $criteria->andWhere(Criteria::expr()->gte('date_end', $now));
        $criteria->orderBy(['zone' => Criteria::ASC, 'rank' => Criteria::ASC]);

        return $this->pageContents->matching($criteria);
    }

    /**
     * @return Collection|PageContent[]
     */
    public function getValidPageContentsPerZone(int $zone): Collection
    {
        $now = (new DateTimer())->get();

        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('zone', $zone));
        $criteria->andWhere(Criteria::expr()->lte('date_start', $now));
        $criteria->orWhere(Criteria::expr()->isNull('date_start'));
        $criteria->andWhere(Criteria::expr()->gte('date_end', $now));
        $criteria->orderBy(['rank' => Criteria::ASC]);

        return $this->pageContents->matching($criteria);
    }

    /**
     * @return array|PageContent[]
     */
    public function getPageContentsByZone(int $zone): array
    {
        $pageContentsByZone = [];
        $criteria = Criteria::create();
        $criteria->orderBy(['zone' => Criteria::ASC, 'rank' => Criteria::ASC]);

        /** @var PageContent $pageContent */
        foreach ($this->pageContents->matching($criteria) as $pageContent) {
            if ($pageContent->getZone() === $zone) {
                $pageContentsByZone[] = $pageContent;
            }
        }

        return $pageContentsByZone;
    }

    public function addPageContent(PageContent $pageContent): self
    {
        if (!$this->pageContents->contains($pageContent)) {
            $this->pageContents[] = $pageContent;
            $pageContent->setPage($this);
        }

        return $this;
    }

    public function removePageContent(PageContent $pageContent): self
    {
        if ($this->pageContents->removeElement($pageContent)) {
            if ($pageContent->getPage() === $this) {
                $pageContent->setPage(null);
            }
        }

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getParent(): ?Tree
    {
        return $this->parent;
    }

    public function setParent(?Tree $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
