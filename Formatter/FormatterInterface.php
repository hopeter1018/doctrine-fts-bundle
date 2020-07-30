<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Formatter;

interface FormatterInterface
{
    public function format($value): string;
}
