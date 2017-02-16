<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 15.02.2017
 * Time: 19:13
 */

namespace WowGuildAudit\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WowGuildAudit\Entity\Member;

class MemberType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => false,
                'attr' => array(
                    'class' => 'form-control input-sm'
                )
            ))
            ->add('role', ChoiceType::class, array(
                'label' => false,
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'choices' => array(
                    'Tank' => 'Tank',
                    'Healer' => 'Healer',
                    'Ranged DD' => 'Ranged DD',
                    'Melee DD' => 'Melee DD'
                )
            ))
            ->add('realm', TextType::class, array(
                'label' => false,
                'attr' => array(
                    'class' => 'form-control input-sm realm'
                )
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $form = $event->getForm();
            $member = $event->getData();
            if ($member instanceof Member) {
                $form
                    ->add('role', ChoiceType::class, array(
                        'label' => false,
                        'attr' => array(
                            'class' => 'form-control input-sm'
                        ),
                        'choices' => array(
                            'Tank' => 'Tank',
                            'Healer' => 'Healer',
                            'Ranged DD' => 'Ranged DD',
                            'Melee DD' => 'Melee DD'
                        ),
                        'data' => $member->getRole()->getRole()
                    ))
                    ->add('realm', TextType::class, array(
                        'label' => false,
                        'attr' => array(
                            'class' => 'form-control input-sm realm'
                        )
                    ));
            }

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Member::class,
        ));
    }

}