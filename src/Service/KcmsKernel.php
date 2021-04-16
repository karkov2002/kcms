<?php

namespace Karkov\Kcms\Service;

use App\Service\KcmsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Entity\CmsPage;
use Karkov\Kcms\KcmsBundle;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class KcmsKernel
{
    private $KcmsBundle;
    private $entityManager;
    private $config;
    private $service = [];

    public function __construct(KcmsBundle $KcmsBundle, EntityManagerInterface $entityManager)
    {
        $this->KcmsBundle = $KcmsBundle;
        $this->entityManager = $entityManager;
        $this->config = $this->KcmsBundle->getConfig();
    }

    public function getPageElement(Request $request)
    {
        $expressionLanguage = new ExpressionLanguage();

        $expressionLanguage->register('karkov', function ($str) {
            return sprintf('(is_string(%1$s) ? strtolower(%1$s) : %1$s)', $str);
        }, function ($argument, $str) {
            if (!is_string($str)) {
                return $str;
            }

            return strtolower($str);
        });

        dump($expressionLanguage->evaluate('1 + 2'));

        $kcmsPage = new CmsPage();
        $kcmsPage->setTitle('a title');

        $result = $expressionLanguage->evaluate('page.getTitle()', ['page' => $kcmsPage]);
        dump($result);

        $result = $expressionLanguage->evaluate("page.getTitle() in ['a title','foo']", ['page' => $kcmsPage]);
        dump($result);

        $result = $expressionLanguage->compile('4 + 2');
        dump($result);

        dump($expressionLanguage->evaluate('karkov("KARKOV IS here")'));
        dump($expressionLanguage->compile('karkov("KARKOV IS here")'));

        $ast = (new ExpressionLanguage())
            ->parse("page.getTitle() in ['a title','foo']", array_keys(['page' => $kcmsPage]))
            ->getNodes()
        ;
        dump($ast);

        $propertyAccessor = new PropertyAccessor();
        $result = $propertyAccessor->getValue($kcmsPage, 'title');

        dump($result);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function addService(KcmsInterface $service)
    {
        $service->setCms();
        $this->service[] = $service;
    }
}
