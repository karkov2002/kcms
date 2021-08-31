<?php

namespace Karkov\Kcms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Karkov\Kcms\Repository\HtmlPatternRepository;

/**
 * @ORM\Entity(repositoryClass=HtmlPatternRepository::class)
 */
class HtmlPattern
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
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pattern;

    /**
     * @ORM\ManyToOne(targetEntity=Tree::class, inversedBy="htmlPatterns")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=ContentLocal::class, mappedBy="htmlPattern")
     */
    private $contentLocals;

    public function __construct()
    {
        $this->contentLocals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function setPattern(?string $pattern): self
    {
        $this->pattern = $pattern;

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
     * @return Collection|ContentLocal[]
     */
    public function getContentLocals(): Collection
    {
        return $this->contentLocals;
    }

    public function addContentLocal(ContentLocal $contentLocal): self
    {
        if (!$this->contentLocals->contains($contentLocal)) {
            $this->contentLocals[] = $contentLocal;
            $contentLocal->setHtmlPattern($this);
        }

        return $this;
    }

    public function removeContentLocal(ContentLocal $contentLocal): self
    {
        if ($this->contentLocals->removeElement($contentLocal)) {
            if ($contentLocal->getHtmlPattern() === $this) {
                $contentLocal->setHtmlPattern(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
