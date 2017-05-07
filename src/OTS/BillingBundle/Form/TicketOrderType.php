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
                                'Half-day' => false,
                                'Full-day' => true
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
                        ->add('date', DateType::class, array(
                            'data' => $options['date'],
                            'label_attr' => array('class' => 'hidden-label'),
                            'widget' =>          'single_text',
                            'html5' =>           false,
                        ))
                //hidden field to get the number of tickets requested in step 1
                        ->add('nbTickets', HiddenType::class, array(
                            'data' => $options['nbTickets']
                        ))
                //hidden field to get the type of tickets requested in step 1
                        ->add('type', HiddenType::class, array(
                            'data' => $options['type']
                        ))
                //to remember the price for display in step 3, will be populated with jquery
                        ->add('price', HiddenType::class, array(
                            'invalid_message' => "The price of the order must be a valid integer greater than 0.",
                            'attr' =>      array('min' => 0)
                        ));
                break;
            case 3:
                //we add every field to offer a recap on the payment step
                $builder->add('date', DateType::class, array(
                            'data' => $options['date'],
                            'label_attr' => array('class' => 'hidden-label'),
                            'widget' =>          'single_text',
                            'html5' =>           false,
                        ))
                        ->add('nbTickets', HiddenType::class, array(
                            'data' => $options['nbTickets']
                        ))
                        ->add('type', HiddenType::class, array(
                            'data' => $options['type']
                        ))
                        ->add('price', HiddenType::class, array(
                            'data' => $options['price']
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
            'allow_extra_fields' => true,
            'date' => false,
            'nbTickets' => false,
            'type' => false,
            'price' => false
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
