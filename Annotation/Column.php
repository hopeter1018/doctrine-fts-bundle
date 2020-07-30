<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Annotation;

use Doctrine\Common\Annotations\Target;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class Column
{
    /**
     * @var string
     */
    public $formatter = null;
}
