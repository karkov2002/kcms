<?php

namespace Karkov\Kcms\Form\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Form\DataTransformer\ComposedTransformer;
use Karkov\Kcms\Form\EditContent\ComposedElementType;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleElement;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ComposedFormSubscriber implements EventSubscriberInterface
{
    private $composedTransformer;

    public function __construct(ComposedTransformer $composedTransformer)
    {
        $this->composedTransformer = $composedTransformer;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $contentLocal = $form->getParent() ? $form->getParent()->getNormData() : new ContentLocal();

        if (null === $contentLocal->getHtmlPattern()) {
            $form->add('patternHtml', TextareaType::class, ['label' => false, 'attr' => ['class' => 'pattern_html field-ckeditor']]);
        }

        $form->add('elements', CollectionType::class, [
            'label' => false,
            'entry_type' => ComposedElementType::class,
            'entry_options' => ['label' => false],
        ]);

        $data = $event->getData();

        if (null !== $data) {
            $dto = $this->composedTransformer->transform($data);
            $dto = $this->symchronizePatternAndElements($dto);
            $data = $this->composedTransformer->reverseTransform($dto);

            $event->setData($data);
        }
    }

    public function submit(FormEvent $event)
    {
        $dto = $event->getData();

        $this->symchronizePatternAndElements($dto);
    }

    public function symchronizePatternAndElements(ComposedModuleDto $dto): ComposedModuleDto
    {
        $elements = $dto->getElements();
        $elementsFromPattern = $dto->getElementsFromPattern();

        $toChangeType = $this->getElementsToChangeType($elementsFromPattern, $elements);
        $toAdd = $this->getElementsToAdd($elementsFromPattern, $elements);
        $toRemove = $this->getElementsToRemove($elements, $elementsFromPattern);

        // Change elements type
        foreach ($toChangeType as $id => $type) {
            foreach ($dto->getElements() as $element) {
                if ($element->getId() === $id) {
                    $element->setType($type);
                }
            }
        }

        // Add elements
        foreach ($toAdd as $id => $type) {
            $composedModuleElement = new ComposedModuleElement();
            $composedModuleElement
                ->setType($type)
                ->setId($id)
                ->setContent('');

            $dto->addElement($composedModuleElement);
        }

        // Remove elements
        foreach ($toRemove as $id) {
            foreach ($dto->getElements() as $element) {
                if ($element->getId() === $id) {
                    $dto->removeElement($element);
                }
            }
        }

        return $dto;
    }

    private function getElementsToChangeType(array $elementsFromPattern, ArrayCollection $elements): array
    {
        $toChangeType = [];

        // Elements in pattern AND in ComposedModuleDto $data :
        // Change type
        foreach ($elementsFromPattern['ids'] as $key => $id) {
            foreach ($elements as $element) {
                if ($element->getId() === (int) $id) {
                    $toChangeType[$id] = $elementsFromPattern['types'][$key];
                }
            }
        }

        return $toChangeType;
    }

    private function getElementsToRemove(ArrayCollection $elements, array $elementsFromPattern): array
    {
        // Elements in ComposedModuleDto $data but no more in pattern :
        // Must be removed from $data
        $toRemove = [];
        foreach ($elements as $element) {
            if (!in_array($element->getId(), $elementsFromPattern['ids']) && !in_array($element->getId(), $toRemove)) {
                $toRemove[] = $element->getId();
            }
        }

        return $toRemove;
    }

    private function getElementsToAdd(array $elementsFromPattern, ArrayCollection $elements): array
    {
        // Elements in pattern but not yet in ComposedModuleDto $data :
        // Must be added to $data
        $toAdd = [];
        foreach ($elementsFromPattern['ids'] as $key => $id) {
            $add = true;
            foreach ($elements as $element) {
                if ($element->getId() === (int) $id) {
                    $add = false;
                }
            }
            if ($add) {
                $toAdd[$id] = $elementsFromPattern['types'][$key];
            }
        }

        return $toAdd;
    }
}
