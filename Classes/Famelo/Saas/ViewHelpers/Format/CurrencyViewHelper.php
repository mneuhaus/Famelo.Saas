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
use TYPO3\Flow\I18n\Exception as I18nException;
use TYPO3\Fluid\Core\ViewHelper\AbstractLocaleAwareViewHelper;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException;
use TYPO3\Fluid\Core\ViewHelper\Exception as ViewHelperException;

/**
 * Formats a given float to a currency representation.
 *
 * @api
 */
class CurrencyViewHelper extends \TYPO3\Fluid\ViewHelpers\Format\CurrencyViewHelper {

	/**
	 * @var \Famelo\Saas\Services\I18nService
	 * @Flow\Inject
	 */
	protected $i18nService;

	/**
	 * @var \Famelo\Saas\Domain\Service\TransactionService
	 * @Flow\Inject
	 */
	protected $transactionService;

	/**
	 * @param string $currencySign (optional) The currency sign, eg $ or €.
	 * @param string $decimalSeparator (optional) The separator for the decimal point.
	 * @param string $thousandsSeparator (optional) The thousands separator.
	 * @param string $currency (optional) The iso code for the currency locale
	 *
	 * @throws \TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException
	 * @return string the formatted amount.
	 * @throws ViewHelperException
	 * @api
	 */
	public function render($currencySign = '', $decimalSeparator = ',', $thousandsSeparator = '.', $currency = NULL) {
		$useLocale = $this->getLocale();

		if ($currency === NULL) {
			$currency = $this->transactionService->getSubscription()->getCurrency();
		}

		$currency = $this->i18nService->getCurrency($currency, $useLocale);
		$currencySign = $currency['symbol'];

		return parent::render($currencySign, $decimalSeparator, $thousandsSeparator);
	}

}
