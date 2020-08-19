<?php

namespace Elegant\Sanitizer\Tests;

use Elegant\Sanitizer\Sanitizer;

trait SanitizesData
{
    /**
     * Sanitizes the data.
     *
     * @param array $data
     * @param array $data
     * @return array
     */
    public function sanitize(array $data, array $rules)
    {
        $sanitizer = new Sanitizer($data, $rules);

        return $sanitizer->sanitize();
    }
}
