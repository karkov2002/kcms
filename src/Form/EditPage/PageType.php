<?php

namespace Karkov\Kcms\Form\EditPage;

use Karkov\Kcms\Entity\Site;
use Karkov\Kcms\Service\Selector\TemplateSelector;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PageType extends AbstractType
{
    private $templateSelector;

    public function __construct(TemplateSelector $templateSelector)
    {
        $this->templateSelector = $templateSelector;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('template', ChoiceType::class, [
                'choices' => $this->templateSelector->getList(),
            ])
            ->add('sites', EntityType::class, [
                'class' => Site::class,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }
}
