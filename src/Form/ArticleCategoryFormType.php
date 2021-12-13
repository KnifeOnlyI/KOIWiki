<?php

namespace App\Form;

use App\Entity\ArticleCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represent an article category form
 *
 * @author Dany Pignoux <dany.pignoux@outlook.fr>
 */
class ArticleCategoryFormType extends AbstractType
{
    /**
     * Create a new form
     *
     * @param FormBuilderInterface $builder The builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('save', SubmitType::class, ['label' => 'Confirmer']);
    }

    /**
     * Configure options
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleCategory::class
        ]);
    }
}