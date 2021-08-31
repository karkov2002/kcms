<?php

namespace Karkov\Kcms\Form\EditContent;

use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Service\Selector\ModuleSelector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
{
    private $moduleSelector;

    public function __construct(ModuleSelector $moduleSelector)
    {
        $this->moduleSelector = $moduleSelector;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('module', ChoiceType::class, [
                'choices' => $this->moduleSelector->getList(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Content::class,
        ]);
    }
}
