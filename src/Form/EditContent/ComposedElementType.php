<?php

namespace Karkov\Kcms\Form\EditContent;

use Karkov\Kcms\Modules\ComposedModule\ComposedModuleElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComposedElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $child = $event->getData();

                switch ($child->getType()) {
                    case 'HTML':
                        $form->add('content', TextareaType::class, ['label' => false, 'required' => false, 'attr' => ['class' => 'field-ckeditor']]);
                        break;
                    case 'HTML_LIGHT':
                        $form->add('content', TextareaType::class, ['label' => false, 'required' => false, 'attr' => ['class' => 'field-ckeditor-light']]);
                        break;
                    case 'IMG':
                    case 'MEDIA_URL':
                        $form->add('content', ImgType::class, ['label' => false, 'required' => false, 'attr' => ['size' => 50]]);
                        break;
                    case 'TXT_AREA':
                        $form->add('content', TextareaType::class, ['label' => false, 'required' => false, 'attr' => ['rows' => 10, 'cols' => 100]]);
                        break;
                    default:
                        $form->add('content', TextType::class, ['label' => false, 'required' => false, 'attr' => ['size' => 50]]);
                        break;
                }
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];
        $class = isset($attr['class']) ? $attr['class'].' ' : '';
        $class .= 'composed_element';
        $attr['class'] = $class;

        $view->vars['attr'] = $attr;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ComposedModuleElement::class,
        ]);
    }
}
