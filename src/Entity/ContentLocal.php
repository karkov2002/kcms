<?php

namespace Karkov\Kcms\Entity;

use Doctrine\ORM\Mapping as ORM;
use Karkov\Kcms\Repository\ContentLocalRepository;

/**
 * @ORM\Entity(repositoryClass=ContentLocalRepository::class)
 */
class ContentLocal
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
    private $local;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rawContent;

    /**
     * @ORM\ManyToOne(targetEntity=Content::class, inversedBy="contentLocals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=HtmlPattern::class, inversedBy="contentLocals")
     */
    private $htmlPattern;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRawContent(): ?string
    {
        return $this->rawContent;
    }

    public function setRawContent(?string $rawContent): self
    {
        $this->rawContent = $rawContent;

        return $this;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
    }

    public function getHtmlPattern(): ?HtmlPattern
    {
        return $this->htmlPattern;
    }

    public function setHtmlPattern(?HtmlPattern $htmlPattern): self
    {
        $this->htmlPattern = $htmlPattern;

        return $this;
    }
}
