<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\True;

/**
 * Class UserTermsType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserTermsType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('acceptTermsOfUse', 'checkbox', [
                'label' => 'Conditions',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new True(),
                ],
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                if (null !== $data = $event->getForm()->get('acceptTermsOfUse')->getData()) {
                    if (null !== $user = $event->getData()) {
                        if ($user instanceof User) {
                            $user->setTermsAccepted(new \DateTime());
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
        ]);
    }
}
