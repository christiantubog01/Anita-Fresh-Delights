<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Added for file upload  
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Added for Price field
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Added for Category field
use App\Entity\Category; // Added for Category field

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ProductName')
            ->add('Description')
            ->add('Price', NumberType::class, [
                'scale' => 2,        // keep two decimals
                'html5' => true,     // renders <input type="number">
                'required' => true,
                'attr' => [
                    'min' => '0',
                    'step' => '0.01',
                    'inputmode' => 'decimal',
                    'pattern' => '\d+(\.\d{1,2})?' 
                ],
            ])
            ->add('image', FileType::class, [
                    'label' => 'Product Image (JPEG or PNG file)',
                    'mapped' => false, // not directly linked to entity field
                    'required' => false,
                    'constraints' => [
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ])
                    ],
                    ])

            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // the property shown in the dropdown (change it to id of you want to show id)
                'required' => true,
                'placeholder' => 'Select a category',
])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
