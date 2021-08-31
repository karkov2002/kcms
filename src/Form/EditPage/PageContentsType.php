<?php

namespace Karkov\Kcms\Form\EditPage;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class PageContentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pageContents', CollectionType::class, [
            'entry_type' => PageContentType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
        ]);
    }
}
