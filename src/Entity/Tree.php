<?php

namespace Karkov\Kcms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Karkov\Kcms\Repository\TreeRepository;

/**
 * @ORM\Entity(repositoryClass=TreeRepository::class)
 */
class Tree
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Tree::class, inversedBy="children")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Tree::class, mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity=Content::class, mappedBy="parent")
     */
    private $contents;

    /**
     * @ORM\OneToMany(targetEntity=Page::class, mappedBy="parent")
     */
    private $pages;

    /**
     * @ORM\OneToMany(targetEntity=HtmlPattern::class, mappedBy="parent")
     */
    private $htmlPatterns;

    public function __construct()
    {
        $this->contents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->htmlPatterns = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection|Content[]
     */
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(Content $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setParent($this);
        }

        return $this;
    }

    public function removeContent(Content $content): self
    {
        if ($this->contents->removeElement($content)) {
            if ($content->getParent() === $this) {
                $content->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setParent($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            if ($page->getParent() === $this) {
                $page->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tree[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Tree $tree): self
    {
        if (!$this->children->contains($tree)) {
            $this->children[] = $tree;
            $tree->setParent($this);
        }

        return $this;
    }

    public function removeChild(Tree $tree): self
    {
        if ($this->children->removeElement($tree)) {
            if ($tree->getParent() === $this) {
                $tree->setParent(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getType().'::'.$this->getName();
    }

    /**
     * @return Collection|HtmlPattern[]
     */
    public function getHtmlPatterns(): Collection
    {
        return $this->htmlPatterns;
    }

    public function addHtmlPattern(HtmlPattern $composedContentHtmlPattern): self
    {
        if (!$this->htmlPatterns->contains($composedContentHtmlPattern)) {
            $this->htmlPatterns[] = $composedContentHtmlPattern;
            $composedContentHtmlPattern->setParent($this);
        }

        return $this;
    }

    public function removeHtmlPattern(HtmlPattern $composedContentHtmlPattern): self
    {
        if ($this->htmlPatterns->removeElement($composedContentHtmlPattern)) {
            if ($composedContentHtmlPattern->getParent() === $this) {
                $composedContentHtmlPattern->setParent(null);
            }
        }

        return $this;
    }
}
