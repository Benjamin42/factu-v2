<?php

namespace App\Form;

use App\Entity\CommandeService;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amt', NumberType::class, ["required" => true])
            ->add('service', EntityType::class, array(
              'class'    => Service::class,
              'query_builder' => function(ServiceRepository $er) {
                  return $er->createQueryBuilder('u')
                      ->where('u.active=true');
              },
              'choice_label' => 'title',
              'multiple' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class'=> CommandeService::class));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factu_appbundle_commandeservice';
    }
}
