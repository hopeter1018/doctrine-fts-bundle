<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Annotation;

use Doctrine\Common\Annotations\Target;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
final class ColumnGroup
{
    /**
     * @var array<\HoPeter1018\DoctrineFullTextSearchBundle\Annotation\Column>
     */
    public $columns = [];

    /**
     * @var string
     */
    public $seperator = ' ';
}
