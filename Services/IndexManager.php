<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Services;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use HoPeter1018\DoctrineFullTextSearchBundle\Annotation\Column;
use HoPeter1018\DoctrineFullTextSearchBundle\Annotation\ColumnGroup;
use HoPeter1018\DoctrineFullTextSearchBundle\Annotation\Entity;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class IndexManager.
 */
class IndexManager
{
    const ANNOTATION_ENTITY = Entity::class;
    const ANNOTATION_PROPERTY = Column::class;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var FormatterManager */
    protected $formatterManager;

    /** @var Reader */
    protected $annotationReader;

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var DynamicColumnDataRepository */
    protected $dynamicColumnDataRepository;

    public function __construct(ManagerRegistry $managerRegistry, FormatterManager $formatterManager, Reader $annotationReader, PropertyAccessorInterface $propertyAccessor)
    {
        $this->managerRegistry = $managerRegistry;
        $this->formatterManager = $formatterManager;
        $this->annotationReader = $annotationReader;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function index($entity)
    {
        $anno = $this->parseAnno(get_class($entity));
        $result = [
          'group' => [],
          'prop' => [],
          'method' => [],
        ];
        $cb = $this;

        /** @var ColumnGroup $columnGroup */
        foreach ($anno['class']->columnGroups as $alias => $columnGroup) {
            $result['group'][$alias] = implode($columnGroup->seperator, array_map(function (Column $column, $key) use ($cb, $entity) {
                return $cb->format($entity, $key, $column);
            }, $columnGroup->columns, array_keys($columnGroup->columns)));
        }
        foreach ($anno['class']->columns as $fieldName => $column) {
            $result['prop'][$fieldName] = $this->format($entity, $fieldName, $column);
        }
        foreach ($anno['properties'] as $propertyName => $column) {
            $result['prop'][$propertyName] = $this->format($entity, $propertyName, $column);
        }
        foreach ($anno['methods'] as $methodName => $column) {
            $result['method'][$methodName] = $this->format($entity, $methodName, $column);
        }

        return $result;
    }

    /**
     * @param string $parent
     * @param string $class
     *
     * @return bool
     */
    public function parseAnno($class)
    {
        $indexes = [
            '_' => false,
            'class' => [],
            'properties' => [],
            'methods' => [],
        ];
        $reflectionClass = new ReflectionClass($class);
        $classAnno = $this->annotationReader->getClassAnnotation($reflectionClass, self::ANNOTATION_ENTITY);

        if ($classAnno) {
            $indexes['_'] = true;
            $indexes['class'] = $classAnno;
            foreach ($reflectionClass->getProperties()as $prop) {
                if ($this->annotationReader->getPropertyAnnotation($prop, self::ANNOTATION_PROPERTY)) {
                    $indexes['properties'][$prop->name] = $this->annotationReader->getPropertyAnnotation($prop, self::ANNOTATION_PROPERTY);
                }
            }
            foreach ($reflectionClass->getMethods() as $method) {
                if ($this->annotationReader->getMethodAnnotation($method, self::ANNOTATION_PROPERTY)) {
                    $indexes['methods'][$method->name] = $this->annotationReader->getMethodAnnotation($method, self::ANNOTATION_PROPERTY);
                }
            }
        }

        return $indexes;
    }

    protected function format($entity, $key, Column $column)
    {
        $value = $this->propertyAccessor->getValue($entity, $key);

        if (null !== $column->formatter) {
            $value = $this->formatterManager->getFormatter($column->formatter)->format($value);
        }

        return $value;
    }
}
