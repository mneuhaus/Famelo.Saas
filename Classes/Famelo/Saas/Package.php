<?php
namespace Famelo\Saas;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Package\Package as BasePackage;

/**
 * The TYPO3 Flow Package
 *
 */
class Package extends BasePackage {
	/**
	 * Invokes custom PHP code directly after the package manager has been initialized.
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(Bootstrap $bootstrap) {
		$dispatcher = $bootstrap->getSignalSlotDispatcher();
		$dispatcher->connect('TYPO3\Flow\Configuration\ConfigurationManager', 'configurationManagerReady', function(ConfigurationManager $configurationManager) {
			$configurationManager->registerConfigurationType('Rules', ConfigurationManager::CONFIGURATION_PROCESSING_TYPE_APPEND);
		});

		$dispatcher->connect(
			'TYPO3\Flow\Mvc\ActionRequest', 'requestDispatched',
			'Famelo\Saas\Services\RedirectService', 'injectCurrentRequest'
		);
	}
}
