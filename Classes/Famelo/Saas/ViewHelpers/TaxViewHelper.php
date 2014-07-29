<?php
namespace Famelo\Saas\ViewHelpers;

/*                                                                        *
 * This script belongs to the Flow package "TYPO3.Expose".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;

/**
 * @api
 */
class TaxViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @param \Famelo\Saas\Domain\Model\Transaction $transaction
	 * @param array $tax
	 * @return string Rendered string
	 * @api
	 */
	public function render($transaction, $tax = NULL) {
		return $tax;
		$country = $transaction->getCountry();

		$taxes = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Saas.taxes');

		if (isset($taxes[$country]) && $tax === NULL) {
			$tax = $taxes[$country];
			switch ($tax['type']) {
				case 'included':
				default:
					$tax['total'] = $transaction->getAmount();
					$tax['subtotal'] = $tax['total'] / (100 + $tax['amount']) * 100;
					$tax['tax'] = $tax['total'] - $tax['subtotal'];
					break;
			}
		}

		$this->templateVariableContainer->add('tax', $tax);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('tax');

		return $content;
	}
}

?>