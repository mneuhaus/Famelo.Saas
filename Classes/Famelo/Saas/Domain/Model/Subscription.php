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
	 * @Flow\Lazy
	 */
	protected $team;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\Transaction>
	 * @ORM\OneToMany(mappedBy="plan", cascade={"persist"})
	 * @Flow\Lazy
	 */
	// protected $transactions;

	/**
	 * Upon creation the creationDate property is initialized.
	 */
	public function __construct() {
		$this->created = new \DateTime();
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
		$this->groups = new \Doctrine\Common\Collections\ArrayCollection();
	}
}

?>