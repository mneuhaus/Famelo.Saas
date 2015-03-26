<?php
namespace Famelo\Saas\Controller;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class InvoiceController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 *
	 * @param \Famelo\Saas\Domain\Model\Transaction $transaction
	 * @return string
	 */
	public function indexAction($transaction) {
		$document = new \Famelo\PDF\Document('Famelo.Saas:Invoice');
        $document->assign('transaction', $transaction);
        echo $document->send();
        exit();
	}
}

?>