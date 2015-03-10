<?php
namespace Famelo\Saas\ViewHelpers\Format;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\I18n\Exception as I18nException;
use TYPO3\Fluid\Core\ViewHelper\AbstractLocaleAwareViewHelper;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Fluid\Core\ViewHelper\Exception as ViewHelperException;
use TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException;

/**
 * Formats a given float to a point representation.
 *
 * @api
 */
class PointsViewHelper extends AbstractViewHelper {

	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @param float $amount
	 * @param string $currency
	 * @return string the formatted points.
	 * @throws ViewHelperException
	 */
	public function render($amount = NULL, $currency = NULL) {
		if ($amount === NULL) {
			$amount = floatval($this->renderChildren());
		}

		$exchangeRates = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Saas.exchangeRates');

		if (is_object($this->transactionService->getPlan()) && $currency === NULL) {
			$currency = $this->transactionService->getPlan()->getCurrency();
		}

		if (isset($exchangeRates[$currency])) {
			$exchangeRate = $exchangeRates[$currency];
			return $amount / $exchangeRate;
		}
	}

}
