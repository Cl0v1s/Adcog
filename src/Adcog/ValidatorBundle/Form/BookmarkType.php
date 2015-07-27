<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Entity\Bookmark;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BookmarkType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class BookmarkType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'Titre',
            ])
            ->add('href', 'text', [
                'label' => 'Lien',
            ])
            ->add('tags', 'adcog_tags_field', [
                'label' => 'Tags',
                'by_reference' => false,
            ])
            ->add('invalidate', 'submit', [
                'label' => 'Invalider',
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $bookmark = $event->getData();
                if ($bookmark instanceof Bookmark) {
                    $bookmark->validate();
                    $invalidate = $event->getForm()->get('invalidate');
                    if ($invalidate instanceof SubmitButton) {
                        if ($invalidate->isClicked()) {
                            $bookmark->invalidate();
                        }
                    }
                    $event->setData($bookmark);
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Bookmark',
        ]);
    }
}
