<?php
namespace Famelo\Saas\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

trait SaasParty {
    /**
     * @var \Famelo\Saas\Domain\Model\Plan
     * @ORM\ManyToOne(inversedBy="parties")
     */
    protected $plan;

    /**
     * Gets plan.
     *
     * @return \Famelo\Saas\Domain\Model\Plan $plan
     */
    public function getPlan() {
        return $this->plan;
    }

    /**
     * Sets the plan.
     *
     * @param \Famelo\Saas\Domain\Model\Plan $plan
     */
    public function setPlan($plan) {
        $this->plan = $plan;
    }
}

?>