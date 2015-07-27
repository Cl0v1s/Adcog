<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as C;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class ContactType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ContactType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Nom',
                'placeholder' => 'John DOE',
            ])
            ->add('email', 'email', [
                'label' => 'Email',
                'placeholder' => 'john.doe@gmail.com',
            ])
            ->add('subject', 'choice', [
                'label' => 'Objet',
                'choices' => array_combine(
                    Contact::getContactSubjects(),
                    Contact::getContactSubjects()
                ),
            ])
            ->add('message', 'textarea', [
                'label' => 'Message',
                'placeholder' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Contact',
        ]);
    }
}
