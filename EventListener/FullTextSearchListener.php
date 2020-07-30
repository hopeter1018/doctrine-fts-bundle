<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\EventListener;

use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use HoPeter1018\DoctrineFullTextSearchBundle\Entity\FullTextSearchIndex;
use HoPeter1018\DoctrineFullTextSearchBundle\Services\IndexManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class FullTextSearchListener implements EventSubscriber
{
    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var IndexManager */
    protected $indexManager;

    public function __construct(PropertyAccessorInterface $propertyAccessor, IndexManager $indexManager)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->indexManager = $indexManager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            // Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        // $classMetadata = $event->getClassMetadata();
        // $class = $classMetadata->name;
        // $driver = $event->getEntityManager()->getConfiguration()->getMetadataDriverImpl();
        //
        // $indexes = $this->indexManager->parseAnno($class);
        // if (false === $indexes['_']) {
        //     return;
        // }
        // dump($indexes);
        // if (!isset($classMetadata->table['indexes'])) {
        //     $classMetadata->table['indexes'] = [];
        // }
        // // $classMetadata->mapField([
        // //     'fieldName' => 'ftsDefault',
        // //     'type' => 'text',
        // // ]);
        // if (!isset($classMetadata->table['indexes']['fts_index'])) {
        //     $classMetadata->table['indexes']['fts_default'] = [
        //         'columns' => ['fts_default'],
        //         'flags' => ['fulltext'],
        //     ];
        // }
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->doIndex($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->doIndex($event);
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $em = $event->getObjectManager();
        $entity = $event->getObject();
        $entityFqcn = get_class($event->getObject());
        $classMetadata = $em->getClassMetadata($entityFqcn);
        $fullTextSearchIndexRepo = $em->getRepository(FullTextSearchIndex::class);
        $id = $this->propertyAccessor->getValue($entity, $classMetadata->getIdentifierFieldNames()[0]);
        $removed = $fullTextSearchIndexRepo->remove($entityFqcn, $id);
    }

    protected function doIndex(LifecycleEventArgs $event)
    {
        $em = $event->getObjectManager();
        $entity = $event->getObject();
        $entityFqcn = get_class($event->getObject());
        $classMetadata = $em->getClassMetadata($entityFqcn);

        $indexes = $this->indexManager->parseAnno($entityFqcn);
        if (false === $indexes['_']) {
            return;
        }

        $fullTextSearchIndexRepo = $em->getRepository(FullTextSearchIndex::class);
        $indexes = $this->indexManager->index($entity);
        $id = $this->propertyAccessor->getValue($entity, $classMetadata->getIdentifierFieldNames()[0]);

        $get = [];
        $get = $fullTextSearchIndexRepo->get($entityFqcn, $id);

        foreach (['group', 'prop', 'method'] as $type) {
            foreach ($indexes[$type] as $field => $content) {
                if (isset($get[$field])) {
                    $ftsIndex = $get[$field];
                    $ftsIndex->setContent($content);
                    $ftsIndex->setUpdated(new DateTime());
                } else {
                    $ftsIndex = $fullTextSearchIndexRepo->newInstance($entityFqcn, $id, $field, $content);
                }
                $em->persist($ftsIndex);
            }
        }
        $em->flush();
    }
}
