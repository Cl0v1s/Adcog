<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\File;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FileType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class FileType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', [
                'label' => 'Fichier',
                'help' => sprintf('Taille maximum %s ko', floor(UploadedFile::getMaxFilesize() / 1000)),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\File',
        ]);
    }
}
