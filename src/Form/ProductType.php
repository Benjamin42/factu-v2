<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('category', EntityType::class, array(
                'class' => ProductCategory::class,
                'choice_label' => 'title',
                'multiple' => false,
                'required' => false,
                'error_bubbling' => true
            ))
            ->add('comment', TextareaType::class, array('required' => false))
            ->add('active', CheckboxType::class, array('required' => false))
            ->add('isFollowedStat', CheckboxType::class, array('required' => false))
            ->add('idColCsv', IntegerType::class, array('required' => false))
            ->add('prices', CollectionType::class, array(
                'entry_type' => PriceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => true
            ))
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Product::class));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_appbundle_product';
    }
}
