<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContractTypeFilterType
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 */
class ContractTypeFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'text', [
                'label' => 'Contenu',
                'required' => false,
            ]);
    }
}
