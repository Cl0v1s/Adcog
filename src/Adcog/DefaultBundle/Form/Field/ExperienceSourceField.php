<?php

namespace Adcog\DefaultBundle\Form\Field;

use Adcog\DefaultBundle\Form\NameTrait;
use Adcog\DefaultBundle\Transformer\ExperienceSourceTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class ExperienceSourceField
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceSourceField extends AbstractType
{
    use NameTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ExperienceSourceTransformer
     */
    private $experienceSourceTransformer;

    /**
     * @param RouterInterface   $router
     * @param ExperienceSourceTransformer $experienceSourceTransformer
     */
    public function __construct(RouterInterface $router, ExperienceSourceTransformer $experienceSourceTransformer)
    {
        $this->router = $router;
        $this->experienceSourceTransformer = $experienceSourceTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->experienceSourceTransformer);
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
            'attr' => [
                'data-source-ws' => $this->router->generate('user_user_api_source_ws'),
            ],
        ]);
    }
}
