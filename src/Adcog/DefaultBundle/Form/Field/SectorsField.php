<?php

namespace Adcog\DefaultBundle\Form\Field;

use Adcog\DefaultBundle\Form\NameTrait;
use Adcog\DefaultBundle\Transformer\SectorTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class SectorsField
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SectorsField extends AbstractType
{
    use NameTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SectorTransformer
     */
    private $sectorTransformer;

    /**
     * @param RouterInterface   $router
     * @param SectorTransformer $sectorTransformer
     */
    public function __construct(RouterInterface $router, SectorTransformer $sectorTransformer)
    {
        $this->router = $router;
        $this->sectorTransformer = $sectorTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->sectorTransformer);
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
                'data-tags-ws' => $this->router->generate('user_user_api_sector_ws'),
            ],
        ]);
    }
}
