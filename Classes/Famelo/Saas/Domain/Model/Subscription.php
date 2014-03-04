<?php
namespace Famelo\Saas\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use Famelo\Common\Annotations as Common;

/**
 * A person
 *
 * @Flow\Entity
 * @Common\Accessable
 */
class Subscription {

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
    protected $plan;

    /**
     * @var \Famelo\Saas\Domain\Model\Team
     * @ORM\OneToOne(mappedBy="subscription")
     */
    protected $team;

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
     * @ORM\OneToMany(mappedBy="subscription", cascade={"persist"})
     * @ORM\OrderBy({"created" = "DESC"})
     * @Flow\Lazy
     */
    protected $transactions;

    /**
     * Upon creation the creationDate property is initialized.
     */
    public function __construct() {
        $this->created = new \DateTime();
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        return $this->plan;
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
     * Gets plan.
     *
     * @return string $plan
     */
    public function getPlan() {
        return $this->plan;
    }

    /**
     * Sets the plan.
     *
     * @param string $plan
     */
    public function setPlan($plan) {
        $this->plan = $plan;
    }

    /**
    * TODO: Document this Method! ( getTeam )
    */
    public function getTeam() {
        return $this->team;
    }

    /**
     * Sets the team.
     *
     * @param \Famelo\Saas\Domain\Model\Team $team
     */
    public function setTeam($team) {
        $this->team = $team;
    }

    /**
     * Add to the transactions.
     *
     * @param \Famelo\Saas\Domain\Model\Transaction $transaction
     */
    public function addTransaction($transaction) {
        $this->transactions->add($transaction);
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

}

?>