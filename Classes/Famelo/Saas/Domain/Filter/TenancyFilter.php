<?php
namespace Famelo\Saas\Domain\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Query\ParameterTypeInferer;
use Famelo\Saas\Annotations\Tenancy;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Security\Context;

class TenancyFilter {
    protected $listener;
    protected $entityManager;
    protected $disabled = array();

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject(setting="Roles.OwnerRole", package="Famelo.Saas")
     * @var string
     */
    protected $defaultOwnerRole;

    /**
     * @Flow\Inject(setting="Roles.TenantRole", package="Famelo.Saas")
     * @var string
     */
    protected $defaultTenantRole;

    /**
     * The entity manager.
     * @var EntityManager
     */
    private $em;

    /**
     * Parameters for the filter.
     * @var array
     */
    private $parameters;

    /**
     * Constructs the SQLFilter object.
     *
     * @param EntityManager $em The EM
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias) {
        if (FLOW_SAPITYPE === 'CLI') {
            return '1 = 1';
        }

        $className = $targetEntity->getName();

        $annotation = $this->reflectionService->getClassAnnotation($className, 'Famelo\Saas\Annotations\Tenancy');
        if (!$annotation instanceof Tenancy) {
            return '1 = 1';
        }

        $ownerRole = $annotation->ownerRole !== NULL ? $annotation->ownerRole : $this->defaultOwnerRole;
        if ($this->securityContext->hasRole($ownerRole)) {
            return '1 = 1';
        }

        $party = $this->securityContext->getParty();
        if ($party === NULL) {
            return '1 = 0';
        }

        $identifier = $this->persistenceManager->getIdentifierByObject($party);

        return 'tenant = "' . $identifier . '"';
    }

    public function disableForEntity($class) {
        $this->disabled[$class] = true;
    }

    public function enableForEntity($class) {
        $this->disabled[$class] = false;
    }

    protected function getListener() {
        if ($this->listener === null) {
            $em = $this->getEntityManager();
            $evm = $em->getEventManager();

            foreach ($evm->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($listener instanceof TenancyFilter) {
                        $this->listener = $listener;

                        break 2;
                    }
                }
            }

            if ($this->listener === null) {
                throw new \RuntimeException('Listener "SoftDeleteableListener" was not added to the EventManager!');
            }
        }

        return $this->listener;
    }

    protected function getEntityManager() {
        if ($this->entityManager === null) {
            $refl = new \ReflectionProperty('Doctrine\ORM\Query\Filter\SQLFilter', 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }

    /**
     * Sets a parameter that can be used by the filter.
     *
     * @param string $name Name of the parameter.
     * @param string $value Value of the parameter.
     * @param string $type The parameter type. If specified, the given value will be run through
     *                     the type conversion of this type. This is usually not needed for
     *                     strings and numeric types.
     *
     * @return SQLFilter The current SQL filter.
     */
    public function setParameter($name, $value, $type = null) {
        if (null === $type) {
            $type = ParameterTypeInferer::inferType($value);
        }

        $this->parameters[$name] = array('value' => $value, 'type' => $type);

        // Keep the parameters sorted for the hash
        ksort($this->parameters);

        // The filter collection of the EM is now dirty
        $this->em->getFilters()->setFiltersStateDirty();

        return $this;
    }

    /**
     * Gets a parameter to use in a query.
     *
     * The function is responsible for the right output escaping to use the
     * value in a query.
     *
     * @param string $name Name of the parameter.
     *
     * @return string The SQL escaped parameter to use in a query.
     */
    public function getParameter($name) {
        if (!isset($this->parameters[$name])) {
            throw new \InvalidArgumentException("Parameter '" . $name . "' does not exist.");
        }

        return $this->em->getConnection()->quote($this->parameters[$name]['value'], $this->parameters[$name]['type']);
    }

    /**
     * Returns as string representation of the SQLFilter parameters (the state).
     *
     * @return string String representation of the SQLFilter.
     */
    public function __toString() {
        return serialize($this->parameters);
    }
}
