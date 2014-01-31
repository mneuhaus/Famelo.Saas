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
class Plan {
	const TYPE_FLATRATE = 'Flatrate';
	const TYPE_DEPOSIT = 'Deposit';
	const TYPE_BILLING = 'Billing';

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $cycle = '1 month';

	/**
	 * @var float
	 */
	protected $price;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Famelo\Saas\Domain\Model\User>
	 * @ORM\OneToMany(mappedBy="plan", cascade={"persist"})
	 * @Flow\Lazy
	 */
	protected $users;

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
		// $this->creationDate = new \DateTime();
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
	}
}

?>