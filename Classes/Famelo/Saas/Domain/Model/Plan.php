<?php
namespace Famelo\Saas\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use Famelo\Common\Annotations as Common;

/**
 * @Flow\Entity
 */
class Plan {

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * @var boolean
     */
    protected $active = TRUE;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Famelo\Harvest\Domain\Model\User>
     * @ORM\OneToMany(mappedBy="plan", cascade={"persist"})
     */
    protected $parties;

    /**
     * @var float
     */
    protected $credit = 0;

    /**
     * @var float
     */
    protected $balance = 0;

    /**
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * @var \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\Transaction>
     * @ORM\OneToMany(mappedBy="plan", cascade={"persist"})
     * @ORM\OrderBy({"created" = "DESC"})
     * @Flow\Lazy
     */
    protected $transactions;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\CreditUse>
     * @ORM\OneToMany(mappedBy="plan", cascade={"persist"})
     * @ORM\OrderBy({"created" = "DESC"})
     * @Flow\Lazy
     */
    protected $creditUses;

    /**
     * @Flow\Inject(setting="Plans")
     * @Flow\Transient
     * @var array
     */
    protected $configuration;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     *
     */
    protected $cycle;

    /**
     * @var \DateTime
     * @ORM\Column(nullable=true)
     *
     */
    protected $cycleStart;

    /**
     * @var \DateTime
     * @ORM\Column(nullable=true)
     *
     */
    protected $cycleNext;

    /**
     * @var float
     * @ORM\Column(nullable=true)
     *
     */
    protected $cost;

    /**
     * @var \Famelo\Saas\Domain\Model\Billing
     * @ORM\OneToOne(mappedBy="plan")
     */
    protected $billing;

    /**
     * Upon creation the creationDate property is initialized.
     */
    public function __construct() {
        $this->created = new \DateTime();
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->parties = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        return $this->type;
    }

    /**
     * Gets active.
     *
     * @return boolean $active
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * Sets the active.
     *
     * @param boolean $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
    * TODO: Document this Method! ( getAmount )
    */
    public function getAmount() {
        return $this->configuration[$this->type]['amount'];
    }

    /**
     * Gets balance.
     *
     * @return float $balance
     */
    public function getBalance() {
        return $this->balance;
    }

    /**
     * Sets the balance.
     *
     * @param float $balance
     */
    public function setBalance($balance) {
        $this->balance = $balance;
    }

    /**
     * Sets the balance.
     *
     * @param float $balance
     */
    public function addBalance($balance) {
        $this->balance+= $balance;
    }

    /**
     * Sets the balance.
     *
     * @param float $balance
     */
    public function removeBalance($balance) {
        $this->balance-= $balance;
    }

    /**
     * Gets billing.
     *
     * @return \Famelo\Saas\Domain\Model\Billing $billing
     */
    public function getBilling() {
        return $this->billing;
    }

    /**
     * Sets the billing.
     *
     * @param \Famelo\Saas\Domain\Model\Billing $billing
     */
    public function setBilling($billing) {
        $billing->setPlan($this);
        $this->billing = $billing;
    }

    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

    /**
    * TODO: Document this Method! ( getConfiguration )
    */
    public function getConfiguration() {
        return $this->configuration[$this->type];
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
     * Gets cost.
     *
     * @return float $cost
     */
    public function getCost() {
        return $this->cost;
    }

    /**
     * Sets the cost.
     *
     * @param float $cost
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }

    /**
     * Gets cycleNext.
     *
     * @return \DateTime $cycleNext
     */
    public function getCycleNext() {
        return $this->cycleNext;
    }

    /**
     * Sets the cycleNext.
     *
     * @param \DateTime $cycleNext
     */
    public function setCycleNext($cycleNext) {
        $this->cycleNext = $cycleNext;
    }

    /**
     * Gets cycleStart.
     *
     * @return \DateTime $cycleStart
     */
    public function getCycleStart() {
        return $this->cycleStart;
    }

    /**
     * Sets the cycleStart.
     *
     * @param \DateTime $cycleStart
     */
    public function setCycleStart($cycleStart) {
        $this->cycleStart = $cycleStart;
    }

    /**
     * Gets cycle.
     *
     * @return string $cycle
     */
    public function getCycle() {
        return $this->cycle;
    }

    /**
     * Sets the cycle.
     *
     * @param string $cycle
     */
    public function setCycle($cycle) {
        $this->cycle = $cycle;
    }

    /**
    * TODO: Document this Method! ( getDueAmount )
    */
    public function getDueAmount() {
        return $this->balance * -1;
    }

    /**
    * TODO: Document this Method! ( getImplementation )
    */
    public function getImplementation() {
        return new $this->configuration[$this->type]['implementation']($this);
    }

    /**
    * TODO: Document this Method! ( getMainParty )
    */
    public function getMainParty() {
        return $this->getParties()->first();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParties() {
        return $this->parties;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $parties
     */
    public function setParties($parties) {
        $this->parties = $parties;
    }

    /**
     * @param \Famelo\Harvest\Domain\Model\User $party
     */
    public function addParty($party) {
        $this->parties->add($party);
        $party->setPlan($this);
    }

    /**
     * @param \Famelo\Harvest\Domain\Model\User $party
     */
    public function removeParty($party) {
        $this->parties->remove($party);
    }

    /**
     * Add to the transactions.
     *
     * @param \Famelo\Saas\Domain\Model\Transaction $transaction
     */
    public function addTransaction($transaction) {
        $this->balance += $transaction->getAmount();
        $this->transactions->add($transaction);
        $transaction->setPlan($this);
    }

    /**
     * Remove from transactions.
     *
     * @param \Famelo\Saas\Domain\Model\Transaction $transaction
     */
    public function removeTransaction($transaction) {
        $this->transactions->remove($transaction);
    }

    /**
     * Gets transactions.
     *
     * @return \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\Transaction> $transactions
     */
    public function getTransactions() {
        return $this->transactions;
    }

    /**
     * Sets the transactions.
     *
     * @param \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\Transaction> $transactions
     */
    public function setTransactions($transactions) {
        $this->transactions = $transactions;
    }

    /**
     * Add to the creditUses.
     *
     * @param \Famelo\Saas\Domain\Model\CreditUse $creditUse
     */
    public function addCreditUse($creditUse) {
        $creditUse->setPlan($this);
        $this->creditUses->add($creditUse);
    }

    /**
     * Remove from creditUse.
     *
     * @param \Famelo\Saas\Domain\Model\CreditUse $creditUse
     */
    public function removeCreditUse($creditUse) {
        $this->creditUses->remove($creditUse);
    }

    /**
     * Gets creditUse.
     *
     * @return \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\CreditUse> $creditUse
     */
    public function getCreditUses() {
        return $this->creditUses;
    }

    /**
     * Sets the creditUse.
     *
     * @param \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\CreditUse> $creditUse
     */
    public function setCreditUses($creditUses) {
        $this->creditUses = $creditUses;
    }

    /**
     * Gets type.
     *
     * @return string $type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the type.
     *
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
        if (isset($this->configuration[$this->type]['cycle'])) {
            $this->cycle = $this->configuration[$this->type]['cycle'];
        }
    }

    /**
     * @param float $credit
     */
    public function setCredit($credit) {
        $this->credit = $credit;
    }

    /**
     * @return float
     */
    public function getCredit() {
        return $this->credit;
    }

    /**
     * @param float $credit
     */
    public function addCredit($credit) {
        $this->credit+= $credit;
    }

    /**
     * @param float $credit
     */
    public function removeCredit($credit) {
        $this->credit-= $credit;
    }

}