<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class EmailType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EmailType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users', 'entity', [
                'label' => 'Utilisateurs',
                'multiple' => true,
                'class' => 'Adcog\DefaultBundle\Entity\User',
                'required' => false,
            ])
            ->add('emails', 'adcog_admin_emails_field', [
                'label' => 'Adresses emails',
                'placeholder' => 'contact@cognitive-corp.fr',
            ])
            ->add('subject', 'text', [
                'label' => 'Sujet',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new Type(['type' => 'string']),
                    new Length(['max' => 50]),
                ],
            ])
            ->add('message', 'adcog_wysiwyg_field', [
                'label' => 'Message',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $callback = function ($data, ExecutionContextInterface $context) {
            $users = $data['users'];
            if ($users instanceof ArrayCollection) {
                if (0 === $users->count() && empty($data['emails'])) {
                    $context->buildViolation('Vous devez choisir au moins un destinataire')->atPath('emails')->addViolation();
                }
            }
        };

        $resolver->setDefaults([
            'constraints' => [
                new Callback($callback),
            ],
        ]);
    }
}
