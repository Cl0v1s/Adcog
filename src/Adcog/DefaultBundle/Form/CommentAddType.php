<?php

namespace Adcog\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CommentAddType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class CommentAddType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'textarea', [
                'label' => 'Commentaire',
                'placeholder' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'help' => 'Ce commentaire sera validé par un administrateur avant sa publication',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Comment',
        ]);
    }
}
