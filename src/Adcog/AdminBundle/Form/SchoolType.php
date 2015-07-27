<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\School;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SchoolType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SchoolType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', 'integer', [
                'label' => 'Année',])
            ->add('name', 'text', [
                'label' => 'Nom'
            ])
            ->add('wikipedia', 'text', [
                'label' => 'Lien vers Wikipedia',
                'required' => false,
            ])
            ->add('description', 'adcog_wysiwyg_field', [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('graduation', 'adcog_wysiwyg_field', [
                'label' => 'Remise des diplômes',
                'required' => false,
            ])
            ->add('graduates', 'integer', [
                'label' => 'Diplômés',
                'required' => false,
            ])
            ->add('file', 'file', [
                'label' => 'Photo',
                'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $school = $event->getData();
                if ($school instanceof School) {
                    if (null !== $spl = $school->getFile()) {
                        if (null !== $school->getId()) {
                            $school
                                ->setUpdated(new \DateTime())
                                ->setUri(null);
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
            'data_class' => 'Adcog\DefaultBundle\Entity\School',
        ]);
    }
}
