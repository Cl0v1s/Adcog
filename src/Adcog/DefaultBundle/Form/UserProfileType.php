<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class UserProfileType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserProfileType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', [
                'label' => 'Image',
                'required' => false,
                'constraints' => [
                    new NotNull(['groups' => 'RequireImage']),
                    new Image(),
                ],
            ])
            ->add('fileRemove', 'submit', [
                'label' => 'Supprimer ma photo',
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $button = $event->getForm()->get('fileRemove');
                if ($button instanceof SubmitButton) {
                    $data = $event->getData();
                    if ($data instanceof User) {
                        if ($button->isClicked()) {
                            $data->removeFile();
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
            'data_class' => 'Adcog\DefaultBundle\Entity\User',
            'validation_groups' => [$this, 'getValidationGroups'],
        ]);
    }

    /**
     * Get validation groups
     *
     * @param FormInterface $form
     *
     * @return array
     */
    public function getValidationGroups(FormInterface $form)
    {
        if (false === $form->has('fileRemove') || false === $form->get('fileRemove')->getData()) {
            return ['RequireImage'];
        }

        return [];
    }
}
