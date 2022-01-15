<?php

namespace App\Form;

use App\Entity\Restaurant;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;


class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $role = "ROLE_ADMIN";
        $builder
            ->add('nom')
            ->add('adress')
            ->add('num_tel')
            ->add('users', EntityType::class,[
                'class' => User::class,
                'choice_label' => 'email',
                'multiple'     => true,
                'mapped' => true,
                'required' => true,
                'query_builder' => function (EntityRepository $er) use($role){
                    return $er->createQueryBuilder('u')
                    ->where('u.roles LIKE :role')
                    ->setParameter(':role', '%"'.$role.'"%');    
                }, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
