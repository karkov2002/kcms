<?php

namespace Karkov\Kcms\Form\EditContent;

use Karkov\Kcms\Entity\ContentLocal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentLocalTextAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('local', HiddenType::class);
        $builder->add('rawContent', TextareaType::class, ['label' => false, 'required' => false, 'attr' => ['rows' => 10, 'cols' => 100]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentLocal::class,
        ]);
    }
}
