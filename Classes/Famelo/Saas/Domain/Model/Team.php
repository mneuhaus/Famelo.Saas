<?php
namespace Famelo\Saas\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use Famelo\Common\Annotations as Common;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Flow\Entity
 * @Common\Accessable
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Team {
	/**
	 * @var \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\User>
	 * @ORM\OneToMany(mappedBy="team", cascade={"persist"})
	 * @Flow\Lazy
	 */
	protected $users;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var \Famelo\Saas\Domain\Model\Subscription
	 * @ORM\OneToOne(mappedBy="team")
	 * @Flow\Lazy
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

	public function __construct() {
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
		$this->users->add(new User);
	}

	/**
	 * @ORM\PrePersist
	 */
	public function updateUsers() {
		foreach ($this->users as $user) {
			$user->setTeam($this);
		}
	}

	public function __toString() {
		return $this->name;
	}
}

?>