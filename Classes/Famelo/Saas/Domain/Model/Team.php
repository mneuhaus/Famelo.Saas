<?php
namespace Famelo\Saas\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use Famelo\Common\Annotations as Common;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Flow\Entity
 * @ORM\HasLifecycleCallbacks
 * @Common\Accessable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Team {

    /**
     * @var \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\User>
     * @ORM\OneToMany(mappedBy="team", cascade={"persist"})
     */
    protected $users;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var \Famelo\Saas\Domain\Model\Subscription
     * @ORM\OneToOne(mappedBy="team")
     */
    protected $subscription;

    /**
     * @var boolean
     */
    protected $active = FALSE;

    /**
     * @var \DateTime
     * @ORM\Column(nullable=true)
     */
    protected $deletedAt;

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $country = 'DE';

    /**
     * @var boolean
     */
    protected $notify;

    /**
    * TODO: Document this Method! ( __construct )
    */
    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdentifier() {
        return $this->Persistence_Object_Identifier;
    }

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        if ($this->name == '') {
            return $this->getMainUser()->__toString();
        }
        return $this->name . ', ' . $this->street . ', ' . $this->zip . ' ' . $this->city;
    }

    public function getCurrency() {
        return $this->subscription->getCurrency();
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
    * TODO: Document this Method! ( getBookingUser )
    */
    public function getMainUser() {
        return $this->getUsers()->first();
    }

    /**
    * TODO: Document this Method! ( getBookingUser )
    */
    public function getBookingUser() {
        return $this->getUsers()->first();
    }

    /**
     * Gets deletedAt.
     *
     * @return \DateTime $deletedAt
     */
    public function getDeletedAt() {
        return $this->deletedAt;
    }

    /**
     * Sets the deletedAt.
     *
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt) {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Gets name.
     *
     * @return string $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
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
     * Add to the users.
     *
     * @param \Famelo\Saas\Domain\Model\User $user
     */
    public function addUser($user) {
        $user->setTeam($this);
        $this->users->add($user);
    }

    /**
     * Remove from users.
     *
     * @param \Famelo\Saas\Domain\Model\User $user
     */
    public function removeUser($user) {
        $this->users->remove($user);
    }

    /**
     * Gets users.
     *
     * @return \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\User> $users
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Sets the users.
     *
     * @param \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\User> $users
     */
    public function setUsers($users) {
        $this->users = $users;
    }

    /**
     * @ORM\PrePersist
     */
    public function updateUsers() {
        foreach ($this->users as $user) {
            $user->setTeam($this);
        }
    }

}

?>