<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Model\Billing;
use Famelo\Saas\Domain\Model\Plan;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
/**
 * @Flow\Scope("singleton")
 */
class BillingController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 *
	 * @return string
	 */
	public function editAction() {
		$party = $this->securityContext->getParty();
		$plan = $party->getPlan();
		$billing = $plan->getBilling();
		if ($billing === NULL) {
			$billing = new Billing();
		}
		$this->view->assign('billing', $billing);
	}

	/**
	 *
	 * @param Billing $billing
	 * @return string
	 */
	public function updateAction(Billing $billing) {
		$party = $this->securityContext->getParty();
		$plan = $party->getPlan();
		if ($this->persistenceManager->isNewObject($billing)) {
			$plan->setBilling($billing);
			$this->persistenceManager->add($billing);
			$this->persistenceManager->update($plan);
			$this->persistenceManager->persistAll();
		} else {
			$this->persistenceManager->update($billing);
		}
		$this->flashMessageContainer->addMessage(new Message('Your Billing information were updated.'));
		$this->redirect('edit');
	}

	/**
	 * A template method for displaying custom error flash messages, or to
	 * display no flash message at all on errors. Override this to customize
	 * the flash message in your action controller.
	 *
	 * @return \TYPO3\Flow\Error\Message The flash message or FALSE if no flash message should be set
	 * @api
	 */
	protected function getErrorFlashMessage() {
		return FALSE;
	}
}
?>