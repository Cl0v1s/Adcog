<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Entity\Comment;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CommentType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class CommentType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', 'entity', [
                'label' => 'Auteur',
                'class' => 'AdcogDefaultBundle:User',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('post', 'entity', [
                'label' => 'Article',
                'class' => 'AdcogDefaultBundle:Post',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('text', 'textarea', [
                'label' => 'Commentaire',
            ])
            ->add('invalidate', 'submit', [
                'label' => 'Invalider',
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $comment = $event->getData();
                if ($comment instanceof Comment) {
                    $comment->validate();
                    $invalidate = $event->getForm()->get('invalidate');
                    if ($invalidate instanceof SubmitButton) {
                        if ($invalidate->isClicked()) {
                            $comment->invalidate();
                        }
                    }
                    $event->setData($comment);
                }
            });
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
