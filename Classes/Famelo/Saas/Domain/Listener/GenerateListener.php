<?php
namespace Famelo\Saas\Domain\Listener;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Security\Context;

class GenerateListener implements EventSubscriber {

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

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
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $className = get_class($entity);
            $propertyNames = $this->reflectionService->getPropertyNamesByAnnotation($className, 'Famelo\Saas\Annotations\GenerateValue');
            foreach ($propertyNames as $propertyName) {
                $annotation = $this->reflectionService->getPropertyAnnotation($className, $propertyName, 'Famelo\Saas\Annotations\GenerateValue');
                $options = $annotation->options;
                switch ($options['generator']) {
                    case 'integer':
                            $query = $entityManager->createQuery('SELECT i FROM ' . $className . ' i ORDER BY i.number DESC');
                            $query->setMaxResults(1);
                            $results = $query->getResult();

                            if (count($results) === 1) {
                                $result = current($results);
                                $generatedValue = ObjectAccess::getProperty($result, $propertyName);
                                $generatedValue+= $options['increment'];
                            } else {
                                $generatedValue = $options['start'];
                            }

                            ObjectAccess::setProperty($entity, $propertyName, $generatedValue);
                            $unitOfWork->propertyChanged($entity, $propertyName, NULL, $generatedValue);
                        break;
                }
            }
        }
    }

}
