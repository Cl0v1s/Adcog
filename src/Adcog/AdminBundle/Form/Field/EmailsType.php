<?php

namespace Adcog\AdminBundle\Form\Field;

use Adcog\DefaultBundle\Form\NameTrait;
use Adcog\DefaultBundle\Transformer\EmailTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EmailsType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EmailsType extends AbstractType
{
    use NameTrait;

    /**
     * @var EmailTransformer
     */
    private $emailTransformer;

    /**
     * @param EmailTransformer $emailTransformer
     */
    public function __construct(EmailTransformer $emailTransformer)
    {
        $this->emailTransformer = $emailTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->emailTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new All([
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                    ],
                ]),
            ],
            'attr' => [
                'data-tags' => 1,
            ],
            'required' => false,
        ]);
    }
}
