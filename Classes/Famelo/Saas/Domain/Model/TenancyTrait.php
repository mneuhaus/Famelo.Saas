<?php
namespace Famelo\Saas\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

trait TenancyTrait {
    /**
     * @var \Famelo\Saas\Domain\Model\SaasPartyInterface
     * @ORM\ManyToOne
     */
    protected $tenant;

    /**
     * @param \Famelo\Saas\Domain\Model\SaasPartyInterface $tenant
     */
    public function setTenant($tenant) {
        $this->tenant = $tenant;
    }

    /**
     * @return \Famelo\Saas\Domain\Model\SaasPartyInterface
     */
    public function getTenant() {
        return $this->tenant;
    }
}

?>