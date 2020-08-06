<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Repository;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use HoPeter1018\DoctrineFullTextSearchBundle\Entity\FullTextSearchIndex;
use HoPeter1018\DoctrineFullTextSearchBundle\Services\IndexManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

// use whatwedo\SearchBundle\Annotation\Searchable;
// use whatwedo\SearchBundle\Entity\PostSearchInterface;
// use whatwedo\SearchBundle\Entity\PreSearchInterface;

/**
 * Class FullTextSearchIndexRepository.
 */
class FullTextSearchIndexRepository extends ServiceEntityRepository
{
    /** @var IndexManager */
    protected $indexManager;

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    public function __construct(RegistryInterface $registry, IndexManager $indexManager, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($registry, FullTextSearchIndex::class);
        $this->indexManager = $indexManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function query($model, $foreignId, $fields = [], $indexBy = null)
    {
        $qb = $this->createQueryBuilder('i', $indexBy);

        if ($model) {
            $qb->andWhere('i.model = :model')->setParameter('model', $model);
        }

        if ($foreignId) {
            $fieldName = $this->getForeignIdFieldName($model);
            $qb->andWhere("i.{$fieldName} = :foreignId")->setParameter('foreignId', $foreignId);
        }

        if (count($fields) > 0) {
            $qb->andWhere('i.field in :fieldNames')->setParameter('fieldNames', $fields);
        }

        return $qb;
    }

    public function countIndex($model, $foreignId, $fields = [])
    {
        $qb = $this->query($model, $foreignId, $fields);

        return $qb->count()
          ->getQuery()
          ->getResult();
    }

    public function select($model, $foreignId, $fields = [])
    {
        $qb = $this->query($model, $foreignId, $fields);

        return $qb->select()
          ->getQuery()
          ->getResult();
    }

    public function remove($model, $foreignId, $fields = [])
    {
        $qb = $this->query($model, $foreignId, $fields);

        if (null !== $foreignId and '' !== $foreignId) {
            return $qb->delete()
              ->getQuery()
              ->execute();
        } else {
            return 0;
        }
    }

    public function get($model, $foreignId, $fields = [])
    {
        $qb = $this->query($model, $foreignId, $fields, 'i.field');

        return $qb->select()
          ->getQuery()
          ->getResult();
    }

    public function newInstance($model, $foreignId, $field, $content)
    {
        $temp = new FullTextSearchIndex();

        $temp->setModel($model);
        $temp->setField($field);
        $temp->setContent($content);

        $fieldName = $this->getForeignIdFieldName($model);
        $this->propertyAccessor->setValue($temp, $fieldName, $foreignId);

        $temp->setCreated(new DateTime());
        $temp->setUpdated(new DateTime());

        return $temp;
    }

    /**
     * @param $query
     * @param string|null $entity
     * @param string|null $field
     *
     * @return array
     */
    public function search($query, $entity = null, $field = null)
    {
        $fieldName = $this->getForeignIdFieldName($entity);

        $qb = $this->createQueryBuilder('i')
            ->select("i.{$fieldName}")
            ->addSelect('MATCH_AGAINST(i.content, :query) AS _matchQuote')
            ->where('MATCH_AGAINST(i.content, :query) > :minScore')
            ->orWhere('i.content LIKE :queryWildcard')
            ->groupBy("i.{$fieldName}")
            ->addGroupBy('_matchQuote')
            ->addOrderBy('_matchQuote', 'DESC')
            ->setParameter('query', $query)
            ->setParameter('queryWildcard', '%'.$query.'%')
            ->setParameter('minScore', round(strlen($query) * 0.8));

        if ($entity) {
            $qb->andWhere('i.model = :entity')->setParameter('entity', $entity);
        }

        if ($field) {
            $qb->andWhere('i.field = :fieldName')->setParameter('fieldName', $field);
        }

        $result = $qb->getQuery()->getScalarResult();

        $ids = [];
        foreach ($result as $row) {
            $ids[] = $row[$fieldName];
        }

        return $ids;
    }

    protected function getForeignIdFieldName($entity)
    {
        $metadata = $this->getEntityManager()->getMetadataFactory()->getMetadataFor($entity);
        $foreignIdFieldName = null;

        if (1 === count($metadata->getIdentifierFieldNames())) {
            $type = $metadata->getFieldMapping($metadata->getIdentifierFieldNames()[0])['type'];
            $foreignIdFieldName = 'foreignId';
            switch ($type) {
              case 'integer': $foreignIdFieldName .= 'Int'; break;
              case 'uuid': $foreignIdFieldName .= 'Guid'; break;
              case 'uuid_binary':
              case 'uuid_binary_ordered_time':
                $foreignIdFieldName .= 'Binary';
                break;
            default:
              throw new Exception('Unsupported id type '.$type.' of model '.$model);
          }
        }

        return $foreignIdFieldName;
    }
}
