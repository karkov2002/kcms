<?php

namespace Karkov\Kcms\Form\EditPatternHtml;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class HtmlPatternType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('pattern', TextareaType::class, ['label' => false, 'required' => false, 'attr' => ['class' => 'field-ckeditor']])
        ;
    }
}
