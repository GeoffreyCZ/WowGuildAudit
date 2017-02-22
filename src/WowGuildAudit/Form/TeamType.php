<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 15.02.2017
 * Time: 19:13
 */

namespace WowGuildAudit\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WowGuildAudit\Entity\Team;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('members', CollectionType::class, [
            'label' => false,
            'entry_type' => MemberType::class,
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true
        ])
            ->add('save changes', SubmitType::class, [
                'attr' => array(
                    'class' => 'btn btn-primary btn-xl page-scroll'
                )
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Team::class,
        ));
    }
}