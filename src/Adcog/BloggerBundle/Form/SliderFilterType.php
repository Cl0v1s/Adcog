<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SliderFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SliderFilterType extends AbstractType
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
                'required' => false,
            ])
            ->add('href', 'text', [
                'label' => 'Lien',
                'required' => false,
            ]);
    }
}
