<?php

namespace Elegant\Sanitizer\Laravel\Lighthouse;

use Closure;
use Illuminate\Support\Collection;
use Elegant\Sanitizer\Contracts\Laravel\Lighthouse\ProvidesFilters;
use Elegant\Sanitizer\Sanitizer;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Support\Utils;
use Nuwave\Lighthouse\Support\Traits\HasArgumentValue;
use Nuwave\Lighthouse\Support\Contracts\Directive;

/**
 * @see \Nuwave\Lighthouse\Validation\ValidateDirective
 * @see \Nuwave\Lighthouse\Validation\RulesGatherer
 */
class SanitizeDirective extends BaseDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Run sanitization on a field.
"""
directive @sanitize on FIELD_DEFINITION
GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        $resolver = $fieldValue->getResolver();

        $wrappedResolver = function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {
            return $resolver($root, $this->sanitize($args, $resolveInfo), $context, $resolveInfo);
        };

        return $next(
            $fieldValue->setResolver($wrappedResolver)
        );
    }

    protected function sanitize(array $args, ResolveInfo $resolveInfo): array
    {
        $argumentSet = $resolveInfo->argumentSet;

        $filters = $this->gatherFiltersFromProviders($argumentSet, $argumentSet->directives);

        $args = (new Sanitizer($args, $filters))->sanitize();

        // apply value to everywhere!
        foreach ($argumentSet->arguments as $name => $argument)
            $argument->value = $args[$name];

        return $args;
    }

    public function gatherFiltersFromProviders($value, Collection $directives): array
    {
        $filters = [];

        /** @var Directive $directive */
        foreach ($directives as $directive) {
            if ($directive instanceof ProvidesFilters) {
                $filters = array_merge($filters, $this->gatherFiltersFromProvider($value, $directive));
            }
        }

        return $filters;
    }

    protected function gatherFiltersFromProvider($value, Directive $directive): array
    {
        if (Utils::classUsesTrait($directive, HasArgumentValue::class))
            $directive->setArgumentValue($value);

        return $directive->filters();
    }
}
