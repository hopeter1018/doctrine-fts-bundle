<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Formatter;

class DateTimeFormatter implements FormatterInterface
{
    public function format($value): string
    {
        return $value->format('Y-m-d H:i:s');
    }
}
