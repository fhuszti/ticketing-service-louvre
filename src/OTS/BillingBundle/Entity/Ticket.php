<?php

namespace OTS\BillingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OTS\BillingBundle\Entity\TicketOrder;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="OTS\BillingBundle\Repository\TicketRepository")
 */
class Ticket
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
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Assert\Type(
     *     type="string",
     *     message="ots_billing.constraints.ticket.first_name.type"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="ots_billing.constraints.ticket.first_name.min",
     *     maxMessage="ots_billing.constraints.ticket.first_name.max"
     * )
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Type(
     *     type="string",
     *     message="ots_billing.constraints.ticket.name.type"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="ots_billing.constraints.ticket.name.min",
     *     maxMessage="ots_billing.constraints.ticket.name.max"
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     * @Assert\Type(
     *     type="string",
     *     message="ots_billing.constraints.ticket.country.type"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="ots_billing.constraints.ticket.country.min",
     *     maxMessage="ots_billing.constraints.ticket.country.max"
     * )
     */
    private $country;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="datetime")
     * @Assert\DateTime(message="ots_billing.constraints.ticket.birth_date")
     */
    private $birthDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="discounted", type="boolean")
     * @Assert\Type(
     *     type="bool",
     *     message="ots_billing.constraints.ticket.discounted"
     * )
     */
    private $discounted;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value=0,
     *     message="ots_billing.constraints.ticket.price"
     * )
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="OTS\BillingBundle\Entity\TicketOrder", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $order;


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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Ticket
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Ticket
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Ticket
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Ticket
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set discounted
     *
     * @param boolean $discounted
     *
     * @return Ticket
     */
    public function setDiscounted($discounted)
    {
        $this->discounted = $discounted;

        return $this;
    }

    /**
     * Get discounted
     *
     * @return bool
     */
    public function getDiscounted()
    {
        return $this->discounted;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Ticket
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
     * Set order
     *
     * @param \OTS\BillingBundle\Entiy\TicketOrder $order
     *
     * @return Ticket
     */
    public function setOrder(TicketOrder $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \OTS\BillingBundle\Entiy\TicketOrder
     */
    public function getOrder()
    {
        return $this->order;
    }
}
