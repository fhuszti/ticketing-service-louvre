<?php

namespace OTS\BillingBundle\Form\Type;

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
                            'invalid_message' => "ots_billing.ticket_order_type.date.message",
                            'widget' =>          'single_text',
                            'html5' =>           false,
                            'label' =>           "ots_billing.ticket_order_type.date.label",
                            'translation_domain' => 'validators'
                        ))
                        ->add('type',      ChoiceType::class, array(
                            'label' =>           "ots_billing.ticket_order_type.type.label",
                            'choices' =>   array(
                                'ots_billing.ticket_order_type.type.choice.half' => false,
                                'ots_billing.ticket_order_type.type.choice.full' => true
                            ),
                            'expanded' =>        true,
                            'multiple' =>        false,
                            'translation_domain' => 'validators'
                        ))
                        ->add('nbTickets', IntegerType::class, array(
                            'invalid_message' => "ots_billing.ticket_order_type.count.message",
                            'label' =>           "ots_billing.ticket_order_type.count.label",
                            'attr' =>      array('min' => 1),
                            'translation_domain' => 'validators'
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
                            'data' =>            $options['date'],
                            'label_attr' => array('class' => 'hidden-label'),
                            'widget' =>          'single_text',
                            'html5' =>           false,
                        ))
                //hidden field to get the number of tickets requested in step 1
                        ->add('nbTickets', HiddenType::class, array(
                            'data' =>            $options['nbTickets']
                        ))
                //hidden field to get the type of tickets requested in step 1
                        ->add('type', HiddenType::class, array(
                            'data' =>            $options['type']
                        ))
                //to remember the price for display in step 3, will be populated with jquery
                        ->add('price', IntegerType::class, array(
                            'invalid_message' => "ots_billing.ticket_order_type.price.message",
                            'label_attr' => array('class' => 'hidden-label'),
                            'attr' =>      array('min' => 1, 'class' => 'hidden'),
                            'translation_domain' => 'validators'
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
                        ))
                        ->add('checkoutToken', HiddenType::class, array(
                            'mapped' => false
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
