<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('creation', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('aura_lieu', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('adresse')
            ->add('code_postal')
            ->add('ville')
            ->add('pays')
            ->add('description')
            ->add('statut_coutumier')
            ->add('image', FileType::class, [
                    'label' => 'image',
                    'required' => 'false',
                    //la valeur par défault pour l'envoie en BDD est "mapped" => "true"
                    //Je ne désire pas envoyer mon nom d'image directement en BDD, dc "mapped" => "false"
                    //car je désire traiter mon nom d'image en lui donnant un nom unique, entre autres.
                    'mapped' => 'false',
                ])

            //Je rajoute un champ pour sélectionner la catégorie de l'événement :
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nom',
                'placeholder' => ' ',
            ])
            //Je rajoute un bouton d'envoi, et je précise le type SubmitType::class)
            ->add('Valider',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
