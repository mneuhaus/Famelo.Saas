<?php
namespace Famelo\Saas\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use Famelo\Common\Annotations as Common;
use TYPO3\Flow\Annotations as Flow;
use Famelo\Saas\Annotations as Saas;
/**
 * A transaction
 *
 * @Flow\Entity
 */
class CreditUse {

    /**
     * @var \Famelo\Saas\Domain\Model\Plan
     * @ORM\ManyToOne(inversedBy="creditUses")
     */
    protected $plan;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var string
     */
    protected $reference;

    public function __construct() {
        $this->created = new \DateTime();
    }

    /**
     * Gets created.
     *
     * @return DateTime $created
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Sets the created.
     *
     * @param DateTime $created
     */
    public function setCreated($created) {
        $this->created = $created;
    }

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

    /**
     * @param string $reference
     */
    public function setReference($reference) {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReference() {
        return $this->reference;
    }
}

?>