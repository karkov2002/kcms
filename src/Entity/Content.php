<?php

namespace Karkov\Kcms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Karkov\Kcms\Repository\ContentRepository;
use Karkov\Kcms\Service\Helper\DateTimer;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ContentRepository::class)
 */
class Content
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"content_output"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $module;

    /**
     * @var string
     */
    private $renderedContent;

    /**
     * @ORM\OneToMany(targetEntity=PageContent::class, mappedBy="content")
     */
    private $pageContents;

    /**
     * @ORM\ManyToOne(targetEntity=Tree::class, inversedBy="contents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=ContentLocal::class, mappedBy="content", orphanRemoval=true, fetch="EAGER")
     */
    private $contentLocals;

    public function __construct()
    {
        $this->contentLocals = new ArrayCollection();
        $this->pageContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return Collection|PageContent[]|null
     */
    public function getPageContents(): ?Collection
    {
        return $this->pageContents;
    }

    /**
     * @return Collection|PageContent[]|null
     */
    public function getValidPageContents(): ?Collection
    {
        $now = (new DateTimer())->get();

        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->lte('date_start', $now));
        $criteria->orWhere(Criteria::expr()->isNull('date_start'));
        $criteria->andWhere(Criteria::expr()->gte('date_end', $now));

        return $this->pageContents->matching($criteria);
    }

    public function addPageContent(PageContent $pageContent): self
    {
        if (!$this->pageContents->contains($pageContent)) {
            $this->pageContents->add($pageContent);
            $pageContent->setContent($this);
        }

        return $this;
    }

    public function removePageContent(PageContent $pageContent): self
    {
        if ($this->pageContents->removeElement($pageContent)) {
            if ($pageContent->getContent() === $this) {
                $pageContent->setContent(null);
            }
        }

        return $this;
    }

    public function getRenderedContent(): ?string
    {
        return $this->renderedContent;
    }

    public function setRenderedContent(?string $renderedContent): self
    {
        $this->renderedContent = $renderedContent;

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

    /**
     * @return Collection|Content[]|null
     */
    public function getContentLocalsByLocal(?string $local): ?Collection
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('local', $local));

        return $this->contentLocals->matching($criteria);
    }

    public function addContentLocal(ContentLocal $contentLocal): self
    {
        if (!$this->contentLocals->contains($contentLocal)) {
            $this->contentLocals[] = $contentLocal;
            $contentLocal->setContent($this);
        }

        return $this;
    }

    public function removeContentLocal(ContentLocal $contentLocal): self
    {
        if ($this->contentLocals->removeElement($contentLocal)) {
            if ($contentLocal->getContent() === $this) {
                $contentLocal->setContent(null);
            }
        }

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
