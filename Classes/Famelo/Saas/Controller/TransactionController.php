<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Model\Transaction;
use Famelo\Saas\Domain\Repository\TransactionRepository;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Utility\Algorithms;

/**
 * @Flow\Scope("singleton")
 */
class TransactionController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var TransactionRepository
	 */
	protected $transactionRepository;

	/**
	 * @return string
	 */
	public function indexAction() {
		$this->view->assign('transactions', $this->transactionRepository->findAll());
	}
}

?>
