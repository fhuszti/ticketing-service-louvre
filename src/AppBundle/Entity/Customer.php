<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Charge;
use AppBundle\Entity\TicketOrder;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 */
class Customer
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
     * @ORM\Column(name="stripe_id", type="string")
     * @Assert\NotBlank(message="core.constraints.customer.stripe_id")
     */
    private $stripe_id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(
     *     message = "core.constraints.customer.email"
     * )
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Charge", mappedBy="customer", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $charges;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TicketOrder", mappedBy="customer", cascade={"remove"})
     * @Assert\Valid()
     */
    private $orders;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->charges = new ArrayCollection();
        $this->orders = new ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add charge
     *
     * @param \AppBundle\Entity\Charge $charge
     *
     * @return Customer
     */
    public function addCharge(Charge $charge)
    {
        $this->charges[] = $charge;

        $charge->setCustomer($this);

        return $this;
    }

    /**
     * Remove charge
     *
     * @param \AppBundle\Entity\Charge $charge
     */
    public function removeCharge(Charge $charge)
    {
        $this->charges->removeElement($charge);
    }

    /**
     * Get charges
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\TicketOrder $order
     *
     * @return Customer
     */
    public function addOrder(TicketOrder $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\TicketOrder $order
     */
    public function removeOrder(TicketOrder $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set stripeId
     *
     * @param string $stripeId
     *
     * @return Customer
     */
    public function setStripeId($stripeId)
    {
        $this->stripe_id = $stripeId;

        return $this;
    }

    /**
     * Get stripeId
     *
     * @return string
     */
    public function getStripeId()
    {
        return $this->stripe_id;
    }
}
