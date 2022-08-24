<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login',            TextType::class)
            ->add('nom',              TextType::class)
            ->add('prenom',           TextType::class, array('required' =>false))
            ->add('email',            TextType::class)
            ->add('active',           CheckboxType::class)
            ->add('password',           TextType::class)
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => ['ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER'],
                    'expanded' => true,
                    'multiple' => true,
                ]
            )
            ->add('save',               SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => User::class));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_userbundle_user';
    }
}
