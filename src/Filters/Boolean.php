<?php

namespace Elegant\Sanitizer\Filters;

use Elegant\Sanitizer\Contracts\Filter;

class Boolean implements Filter
{

    /**
     * If the given value is not a boolean set it to null.
     *
     * @param mixed $value
     * @param array $options
     * @return mixed
     */
    public function apply($value, array $options = [])
    {
        if (is_bool($value)) {
            return $value;
        } else if (is_string($value) === true && in_array($value, ['0', '1'])) {
            return (bool) $value;
        } else if (is_int($value) === true && in_array($value, [0, 1])) {
            return (bool) $value;
        }

        return null;
    }
}
