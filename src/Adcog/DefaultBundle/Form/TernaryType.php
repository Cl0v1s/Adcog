<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Transformer\TernaryTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TernaryType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TernaryType extends AbstractType
{
    use NameTrait;

    const NO = 'no';
    const NONE = 'none';
    const YES = 'yes';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new TernaryTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'required' => true,
            'choices' => [
                self::NONE => $this->translator->trans(sprintf('eb.ternary.%s', self::NONE)),
                self::YES => $this->translator->trans(sprintf('eb.ternary.%s', self::YES)),
                self::NO => $this->translator->trans(sprintf('eb.ternary.%s', self::NO)),
            ],
        ]);
    }
}
