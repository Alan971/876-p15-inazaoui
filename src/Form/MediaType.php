<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'mapped' => false,
                'label' => 'Image',
                'attr' =>['accept' =>'image/*'], //n'accepter que les images
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M', // Taille maximale 
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (PNG, JPEG, JPG).',
                        'maxSizeMessage'=> 'La taille du fichier dépasse la limite autorisée ({{ limit }}Mo).',
                    ]),
                    new Assert\Image([
                        'maxWidth' => 3840, 
                        'maxHeight' => 2160,
                        'maxWidthMessage' => 'L\'image est trop large. La largeur maximale autorisée est 3840 px.',
                        'maxHeightMessage' => 'L\'image est trop haute. La hauteur maximale autorisée est 2160px.',
            
                    ]),
            ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
        ;

        if ($options['is_admin']) {
            $builder
                ->add('user', EntityType::class, [
                    'label' => 'Utilisateur',
                    'required' => false,
                    'class' => User::class,
                    'choice_label' => 'name',
                ])
                ->add('album', EntityType::class, [
                    'label' => 'Album',
                    'required' => false,
                    'class' => Album::class,
                    'choice_label' => 'name',
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'is_admin' => false,
        ]);
    }
}
