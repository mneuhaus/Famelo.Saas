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
		$invoice = $transaction->getInvoicePath();
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="Broensfin - Invoice - ' . basename($invoice) . '"');
		readfile($invoice);
	}
}

?>