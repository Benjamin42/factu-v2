<?php

namespace App\Form;

use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unitPrice',        NumberType::class)
            ->add('unitPriceLiv',     NumberType::class)
            ->add('creaDate',         DateType::class,
                array('required' => true,
                    'widget' =>'single_text',
                    'html5' => false,
                    'format' =>'dd/MM/yyyy'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class'=> Price::class));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_appbundle_price';
    }
}
