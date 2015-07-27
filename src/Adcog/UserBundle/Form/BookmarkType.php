<?php

namespace Adcog\UserBundle\Form;

use Adcog\DefaultBundle\Entity\Bookmark;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BookmarkType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class BookmarkType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'Titre',
            ])
            ->add('href', 'text', [
                'label' => 'Lien',
            ])
            ->add('tags', 'adcog_tags_field', [
                'label' => 'Tags',
                'required' => false,
                'by_reference' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Bookmark',
        ]);
    }
}
