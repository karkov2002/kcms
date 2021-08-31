<?php

namespace Karkov\Kcms\Service\Selector;

use Symfony\Component\HttpFoundation\RequestStack;

class LocalSelector
{
    private $config;
    private $requestStack;

    public function __construct(RequestStack $requestStack, array $config)
    {
        $this->config = $config;
        $this->requestStack = $requestStack;
    }

    public function getList(): ?array
    {
        if (!$this->config['multilingual']['enable']) {
            return array_combine(['default'], [$this->requestStack->getMasterRequest()->getDefaultLocale()]);
        }

        return array_combine($this->config['multilingual']['local'], $this->config['multilingual']['local']);
    }
}
