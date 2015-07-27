<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Employer;
use Adcog\DefaultBundle\Form\NameTrait;
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
                'attr' => [
                    'data-ws' => $this->router->generate('user_employer_api_name'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('address', 'textarea', [
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('zip', 'text', [
                'label' => 'Code postal',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_employer_api_zip'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_employer_api_city'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_employer_api_country'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('phone', 'text', [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('website', 'text', [
                'label' => 'Site web',
                'required' => false,
            ])
            ->add('email', 'email', [
                'label' => 'Email',
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
