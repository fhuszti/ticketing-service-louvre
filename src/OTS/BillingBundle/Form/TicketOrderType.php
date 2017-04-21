<?php

namespace OTS\BillingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class TicketOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder->add('date',      DateType::class, array(
                            'invalid_message' => "Order date must be either a valid DateTime object or a valid date string.",
                            'widget' =>          'single_text',
                            'html5' =>           false,
                            'label' =>           'Date of the visit'
                        ))
                        ->add('type',      ChoiceType::class, array(
                            'label' =>           'Ticket type',
                            'choices' =>   array(
                                'Full-day' => true,
                                'Half-day' => false,
                            ),
                            'expanded' =>        true,
                            'multiple' =>        false,
                        ))
                        ->add('nbTickets', IntegerType::class, array(
                            'invalid_message' => "The number of tickets in the order must be a valid integer greater than 0.",
                            'label' =>           "Tickets count",
                            'attr' =>      array('min' => 1)
                        ));
                break;
            case 2:
                //ticket forms
                $builder->add('tickets',   CollectionType::class, array(
                            'entry_type' =>      TicketType::class,
                            'allow_add' =>       true,
                            'allow_delete' =>    true,
                            'by_reference' =>    false
                        ))
                //hidden field to get the number of tickets requested in step 2
                        ->add('nbTickets', HiddenType::class, array(
                            'data' => $options['nbTickets']
                        ));
                break;
        }

        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OTS\BillingBundle\Entity\TicketOrder',
            'nbTickets' => false
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
