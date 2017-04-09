<?php

namespace OTS\BillingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TicketOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, array(
                    'invalid_message' => "Order date must be either a valid DateTime object or a valid date string.",
                    'widget' => 'single_text',
                    'html5' => false,
                ))
                ->add('type', ChoiceType::class, array(
                    'label' => 'Ticket type',
                    'choices' => array(
                        'Full-day' => 1,
                        'Half-day' => 0,
                    ),
                    'expanded' => true,
                    'multiple' => false,
                ))
                ->add('nbTickets', IntegerType::class, array(
                    'invalid_message' => "The number of tickets in the order must be a valid integer greater than 0."
                ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OTS\BillingBundle\Entity\TicketOrder'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ots_billingbundle_ticketorder';
    }


}
