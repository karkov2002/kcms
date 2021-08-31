<?php

namespace Karkov\Kcms\Dto;

use Karkov\Kcms\Entity\PageSlug;
use Symfony\Component\HttpFoundation\Request;

class RequestDto
{
    private $host;
    private $local;
    private $request;
    private $pageSlug;

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

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

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function setPageSlug(?PageSlug $pageSlug): self
    {
        $this->pageSlug = $pageSlug;

        return $this;
    }

    public function getPageSlug(): ?PageSlug
    {
        return $this->pageSlug;
    }

    public function __toString()
    {
        return sprintf('host : %s, local : %s, slug : %s', $this->host, $this->local, $this->pageSlug ? $this->pageSlug->getSlug() : '');
    }
}
