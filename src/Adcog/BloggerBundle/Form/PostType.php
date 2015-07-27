<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\Post;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PostType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PostType extends AbstractType
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
            ->add('file', 'file', [
                'label' => 'Image',
                'required' => false,
            ])
            ->add('author', 'entity', [
                'label' => 'Auteur',
                'class' => 'Adcog\DefaultBundle\Entity\User',
            ])
            ->add('tags', 'adcog_tags_field', [
                'label' => 'Tags',
                'by_reference' => false,
            ])
            ->add('text', 'adcog_wysiwyg_field', [
                'label' => 'Texte',
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $post = $event->getData();

                if ($post instanceof Post) {
                    $event->getForm()->add('validate', 'checkbox', [
                        'label' => 'Publier',
                        'mapped' => false,
                        'data' => null !== $post->getValidated(),
                        'required' => false,
                    ]);

                    if (null !== $post->getId()) {
                        $event->getForm()->add('fileRemove', 'submit', [
                            'label' => 'Modifier & Supprimer la photo',
                            'attr' => [
                                'class' => 'btn btn-warning',
                            ]
                        ]);
                    }
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $post = $event->getData();

                if ($post instanceof Post) {
                    // Remove file
                    if ($event->getForm()->has('fileRemove')) {
                        $button = $event->getForm()->get('fileRemove');
                        if ($button instanceof SubmitButton) {
                            if ($button->isClicked()) {
                                $event->setData($post->setFile(null));
                            }
                        }
                    }

                    // Validate
                    $validate = $event->getForm()->get('validate')->getData();
                    if ($post instanceof Post) {
                        if (true === $validate) {
                            if (null === $post->getValidated()) {
                                $post->setValidated(new \DateTime());
                            }
                        } else {
                            $post->setValidated(null);
                        }
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
