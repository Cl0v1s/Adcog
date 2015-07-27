<?php

namespace Adcog\DefaultBundle\Twig\Extension;

use Adcog\DefaultBundle\Entity\Tag;
use EB\TranslationBundle\Translation\TranslationService;

/**
 * Class TagExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TagExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var TranslationService
     */
    private $translation;

    /**
     * @param string             $name        Extension name
     * @param TranslationService $translation Translation service
     */
    public function __construct($name, TranslationService $translation)
    {
        $this->name = $name;
        $this->translation = $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('yes_no_icon', [$this, 'renderYesNoIcon'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('tag_render', [$this, 'renderTag'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Render tag
     *
     * @param Tag $tag
     *
     * @return string
     */
    public function renderTag(Tag $tag)
    {
        return $this->translation->link('blog_tag', [
            'tag' => $tag->getId(),
            'slug' => $tag->getSlug(),
        ], [
            'class' => 'tag',
            'name' => $tag->getContent(),
            'title' => $tag->getContent(),
        ]);
    }

    /**
     * Render yes no icon
     *
     * @param mixed $yesNo
     *
     * @return string
     */
    public function renderYesNoIcon($yesNo)
    {
        return sprintf('<span class="text-%s"><span class="fa fa-circle"></span></span>', $yesNo ? 'success' : 'danger');
    }
}
