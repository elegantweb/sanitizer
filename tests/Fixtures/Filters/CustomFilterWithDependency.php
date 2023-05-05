<?php

namespace Elegant\Sanitizer\Tests\Fixtures\Filters;

use Elegant\Sanitizer\Contracts\Filter;

class CustomFilterWithDependency implements Filter
{
    public function __construct(private Dependency $dependency)
    {
    }

    public function apply($value, array $options = [])
    {
        $this->dependency->call();

        return $value;
    }
}
