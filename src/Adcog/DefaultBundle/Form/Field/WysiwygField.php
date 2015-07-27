<?php

namespace Adcog\DefaultBundle\Form\Field;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class WysiwygField
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class WysiwygField extends AbstractType
{
    use NameTrait;

    private $tags = [
        'b',
        'a',
        'span',
        'strong',
        'blockquote',
        'header',
        'footer',
        'cite',
        'em',
        'p',
        'i',
        'br',
        'div',
        'u',
        'ul',
        'li',
        'ol',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'iframe',
        'img',
    ];

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {

            $event->setData(strip_tags($event->getData(), implode(array_map(function ($tag) {
                return sprintf('<%s>', $tag);
            }, $this->tags))));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'data-wysiwyg' => json_encode([
                    'filebrowserBrowseUrl' => $this->router->generate('blogger_file_browse'),
                    'filebrowserUploadUrl' => $this->router->generate('blogger_file_upload'),
                ]),
            ],
        ]);
    }
}
