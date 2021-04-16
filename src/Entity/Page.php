<?php

namespace Karkov\Kcms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Karkov\Kcms\Repository\PageRepository;

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
     * @ORM\ManyToMany(targetEntity=Site::class, inversedBy="pages")
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity=PageSlug::class, mappedBy="page", orphanRemoval=true)
     */
    private $pageSlugs;

    /**
     * @ORM\OneToMany(targetEntity=PageZone::class, mappedBy="page", orphanRemoval=true)
     */
    private $pageZones;

    public function __construct()
    {
        $this->site = new ArrayCollection();
        $this->slugs = new ArrayCollection();
        $this->pageZones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Site[]
     */
    public function getSite(): Collection
    {
        return $this->site;
    }

    public function addSite(Site $site): self
    {
        if (!$this->site->contains($site)) {
            $this->site[] = $site;
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        $this->site->removeElement($site);

        return $this;
    }

    /**
     * @return Collection|PageSlug[]
     */
    public function getSlugs(): Collection
    {
        return $this->slugs;
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
            // set the owning side to null (unless already changed)
            if ($pageSlug->getPage() === $this) {
                $pageSlug->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PageZone[]
     */
    public function getPageZones(): Collection
    {
        return $this->pageZones;
    }

    public function addPageZone(PageZone $pageZone): self
    {
        if (!$this->pageZones->contains($pageZone)) {
            $this->pageZones[] = $pageZone;
            $pageZone->setPage($this);
        }

        return $this;
    }

    public function removePageZone(PageZone $pageZone): self
    {
        if ($this->pageZones->removeElement($pageZone)) {
            // set the owning side to null (unless already changed)
            if ($pageZone->getPage() === $this) {
                $pageZone->setPage(null);
            }
        }

        return $this;
    }
}
