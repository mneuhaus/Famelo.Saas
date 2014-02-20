<?php
namespace Famelo\Saas\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Saas"			  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * OptionsProvider for related Plans
 *
 */
class PlanOptionsProvider extends \TYPO3\Expose\Core\OptionsProvider\AbstractOptionsProvider {

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @return array
	 */
	public function getOptions() {
		$planDefinitions = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Saas.Plans');
		$plans = array();
		foreach ($planDefinitions as $planKey => $planDefinition) {
			$plans[$planKey] = $planDefinition['name'];
		}
		return $plans;
	}

}

?>