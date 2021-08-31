<?php

namespace Karkov\Kcms\Form\EditPage;

use Karkov\Kcms\Entity\PageContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rank', NumberType::class, ['attr' => ['class' => 'page_content_input_rank', 'readonly' => 'readonly']])
            ->add('date_start', DateTimeType::class, ['date_widget' => 'single_text'])
            ->add('date_end', DateTimeType::class, ['date_widget' => 'single_text'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PageContent::class,
        ]);
    }
}
