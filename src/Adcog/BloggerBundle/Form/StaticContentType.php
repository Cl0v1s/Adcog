<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\StaticContent;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class StaticContentType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class StaticContentType extends AbstractType
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
            ])
            ->add('title', 'text', [
                'label' => 'Titre',
            ])
            ->add('content', 'adcog_wysiwyg_field', [
                'label' => 'Contenu',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\StaticContent',
        ]);
    }
}
