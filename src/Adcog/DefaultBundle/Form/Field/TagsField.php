<?php

namespace Adcog\DefaultBundle\Form\Field;

use Adcog\DefaultBundle\Form\NameTrait;
use Adcog\DefaultBundle\Transformer\TagTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TagsField
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TagsField extends AbstractType
{
    use NameTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TagTransformer
     */
    private $tagTransformer;

    /**
     * @param RouterInterface $router         Router
     * @param TagTransformer  $tagTransformer Transformer
     */
    public function __construct(RouterInterface $router, TagTransformer $tagTransformer)
    {
        $this->router = $router;
        $this->tagTransformer = $tagTransformer;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->tagTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'data-tags-ws' => $this->router->generate('user_user_api_tag'),
            ]
        ]);
    }
}
