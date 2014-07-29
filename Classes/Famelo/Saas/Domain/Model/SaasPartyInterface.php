<?php
namespace Famelo\Saas\Domain\Model;

interface SaasPartyInterface {
    /**
     * Gets plan.
     *
     * @return \Famelo\Saas\Domain\Model\Plan $plan
     */
    public function getPlan();

    /**
     * Sets the plan.
     *
     * @param \Famelo\Saas\Domain\Model\Plan $plan
     */
    public function setPlan($plan);
}

?>