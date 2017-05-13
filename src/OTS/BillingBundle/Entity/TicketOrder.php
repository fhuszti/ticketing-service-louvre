<?php

namespace OTS\BillingBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OTS\BillingBundle\Entity\Ticket;
use OTS\BillingBundle\Entity\Customer;
use OTS\BillingBundle\Entity\Charge;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * TicketOrder
 *
 * @ORM\Table(name="ticket_order")
 * @ORM\Entity(repositoryClass="OTS\BillingBundle\Repository\TicketOrderRepository")
 * @UniqueEntity(fields={"reference"})
 */
class TicketOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date", type="date")
     * @Assert\Date(message="Order date must be either a valid DateTime object or a valid date string.")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean")
     * @Assert\Type(
     *     type="bool",
     *     message="Order type must be a boolean."
     * )
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_tickets", type="smallint")
     * @Assert\GreaterThan(
     *     value=0,
     *     message="Number of tikets must be greater than 0."
     * )
     */
    private $nbTickets;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="smallint")
     * @Assert\GreaterThanOrEqual(
     *     value=1,
     *     message="The price of the order must be equal to or over 1."
     * )
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=15, unique=true)
     * @Assert\Type(
     *     type="string",
     *     message="The Reference Code for the order must be a valid string."
     * )
     * @Assert\Length(
     *     min=15,
     *     max=15,
     *     exactMessage="The Reference Code for the order has to be 15 characters long."
     * )
     */
    private $reference;

    /**
     * @ORM\OneToMany(targetEntity="OTS\BillingBundle\Entity\Ticket", mappedBy="order", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $tickets;

    /**
     * @ORM\ManyToOne(targetEntity="OTS\BillingBundle\Entity\Customer", inversedBy="orders", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $customer;

    /**
     * @ORM\OneToOne(targetEntity="OTS\BillingBundle\Entity\Charge", inversedBy="order", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $charge;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TicketOrder
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set type
     *
     * @param boolean $type
     *
     * @return TicketOrder
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return bool
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set nbTickets
     *
     * @param integer $nbTickets
     *
     * @return TicketOrder
     */
    public function setNbTickets($nbTickets)
    {
        $this->nbTickets = $nbTickets;

        return $this;
    }

    /**
     * Get nbTickets
     *
     * @return int
     */
    public function getNbTickets()
    {
        return $this->nbTickets;
    }

    /**
     * Add ticket
     *
     * @param \OTS\BillingBundle\Entity\Ticket $ticket
     *
     * @return TicketOrder
     */
    public function addTicket(Ticket $ticket)
    {
        $this->tickets[] = $ticket;

        $ticket->setOrder($this);

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \OTS\BillingBundle\Entity\Ticket $ticket
     */
    public function removeTicket(Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return TicketOrder
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set customer
     *
     * @param \OTS\BillingBundle\Entity\Customer $customer
     *
     * @return TicketOrder
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        $customer->addOrder($this);

        return $this;
    }

    /**
     * Get customer
     *
     * @return \OTS\BillingBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set charge
     *
     * @param \OTS\BillingBundle\Entity\Charge $charge
     *
     * @return TicketOrder
     */
    public function setCharge(Charge $charge)
    {
        $this->charge = $charge;

        $charge->setOrder($this);

        return $this;
    }

    /**
     * Get charge
     *
     * @return \OTS\BillingBundle\Entity\Charge
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return TicketOrder
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
