<?php

namespace Elegant\Sanitizer\Laravel\Lighthouse;

use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;

/**
 * @see \Nuwave\Lighthouse\Validation\Validator
 */
abstract class Sanitizer
{
    /**
     * The slice of incoming arguments to validate.
     *
     * @var ArgumentSet
     */
    protected $args;

    /**
     * Return the sanitizer filters.
     *
     * @return array<string, array<mixed>>
     */
    abstract public function filters(): array;

    /**
     * Set the slice of args to validate.
     */
    public function setArgs(ArgumentSet $args): void
    {
        $this->args = $args;
    }

    /**
     * Retrieve the value of an argument.
     *
     * @param  string  $key  The key of the argument, may use dot notation to get nested values.
     * @param  mixed|null   $default  Returned in case the argument is not present.
     * @return mixed  The value of the argument or the default.
     */
    protected function arg(string $key, $default = null)
    {
        return Arr::get(
            $this->args->toArray(),
            $key,
            $default
        );
    }
}
