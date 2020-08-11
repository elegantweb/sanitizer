<?php

namespace Elegant\Sanitizer\Laravel;

use Closure;
use InvalidArgumentException;

use Elegant\Sanitizer\Sanitizer;

class Factory
{
    /**
     * List of custom filters.
     * 
     * @var array
     */
    protected $extensions = [];

    /**
     * Create a new Sanitizer instance.
     *
     * @param array $data Data to be sanitized
     * @param array $rules Filters to be applied to the given data
     * @return Sanitizer
     */
    public function make(array $data, array $rules)
    {
        $sanitizer = new Sanitizer($data, $rules, $this->extensions);
        $sanitizer->addExtensions($this->extensions);
        return $sanitizer;
    }

    /**
     * Add a custom filters to all Sanitizers created with this Factory.
     *
     * @param string $filter
     * @param mixed $extension Either the full class name of a Filter class implementing the Filter contract, or a Closure.
     * @return void
     */
    public function extend($filter, $extension)
    {
        $this->extensions[$filter] = $extension;
    }
}
