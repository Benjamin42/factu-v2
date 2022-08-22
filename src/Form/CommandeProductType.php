<?php

namespace App\Form;

use App\Entity\CommandeProduct;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('qty',          NumberType::class, array('required' => true))
            ->add('qtyGift',      NumberType::class, array('required' => false))
            ->add('forcedPrice',  NumberType::class, array('required' => false))
            ->add('product',      EntityType::class, array(
              'class'    => Product::class,
              'query_builder' => function(ProductRepository $er) {
                  return $er->createQueryBuilder('u')
                      ->where('u.active=true');
              },
              'choice_label' =>       'title',
              'multiple' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class'=> CommandeProduct::class));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_appbundle_commandeproduct';
    }
}
