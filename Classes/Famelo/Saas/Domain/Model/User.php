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
 * @ORM\HasLifecycleCallbacks
 */
class User extends \TYPO3\Party\Domain\Model\Person {

    /**
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Flow\Security\Account>
     * @ORM\OneToMany(mappedBy="party", cascade={"persist"})
     * @Flow\Lazy
     */
    protected $accounts;

    /**
     * The phone
     * @var string
     */
    protected $phone = '';

    /**
     * The mobile
     * @var string
     */
    protected $mobile = '';

    /**
     * The email
     * @var string
     */
    protected $email;

    /**
     * @var \Famelo\Saas\Domain\Model\Team
     * @ORM\ManyToOne(inversedBy="users")
     */
    protected $team;

    /**
    * TODO: Document this Method! ( __construct )
    */
    public function __construct() {
        parent::__construct();
        $this->accounts->add(new \TYPO3\Flow\Security\Account());
    }

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        return $this->name->__toString();
    }

    /**
     * Add to the accounts.
     *
     * @param \TYPO3\Flow\Security\Account $account
     */
    public function addAccount(\TYPO3\Flow\Security\Account $account) {
        $this->accounts->add($account);
    }

    /**
     * Remove from accounts.
     *
     * @param \TYPO3\Flow\Security\Account $account
     */
    public function removeAccount(\TYPO3\Flow\Security\Account $account) {
        $this->accounts->remove($account);
    }

    /**
     * Gets accounts.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Flow\Security\Account> $accounts
     */
    public function getAccounts() {
        return $this->accounts;
    }

    /**
     * Sets the accounts.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Flow\Security\Account> $accounts
     */
    public function setAccounts($accounts) {
        $this->accounts = $accounts;
    }

    /**
     * @ORM\PrePersist
     */
    public function updateAccounts() {
        foreach ($this->accounts as $account) {
            $account->setParty($this);
        }
    }

    /**
     * Gets email.
     *
     * @return string $email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Gets mobile.
     *
     * @return string $mobile
     */
    public function getMobile() {
        return $this->mobile;
    }

    /**
     * Sets the mobile.
     *
     * @param string $mobile
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    /**
     * Gets phone.
     *
     * @return string $phone
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Sets the phone.
     *
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    /**
     * Gets team.
     *
     * @return \Famelo\Saas\Domain\Model\Team $team
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

}

?>