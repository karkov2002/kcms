<?php

namespace Karkov\Kcms\Form\EditContent;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ImgType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'img_kcms_';
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];
        $class = isset($attr['class']) ? $attr['class'].' ' : '';
        $class .= 'kcms_img';
        $attr['class'] = $class;

        $view->vars['attr'] = $attr;
    }
}
