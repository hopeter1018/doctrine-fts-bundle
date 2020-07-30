<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Services;

use HoPeter1018\DoctrineFullTextSearchBundle\Formatter\FormatterInterface;

/**
 * Class FormatterManager.
 */
class FormatterManager
{
    /**
     * @var FormatterInterface[]
     */
    protected $formatters = [];

    /**
     * @param $class
     *
     * @return FormatterInterface
     */
    public function getFormatter($class)
    {
        if (!isset($this->formatters[$class])) {
            $this->formatters[$class] = new $class();
        }

        return $this->formatters[$class];
    }
}
