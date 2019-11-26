<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class UserAccountType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserAccountType extends AbstractType
{
    use NameTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'email', [
                'label' => 'Email',
                'placeholder' => 'ex: john.doe@gmail.com',
            ])
            ->add('firstname', 'text', [
                'label' => 'Prénom',
                'placeholder' => 'ex: John',
            ])
            ->add('lastname', 'text', [
                'label' => 'Nom',
                'placeholder' => 'ex: DOE',
            ])
            ->add('gender', 'choice',[
                'label' => 'Sexe',
                'placeholder' => "Choisissez votre Sexe",
                'choices' => array(
                    'H' => "Homme",
                    'F' => "Femme"
                )
            ])
            ->add('birthDate', 'adcog_date_field', [
                'label'=>'Date de Naissance',
                'placeholder' => 'ex: 15/08/1995',
            ])
            ->add('nationality', 'country', [
                'label' => 'Nationalité',
                'placeholder' => 'Choisissez votre nationalité',
                'preferred_choices' => array('FR')
            ])
            ->add('school', 'entity', [
                'label' => 'Promotion',
                'placeholder' => 'Choisissez votre promotion',
                'class' => 'Adcog\DefaultBundle\Entity\School',
            ])
            ->add('address', 'textarea', [
                'label' => 'Adresse',
                'placeholder' => 'ex: 109 avenue Roul',
                'required' => false,
            ])
            ->add('zip', 'text', [
                'label' => 'Code postal',
                'placeholder' => 'ex: 33400',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_zip'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'placeholder' => 'ex: Talence',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_city'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'placeholder' => 'ex: France',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_country'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('phone', 'text', [
                'label' => 'Téléphone',
                'placeholder' => 'ex: 0566778899',
                'required' => false,
            ])
            ->add('website', 'text', [
                'label' => 'Site web',
                'placeholder' => 'ex: http://adcog.fr',
                'required' => false,
            ])
            
            ->add('linkedIn', 'text', [
                'label' => 'linkedIn',
                'required' => false,
            ])

            ->add('options', 'choice',[
                'label' => 'Option',
                'placeholder' => "Choisissez votre option",
                'choices' => array(
                    '1' => "Robotique",
                    '2' => "Intelligence Artificielle",
                    '3' => "Systèmes Cognitifs",
                    '4' => "Augmentation et Autonomie",
                    '5' => "Ancien parcours Cognitique",
                )
            ])

            ->add('abroad', 'choice',[
                'label' => 'Parti.e à l\'étranger',
                'choices' => array(
                    '0' => "Non",
                    '1' => "Oui"
                )
            ])

            ->add('description', 'textarea', [
                'label' => 'Description',
                'required' => false,
            ])

            ->add('acceptedContact','checkbox',[
                'label' => "Partage d'informations",
                'help' => "En cochant, j'accepte d'être contacté par les autres membres du réseau de l'ADCOG pour répondre à leurs questions",
				'required' =>false,
            ])

            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                if ($user instanceof User) {
                    if (null === $user->getId()) {
                        $event->getForm()->add('rawPassword', 'repeated', [
                            'type' => 'password',
                        ]);
                    }

                    if (null === $user->getSchool()) {
                        $event->getForm()->add('school', 'entity', [
                            'class' => 'Adcog\DefaultBundle\Entity\School',
                        ]);
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
