<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\Profile;
use EB\DoctrineBundle\Converter\StringConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ProfileType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ProfileType extends AbstractType
{
    use NameTrait;

    /**
     * @var StringConverter
     */
    private $stringConverter;

    /**
     * @param StringConverter $stringConverter
     */
    public function __construct(StringConverter $stringConverter)
    {
        $this->stringConverter = $stringConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Nom',
            ])
            ->add('description', 'textarea', [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'style' => 'min-height:400px;',
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $profile = $event->getData();
                if ($profile instanceof Profile) {
                    $profile->setSlug($this->stringConverter->slug($profile->getName()));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Profile',
        ]);
    }
}
