<?php

namespace App\Form;

use App\Entity\Ad;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tableau_id',HiddenType::class,['required'=>false])
            ->add('title',TextType::class,['required'=>true])
            //->add('slug')
            ->add('price',MoneyType::class) //'price', 'content' etc represente les entity
            ->add('introduction')
            ->add('content')
            ->add('coverImage',UrlType::class)
            ->add('rooms',IntegerType::class)
            ->add('images', CollectionType::class, [
                'entry_type'=> ImageType::class,
                'allow_add'=>true,         // ajout de propriété
                'allow_delete'=>true,       //possibilité d'effacer propriété
                                           
            ])
            ->add('file',FileType::class,[
                'label'=>false,
                'required'=>false,
                'multiple'=>true,
                'attr'=>['placeholder'=>'choisir une image'],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
