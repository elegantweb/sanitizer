<?php

namespace Elegant\Sanitizer;

use Closure;
use InvalidArgumentException;
use UnexpectedValueException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\ClosureValidationRule;
use Elegant\Sanitizer\Contracts\Filter;

class Sanitizer
{
    /**
     * Data to sanitize.
     * 
     * @var array
     */
    protected $data;

    /**
     * Filters to apply.
     * 
     * @var array
     */
    protected $rules;

    /**
     * Available filters as $name => $classPath.
     * 
     * @var array
     */
    protected $filters = [
        'capitalize' => \Elegant\Sanitizer\Filters\Capitalize::class,
        'cast' => \Elegant\Sanitizer\Filters\Cast::class,
        'escape' => \Elegant\Sanitizer\Filters\EscapeHTML::class,
        'format_date' => \Elegant\Sanitizer\Filters\FormatDate::class,
        'lowercase' => \Elegant\Sanitizer\Filters\Lowercase::class,
        'uppercase' => \Elegant\Sanitizer\Filters\Uppercase::class,
        'trim' => \Elegant\Sanitizer\Filters\Trim::class,
        'strip_tags' => \Elegant\Sanitizer\Filters\StripTags::class,
        'digit' => \Elegant\Sanitizer\Filters\Digit::class,
    ];

    /**
     * Create a new sanitizer instance.
     *
     * @param array $data
     * @param array $rules Rules to be applied to each data attribute
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $this->parseRules($rules);
    }

    /**
     * Register an array of custom filter extensions.
     *
     * @param array $extensions
     * @return void
     */
    public function addExtensions(array $extensions)
    {
        $this->filters = array_merge($this->filters, $extensions);
    }

    /**
     * Parse a rules array.
     *
     * @param array $rules
     * @return array
     */
    protected function parseRules(array $rules)
    {
        $parsedRules = [];

        $rawRules = (new ValidationRuleParser($this->data))->explode($rules);

        foreach ($rawRules->rules as $attribute => $attributeRules) {
            foreach ($attributeRules as $attributeRule) {
                $parsedRule = $this->parseRule($attributeRule);
                if ($parsedRule) {
                    $parsedRules[$attribute][] = $parsedRule;
                }
            }
        }

        return $parsedRules;
    }

    /**
     * Parse a rule.
     *
     * @param string|Closure $rule
     * @return array|Closure
     */
    protected function parseRule($rule)
    {
        if (is_string($rule)) {
            return $this->parseRuleString($rule);
        } elseif ($rule instanceof ClosureValidationRule) {
            return $rule->callback;
        } else {
            throw new InvalidArgumentException("Unsupported rule type.");
        }
    }

    /**
     * Parse a rule string formatted as filterName:option1, option2 into an array formatted as [name => filterName, options => [option1, option2]]
     *
     * @param string $rule Formatted as 'filterName:option1, option2' or just 'filterName'
     * @return array Formatted as [name => filterName, options => [option1, option2]]. Empty array if no filter name was found.
     */
    protected function parseRuleString($rule)
    {
        if (strpos($rule, ':') !== false) {
            list($name, $options) = explode(':', $rule, 2);
            $options = array_map('trim', explode(',', $options));
        } else {
            $name = $rule;
            $options = [];
        }

        if (!$name) {
            return [];
        }

        return compact('name', 'options');
    }

    /**
     * Apply the given filter by its name
     *
     * @param string|Closure $rule
     * @return Filter
     */
    protected function applyFilter($rule, $value)
    {
        if ($rule instanceof Closure) {
            return call_user_func($rule, $value);
        }

        $name = $rule['name'];
        $options = $rule['options'];

        // If the filter does not exist, throw an Exception:
        if (!isset($this->filters[$name])) {
            throw new InvalidArgumentException("Filter [$name] not found.");
        }

        $filter = $this->filters[$name];

        if ($filter instanceof Closure) {
            return call_user_func_array($filter, [$value, $options]);
        } elseif (in_array(Filter::class, class_implements($filter))) {
            return (new $filter)->apply($value, $options);
        } else {
            throw new UnexpectedValueException("Invalid filter [$name] must be a Closure or a class implementing the Elegant\Sanitizer\Contracts\Filter interface.");
        }
    }

    /**
     * Sanitize the given data.
     *
     * @return array
     */
    public function sanitize()
    {
        $sanitized = $this->data;

        foreach ($this->rules as $attr => $rules) {
            if (Arr::has($sanitized, $attr)) {
                foreach ($rules as $rule) {
                    Arr::set($sanitized, $attr, $this->applyFilter($rule, Arr::get($sanitized, $attr)));
                }
            }
        }

        return $sanitized;
    }
}
