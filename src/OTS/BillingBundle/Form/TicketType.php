<?php

namespace OTS\BillingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, array(
                    'label' => 'First name'
                ))
                ->add('name', TextType::class, array(
                    'label' => 'Name'
                ))
                ->add('country', CountryType::class, array(
                    'label' => 'Country',
                    'placeholder' => 'Choose a country...',
                    'preferred_choices' => array('FR', 'GB', 'US', 'CN')
                ))
                ->add('birthDate', DateType::class, array(
                    'invalid_message' => "Birth date must be either a valid DateTime object or a valid date string.",
                    'widget' => 'single_text',
                    'html5' => false,
                    'label' => 'Birth date'
                ))
                ->add('discounted', CheckboxType::class, array(
                    'label' => 'Reduced price'
                ))
                ->add('save', SubmitType::class, array(
                    'attr' => array('class' => 'btn btn-default'),
                    'label' => 'Next step',
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
