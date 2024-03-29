<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represent an article form
 *
 * @author Dany Pignoux <dany.pignoux@outlook.fr>
 */
class ArticleFormType extends AbstractType
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
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('category', EntityType::class, ['class' => ArticleCategory::class, 'choice_label' => 'name'])
            ->add('description', TextType::class, ['label' => 'Description', 'required' => false])
            ->add('imageUrl', TextType::class, ['label' => 'URL de l\'image d\'illustration', 'required' => false, 'empty_data' => null])
            ->add('content', TextareaType::class, ['label' => 'Contenu', 'empty_data' => ''])
            ->add('isPublic', CheckboxType::class, ['label' => 'Est publique', 'required' => false])
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
            'data_class' => Article::class
        ]);
    }
}