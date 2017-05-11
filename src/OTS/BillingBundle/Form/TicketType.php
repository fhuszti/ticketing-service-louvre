<?php

namespace OTS\BillingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class TicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, array(
                    'label' => 'First name',
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control']
                ))
                ->add('name', TextType::class, array(
                    'label' => 'Name',
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control']
                ))
                ->add('country', CountryType::class, array(
                    'label' => 'Country',
                    'placeholder' => 'Choose a country...',
                    'preferred_choices' => array('FR', 'GB', 'US', 'CN'),
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control']
                ))
                ->add('birthDate', DateType::class, array(
                    'invalid_message' => "Birth date must be either a valid DateTime object or a valid date string.",
                    'label' => 'Birth date',
                    'widget' => 'single_text',
                    'html5' => false,
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control']
                ))
                ->add('discounted', CheckboxType::class, array(
                    'label' => 'Reduced price
                                Student, french military, museum employee or Ministry of Culture employee.
                                An ID will be required at the entrance.',
                    'label_attr' => ['class' => 'col-xs-11 col-xs-push-1'],
                    'attr' => ['class' => 'col-xs-1 col-xs-pull-11'],
                    'required' => false
                ))
                ->add('price', HiddenType::class, array(
                    'invalid_message' => "The price of the order must be a valid integer greater than 0.",
                    'attr' =>      array('min' => 0)
                ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OTS\BillingBundle\Entity\Ticket'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ots_billingbundle_ticket';
    }


}
