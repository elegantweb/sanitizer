<?php

namespace Elegant\Sanitizer\Tests\Fixtures\Filters;

class Dependency
{
    private bool $called = false;

    public function call()
    {
        $this->called = true;
    }

    public function isCalled(): bool
    {
        return $this->called;
    }
}
