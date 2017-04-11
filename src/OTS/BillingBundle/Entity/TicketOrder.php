<?php

namespace OTS\BillingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TicketOrder
 *
 * @ORM\Table(name="ticket_order")
 * @ORM\Entity(repositoryClass="OTS\BillingBundle\Repository\TicketOrderRepository")
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
     * @ORM\OneToMany(targetEntity="OTS\BillingBundle\Entity\Ticket", mappedBy="order")
     * @Assert\Valid()
     */
    private $tickets;



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
    public function addTicket(\OTS\BillingBundle\Entity\Ticket $ticket)
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
    public function removeTicket(\OTS\BillingBundle\Entity\Ticket $ticket)
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
}
