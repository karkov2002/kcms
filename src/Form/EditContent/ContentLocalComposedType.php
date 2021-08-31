<?php

namespace Karkov\Kcms\Form\EditContent;

use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Form\DataTransformer\ComposedTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentLocalComposedType extends AbstractType
{
    private $composedTransformer;

    public function __construct(ComposedTransformer $composedTransformer)
    {
        $this->composedTransformer = $composedTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('local', HiddenType::class);
        $builder->add('rawContent', ComposedType::class, ['label' => false, 'required' => false]);

        $builder->get('rawContent')->addModelTransformer($this->composedTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentLocal::class,
        ]);
    }
}
