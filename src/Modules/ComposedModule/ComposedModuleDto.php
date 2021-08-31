<?php

namespace Karkov\Kcms\Modules\ComposedModule;

use Doctrine\Common\Collections\ArrayCollection;

class ComposedModuleDto
{
    private $patternHtml;

    private $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function getPatternHtml(): string
    {
        return $this->patternHtml;
    }

    public function setPatternHtml(?string $patternHtml): self
    {
        $this->patternHtml = (string) $patternHtml;

        return $this;
    }

    public function addElements(array $elements): self
    {
        foreach ($elements as $element) {
            $this->addElement($element);
        }

        return $this;
    }

    public function addElement(ComposedModuleElement $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
        }

        return $this;
    }

    public function removeElements(array $elements): self
    {
        foreach ($elements as $element) {
            $this->removeElement($element);
        }

        return $this;
    }

    public function removeElement($element): self
    {
        $this->elements->removeElement($element);

        return $this;
    }

    public function getElements(): ArrayCollection
    {
        return $this->elements;
    }

    public function getElementsFromPattern()
    {
        $pattern = '/{{ELEMENT:([a-zA-Z0-9]*):([a-zA-Z_]*)}}/i';
        preg_match_all($pattern, $this->patternHtml, $result);

        return ['elements' => $result[0], 'ids' => $result[1], 'types' => $result[2]];
    }
}
