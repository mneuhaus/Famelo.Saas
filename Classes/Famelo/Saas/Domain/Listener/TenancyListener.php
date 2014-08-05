<?php
namespace Famelo\Saas\Domain\Listener;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Famelo\Saas\Annotations\Tenancy;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Security\Context;

class TenancyListener implements EventSubscriber {

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
     * {@inheritdoc}
     */
    public function getSubscribedEvents() {
        return array(
            'onFlush'
        );
    }

    /**
     * If it's a SoftDeleteable object, update the "deletedAt" field
     * and skip the removal of the object
     *
     * @param EventArgs $args
     * @return void
     */
    public function onFlush(EventArgs $args) {
        if (FLOW_SAPITYPE === 'CLI') {
            return '1 = 1';
        }

        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $party = $this->securityContext->getParty();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $className = get_class($entity);
            $classMetadata = $entityManager->getClassMetadata($className);
            $annotation = $this->reflectionService->getClassAnnotation($className, 'Famelo\Saas\Annotations\Tenancy');
            if ($annotation instanceof Tenancy) {
                $tenantRole = $annotation->tenantRole !== NULL ? $annotation->tenantRole : $this->defaultTenantRole;
                if (!$this->securityContext->hasRole($tenantRole)) {
                    continue;
                }
                $entity->setTenant($party);
                $unitOfWork->propertyChanged($entity, 'tenant', NULL, $party);
            }
        }
    }

}
