<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\Employer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class EmployerType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EmployerType extends AbstractType
{
    use NameTrait;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param RouterInterface $router Router
     * @param EntityManager   $em     Manager
     */
    public function __construct(RouterInterface $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Nom',
                'placeholder' => 'ex: Cognitive Corp\'',
                'attr' => [
                    'data-employer-selector' => 1,
                    'data-ws' => $this->router->generate('user_employer_api_name'),
                    'autocomplete' => 'off',
                ],
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
                    'data-ws' => $this->router->generate('user_employer_api_zip'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'placeholder' => 'ex: Talence',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_employer_api_city'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'placeholder' => 'ex: France',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_employer_api_country'),
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
                'placeholder' => 'ex: http://cognitive-corp.fr',
                'required' => false,
            ])
            ->add('email', 'email', [
                'label' => 'Email',
                'placeholder' => 'ex: contact@cognitive-corp.fr',
                'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $employer = $event->getData();
                if ($employer instanceof Employer) {
                    if (is_numeric($id = $employer->getName())) {
                        if (null !== $employer = $this->em->getRepository('AdcogDefaultBundle:Employer')->find($id)) {
                            $event->setData($employer);
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
            'data_class' => 'Adcog\DefaultBundle\Entity\Employer',
        ]);
    }
}
