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
                    'label' => 'ots_billing.ticket_type.first_name.label',
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control'],
                    'translation_domain' => 'validators'
                ))
                ->add('name', TextType::class, array(
                    'label' => 'ots_billing.ticket_type.name.label',
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control'],
                    'translation_domain' => 'validators'
                ))
                ->add('country', CountryType::class, array(
                    'label' => 'ots_billing.ticket_type.country.label',
                    'placeholder' => 'ots_billing.ticket_type.country.placeholder',
                    'preferred_choices' => array('FR', 'GB', 'US', 'CN'),
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control'],
                    'translation_domain' => 'validators'
                ))
                ->add('birthDate', DateType::class, array(
                    'invalid_message' => "ots_billing.ticket_type.birth_date.message",
                    'label' => 'ots_billing.ticket_type.birth_date.label',
                    'widget' => 'single_text',
                    'html5' => false,
                    'label_attr' => ['class' => 'col-xs-12 control-label'],
                    'attr' => ['class' => 'form-control',  'autocomplete' => "off"],
                    'translation_domain' => 'validators'
                ))
                ->add('discounted', CheckboxType::class, array(
                    'label' => 'ots_billing.ticket_type.discounted.label',
                    'label_attr' => ['class' => 'col-xs-11 col-xs-push-1'],
                    'attr' => ['class' => 'col-xs-1 col-xs-pull-11'],
                    'required' => false,
                    'translation_domain' => 'validators'
                ))
                ->add('price', HiddenType::class, array(
                    'invalid_message' => "ots_billing.ticket_type.price.message",
                    'attr' =>      array('min' => 0),
                    'translation_domain' => 'validators'
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
