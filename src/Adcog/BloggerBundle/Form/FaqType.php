<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\Faq;
use Adcog\DefaultBundle\Entity\FaqCategory;
use Adcog\DefaultBundle\Form\NameTrait;
use EB\DoctrineBundle\Converter\StringConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FaqType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class FaqType extends AbstractType
{
    use NameTrait;

    /**
     * @var StringConverter
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
            ->add('category', 'entity', [
                'label' => 'Catégorie',
                'class' => 'Adcog\DefaultBundle\Entity\FaqCategory',
            ])
            ->add('title', 'text', [
                'label' => 'Titre',
            ])
            ->add('text', 'adcog_wysiwyg_field', [
                'label' => 'Contenu',
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $faq = $event->getData();
                if ($faq instanceof Faq) {
                    $faq->setSlug($this->stringConverter->slug($faq->getStringToSlug()));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Faq',
        ]);
    }
}
