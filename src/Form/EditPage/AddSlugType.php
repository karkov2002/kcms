<?php

namespace Karkov\Kcms\Form\EditPage;

use Karkov\Kcms\Service\Selector\LocalSelector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddSlugType extends AbstractType
{
    private $localSelector;

    public function __construct(LocalSelector $localSelector)
    {
        $this->localSelector = $localSelector;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('local', ChoiceType::class, [
                'required' => false,
                'choices' => $this->localSelector->getList(),
            ])
            ->add('slug', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['label' => false]);
    }
}
