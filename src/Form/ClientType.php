<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Country;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numClient', NumberType::class)
            ->add('numTva', TextType::class, array('required' => false))
            ->add('civilite', EntityType::class, array(
                'class' => Type::class,
                'choice_label' => 'title',
                'multiple' => false
            ))
            ->add('nom', TextType::class)
            ->add('nomInfo', TextType::class, array('required' => false))
            ->add('prenom', TextType::class, array('required' => false))
            ->add('tel', TextType::class, array('required' => false))
            ->add('portable', TextType::class, array('required' => false))
            ->add('fax', TextType::class, array('required' => false))
            ->add('email', EmailType::class, array('required' => false))
            ->add('commentaire', TextareaType::class, array('required' => false))
            ->add('rue', TextType::class, array('required' => false))
            ->add('bat', TextType::class, array('required' => false))
            ->add('bp', TextType::class, array('required' => false))
            ->add('codePostal', TextType::class, array('required' => false))
            ->add('ville', TextType::class, array('required' => false))
            ->add('pays', EntityType::class, array(
                'class' => Country::class,
                'choice_label' => 'name',
                'multiple' => false,
                'error_bubbling' => true
            ))
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Client::class));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_appbundle_client';
    }
}
