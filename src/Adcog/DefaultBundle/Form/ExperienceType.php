<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ExperienceType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceType extends AbstractType
{
    use NameTrait;
    
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isPublic', 'checkbox', [
                'label' => 'Public',
                'help' => 'Cette expérience sera diffusée sur votre profil s\'il est public lui aussi',
                'required' => false,
            ])
            ->add('started', 'adcog_date_field', [
                'label' => 'Début',
                'placeholder' => 'ex: 01/09/2007',
            ])
            ->add('ended', 'adcog_date_field', [
                'label' => 'Fin',
                'placeholder' => 'ex: 30/06/2010',
                'required' => false,
            ])
            ->add('description', 'textarea', [
                'label' => 'Description',
                'placeholder' => 'Decrivez ici votre expérience...',
            ])
            ->add('sectors', 'adcog_sectors_field', [
                'label' => 'Secteurs',
            ])
            ->add('employer', 'adcog_employer', [
                'label' => 'Employeur, Établissement',
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                if ($this->authorizationChecker->isGranted(User::ROLE_ADMIN)) {
                    $event->getForm()->add('user', 'entity', [
                        'label' => 'Utilisateur',
                        'help' => 'Ce champ n\'est disponible que pour les administrateurs',
                        'class' => 'Adcog\DefaultBundle\Entity\User',
                    ]);
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}
