<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\StaticContent;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class StaticContentFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class StaticContentFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', [
                'label' => 'Type',
                'choices' => StaticContent::getTypeNameList(),
                'required' => false,
            ])
            ->add('title', 'text', [
                'label' => 'Titre',
                'required' => false,
            ]);
    }
}
