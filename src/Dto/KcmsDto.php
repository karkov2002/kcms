<?php

namespace Karkov\Kcms\Dto;

use Karkov\Kcms\Entity\Page;

class KcmsDto
{
    private $requestDto;
    private $page;
    private $zones;
    private $js;

    public function getRequestDto(): ?RequestDto
    {
        return $this->requestDto;
    }

    public function setRequestDto(RequestDto $requestDto): self
    {
        $this->requestDto = $requestDto;

        return $this;
    }

    public function getZones(): array
    {
        return $this->zones;
    }

    public function setZones(array $zones): self
    {
        $this->zones = $zones;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function setJs(string $js): self
    {
        $this->js = $js;

        return $this;
    }

    public function getJs(): ?string
    {
        return $this->js;
    }
}
