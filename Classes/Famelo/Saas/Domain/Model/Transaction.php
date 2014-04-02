<?php
namespace Famelo\Saas\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use Famelo\Common\Annotations as Common;
use Famelo\Saas\Domain\Model\Subscription;
use TYPO3\Flow\Annotations as Flow;

/**
 * A transaction
 *
 * @Flow\Entity
 * @Common\Accessable
 */
class Transaction {
    const STATE_OPEN = 'open';
    const STATE_PAID = 'paid';
    const STATE_CANCELED = 'canceled';

    /**
     * @var \Famelo\Saas\Domain\Model\Subscription
     * @ORM\ManyToOne(inversedBy="transactions")
     */
    protected $subscription;

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $note;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $paymentGateway;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $invoiceNumber;

    /**
     * @var string
     */
    protected $state;

    /**
    * TODO: Document this Method! ( __construct )
    */
    public function __construct() {
        $this->created = new \DateTime();
        $this->state = self::STATE_OPEN;
    }

    public function getTeam() {
        if ($this->subscription instanceof Subscription) {
            return $this->subscription->getTeam();
        }
    }

    /**
     * Gets amount.
     *
     * @return float $amount
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Sets the amount.
     *
     * @param float $amount
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * Gets created.
     *
     * @return DateTime $created
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Sets the created.
     *
     * @param DateTime $created
     */
    public function setCreated($created) {
        $this->created = $created;
    }

    /**
     * Gets currency.
     *
     * @return string $currency
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets the currency.
     *
     * @param string $currency
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
    * TODO: Document this Method! ( getIdentifier )
    */
    public function getIdentifier() {
        return $this->Persistence_Object_Identifier;
    }

    /**
     * Gets invoiceNumber.
     *
     * @return string $invoiceNumber
     */
    public function getInvoiceNumber() {
        return $this->invoiceNumber;
    }

    /**
     * Sets the invoiceNumber.
     *
     * @param string $invoiceNumber
     */
    public function setInvoiceNumber($invoiceNumber) {
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
    * TODO: Document this Method! ( getInvoicePath )
    */
    public function getInvoicePath() {
        return FLOW_PATH_DATA . 'Invoices/' . $this->getIdentifier() . '/' . $this->getInvoiceNumber() . '.pdf';
    }

    /**
     * Gets note.
     *
     * @return string $note
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * Sets the note.
     *
     * @param string $note
     */
    public function setNote($note) {
        $this->note = $note;
    }

    /**
     * Gets paymentGateway.
     *
     * @return string $paymentGateway
     */
    public function getPaymentGateway() {
        return $this->paymentGateway;
    }

    /**
     * Sets the paymentGateway.
     *
     * @param string $paymentGateway
     */
    public function setPaymentGateway($paymentGateway) {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Gets subscription.
     *
     * @return \Famelo\Saas\Domain\Model\Subscription $subscription
     */
    public function getSubscription() {
        return $this->subscription;
    }

    /**
     * Sets the subscription.
     *
     * @param \Famelo\Saas\Domain\Model\Subscription $subscription
     */
    public function setSubscription($subscription) {
        $this->subscription = $subscription;
    }

    /**
     * @param string $state
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState() {
        if (empty($this->state)) {
            return self::STATE_OPEN;
        }
        return $this->state;
    }

}

?>