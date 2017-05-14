<?php

namespace OTS\BillingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OTS\BillingBundle\Entity\Customer;
use OTS\BillingBundle\Entity\TicketOrder;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Charge
 *
 * @ORM\Table(name="charge")
 * @ORM\Entity(repositoryClass="OTS\BillingBundle\Repository\ChargeRepository")
 */
class Charge
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
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value=1,
     *     message="ots_billing.constraints.charge.amount"
     * )
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3)
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      exactMessage = "ots_billing.constraints.charge.currency"
     * )
     */
    private $currency;

    /**
     * @ORM\ManyToOne(targetEntity="OTS\BillingBundle\Entity\Customer", inversedBy="charges")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $customer;

    /**
     * @ORM\OneToOne(targetEntity="OTS\BillingBundle\Entity\TicketOrder", mappedBy="charge", cascade={"remove"})
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
     * Set amount
     *
     * @param integer $amount
     *
     * @return Charge
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Charge
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set customer
     *
     * @param \OTS\BillingBundle\Entity\Customer $customer
     *
     * @return Charge
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

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
     * Set order
     *
     * @param \OTS\BillingBundle\Entity\TicketOrder $order
     *
     * @return Charge
     */
    public function setOrder(TicketOrder $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \OTS\BillingBundle\Entity\TicketOrder
     */
    public function getOrder()
    {
        return $this->order;
    }
}
