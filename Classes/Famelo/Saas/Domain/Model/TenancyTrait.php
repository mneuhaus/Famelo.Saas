<?php
namespace Famelo\Saas\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

trait TenancyTrait {
    /**
     * @var \TYPO3\Party\Domain\Model\AbstractParty
     * @ORM\ManyToOne
     */
    protected $tenant;

    /**
     * @param \TYPO3\Party\Domain\Model\AbstractParty $tenant
     */
    public function setTenant($tenant) {
        $this->tenant = $tenant;
    }

    /**
     * @return \TYPO3\Party\Domain\Model\AbstractParty
     */
    public function getTenant() {
        return $this->tenant;
    }
}

?>