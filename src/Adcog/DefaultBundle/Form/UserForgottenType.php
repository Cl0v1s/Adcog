<?php

namespace Adcog\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as C;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class UserForgottenType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserForgottenType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'email', [
            'label' => 'Email',
            'placeholder' => 'john.doe@gmail.com',
            'constraints' => [
                new C\NotBlank(),
                new C\NotNull(),
                new C\Email(),
            ],
        ]);
    }
}
