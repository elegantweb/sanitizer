<?php

namespace Elegant\Sanitizer\Contracts\Laravel\Lighthouse;

/**
 * Provide filters for field sanitization.
 */
interface ProvidesFilters
{
    /**
     * Return sanitizer filters for the arguments.
     *
     * @return array<string, array<mixed>>
     */
    public function filters(): array;
}
