<?php

/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 15.02.2017
 * Time: 14:58
 */

namespace WowGuildAudit\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('realm', TextType::class, ['label' => false, 'required' => true, 'attr' => ['placeholder' => 'Guild Realm', 'class' => 'form-control input-sm']])
            ->add('name', TextType::class, ['label' => false, 'required' => true, 'attr' => ['placeholder' => 'Guild Name', 'class' => 'form-control input-sm']])
            ->add('track', SubmitType::class, ['label' => 'Track this guild', 'attr' => ['class' => 'btn btn-primary btn-xl page-scroll']]);
    }
}