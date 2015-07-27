<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\Slider;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SliderType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SliderType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alt', 'text', [
                'label' => 'Texte',
                'placeholder' => 'Lorem Ipsum Dolor',
            ])
            ->add('href', 'text', [
                'label' => 'Lien',
                'placeholder' => 'http://adcog.fr',
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $slider = $event->getData();
                if ($slider instanceof Slider) {
                    $event->getForm()->add('file', 'file', [
                        'label' => 'Image',
                        'required' => null === $slider->getId(),
                    ]);
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Slider',
        ]);
    }
}
