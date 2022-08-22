<?php

namespace App\Form;

use App\Entity\Bdl;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BdlType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numBdl',           NumberType::class, array('required' => false))
            ->add('title',            TextType::class)
            ->add('dateBdl',          DateType::class, array('required' => false, 'widget' =>'single_text', 'format' =>'dd/MM/yyyy', 'html5' => false))
            ->add('toDelivered',      CheckboxType::class, array('required' => false))
            ->add('isDelivered',        CheckboxType::class, array('required' => false))
            ->add('dateDelivered',      DateType::class, array('required' => false, 'widget' =>'single_text', 'format' =>'dd/MM/yyyy', 'html5' => false))
            ->add('save',               SubmitType::class)
            ->add('client', EntityType::class, array(
              'class'    => Client::class,
              'choice_label' => 'formatedLabel',
              'multiple' => false
            ))
            ->add('commandeProducts', CollectionType::class, array(
                'entry_type' => CommandeProductType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('commandeServices', CollectionType::class, array(
                'entry_type' => CommandeServiceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class'=> Bdl::class));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_appbundle_bdl';
    }
}
