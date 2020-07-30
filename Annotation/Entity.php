<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Annotation;

use Doctrine\Common\Annotations\Enum;
use Doctrine\Common\Annotations\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class Entity
{
    /**
     * @Enum({"SINGLE", "PERENTITY"})
     *
     * @var string
     */
    public $mode = 'SINGLE';

    /**
     * @var array<\HoPeter1018\DoctrineFullTextSearchBundle\Annotation\Column>
     */
    public $columns = [];

    /**
     * @var array<\HoPeter1018\DoctrineFullTextSearchBundle\Annotation\ColumnGroup>
     */
    public $columnGroups = [];
}
