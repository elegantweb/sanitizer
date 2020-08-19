<?php

namespace Elegant\Sanitizer\Tests\Fixtures\Filters;

use Elegant\Sanitizer\Contracts\Filter;

class CustomFilter implements Filter
{
    public function apply($value, $options = [])
    {
        return $value . $value;
    }
}
