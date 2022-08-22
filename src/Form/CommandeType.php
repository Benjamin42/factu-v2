<?php

namespace App\Form;

use App\Entity\Bdl;
use App\Entity\Client;
use App\Entity\Commande;
use App\Repository\BdlRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numFactu', NumberType::class, array('required' => false))
            ->add('bdl', EntityType::class, array(
                'class' => Bdl::class,
                'query_builder' => function (BdlRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.dateBdl', 'DESC');
                },
                'choice_label' => 'formatedLabel',
                'multiple' => false,
                'required' => false
            ))
            ->add('dateFactu', DateType::class, array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false))
            ->add('toDelivered', CheckboxType::class, array('required' => false))
            ->add('isPayed', CheckboxType::class, array('required' => false))
            ->add('isDelivered', CheckboxType::class, array('required' => false))
            ->add('datePayed', DateType::class, array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false))
            ->add('dateDelivered', DateType::class, array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false))
            ->add('save', SubmitType::class)
            ->add('client', EntityType::class, array(
                'class' => Client::class,
                'choice_label' => 'formatedLabel',
                'multiple' => false,
                'expanded' => false,
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
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Commande::class));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_appbundle_commande';
    }
}
