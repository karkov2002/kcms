<?php

namespace Karkov\Kcms\Form\EditContent;

use Karkov\Kcms\Form\DataTransformer\ComposedTransformer;
use Karkov\Kcms\Form\EventSubscriber\ComposedFormSubscriber;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComposedType extends AbstractType
{
    private $composedTransformer;

    public function __construct(ComposedTransformer $composedTransformer)
    {
        $this->composedTransformer = $composedTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ComposedFormSubscriber($this->composedTransformer));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ComposedModuleDto::class,
        ]);
    }
}
