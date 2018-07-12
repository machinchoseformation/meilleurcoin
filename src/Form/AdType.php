<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                "class" => Category::class,
                "label" => "Catégorie",
                "choice_label" => "name" //ou __toString dans Category
            ])
            ->add('title', TextareaType::class, ["label" => "Titre"])
            ->add('description')
            ->add('city', null, [
                "label" => "Ville",
                "attr" => [
                    "class" => "pouf"
                ]
            ])
            ->add('zip', null, ["label" => "Code postal"])
            ->add('price', null, ["label" => "Prix demandé"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
