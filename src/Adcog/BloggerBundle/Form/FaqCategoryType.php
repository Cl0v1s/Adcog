<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\FaqCategory;
use Adcog\DefaultBundle\Form\NameTrait;
use EB\DoctrineBundle\Converter\StringConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FaqCategoryType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class FaqCategoryType extends AbstractType
{
    use NameTrait;

    /**
     * @var $stringConverter
     */
    private $stringConverter;

    /**
     * @param StringConverter $stringConverter
     */
    public function __construct(StringConverter $stringConverter)
    {
        $this->stringConverter = $stringConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Nom',
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $faqCategory = $event->getData();
                if ($faqCategory instanceof FaqCategory) {
                    $faqCategory->setSlug($this->stringConverter->slug($faqCategory->getStringToSlug()));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\FaqCategory',
        ]);
    }
}
