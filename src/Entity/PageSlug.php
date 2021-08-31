<?php

namespace Karkov\Kcms\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Karkov\Kcms\Repository\PageSlugRepository;

/**
 * @ORM\Entity(repositoryClass=PageSlugRepository::class)
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="slug_unique", columns={"slug","local"})})
 */
class PageSlug
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="pageSlugs", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $local;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var array
     */
    private $routeAttributes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;
        if (null !== $page) {
            $this->page->addPageSlug($this);
        }

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(?string $local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function setRouteAttributes(array $routeAttributes): self
    {
        $this->routeAttributes = $routeAttributes;

        return $this;
    }

    public function getRouteAttributes(): array
    {
        return $this->routeAttributes;
    }

    public function __toString()
    {
        return $this->local.$this->slug;
    }
}
