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

	public function __construct() {
		parent::__construct();
		$this->accounts->add(new \TYPO3\Flow\Security\Account);
	}

	/**
	 * @ORM\PrePersist
	 */
	public function updateAccounts() {
		foreach ($this->accounts as $account) {
			$account->setParty($this);
		}
	}

	public function __toString() {
		return $this->name->__toString();
	}
}

?>