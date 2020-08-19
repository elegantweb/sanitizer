<?php

namespace Elegant\Sanitizer\Laravel\Lighthouse;

use Elegant\Sanitizer\Contracts\Laravel\Lighthouse\ProvidesFilters;
use GraphQL\Language\Parser;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Traits\HasArgumentValue;
use Nuwave\Lighthouse\Support\Contracts\FieldManipulator;

/**
 * @see \Nuwave\Lighthouse\Validation\ValidatorDirective
 */
class SanitizerDirective extends BaseDirective implements ProvidesFilters, FieldManipulator
{
    use HasArgumentValue;

    protected Sanitizer $sanitizer;

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Provide validation rules through a PHP class.
"""
directive @sanitizer(
  """
  The name of the class to use.
  If defined on an input, this defaults to a class called `{$inputName}Sanitizer` in the
  default sanitizer namespace. For fields, it uses the name of the parent type
  and the field name: `{$parent}{$field}Sanitizer`.
  """
  class: String
) repeatable on INPUT_FIELD_DEFINITION | FIELD_DEFINITION | INPUT_OBJECT
GRAPHQL;
    }

    public function filters(): array
    {
        return $this->sanitizer()->filters();
    }

    protected function createSanitizer(): Sanitizer
    {
        // We precomputed and validated the full class name at schema build time
        $sanitizer = app($this->directiveArgValue('class'));

        $sanitizer->setArgs($this->argumentValue);

        return $sanitizer;
    }

    protected function sanitizer(): Sanitizer
    {
        return $this->sanitizer ??= $this->createSanitizer();
    }

    public function manipulateTypeDefinition(
        DocumentAST &$documentAST,
        TypeDefinitionNode &$typeDefinition
    ) {
        if (!$typeDefinition instanceof InputObjectTypeDefinitionNode) {
            throw new DefinitionException(
                "Can not use @sanitizer on non input type {$typeDefinition->name->value}."
            );
        }

        if ($this->directiveHasArgument('class')) {
            $classCandidate = $this->directiveArgValue('class');
        } else {
            $classCandidate = $typeDefinition->name->value.'Sanitizer';
        }

        $this->setFullClassnameOnDirective($typeDefinition, $classCandidate);
    }

    public function manipulateFieldDefinition(
        DocumentAST &$documentAST,
        FieldDefinitionNode &$fieldDefinition,
        ObjectTypeDefinitionNode &$parentType
    ) {
        if ($this->directiveHasArgument('class')) {
            $classCandidate = $this->directiveArgValue('class');
        } else {
            $classCandidate = $parentType->name->value.'\\'.ucfirst($fieldDefinition->name->value).'Sanitizer';
        }

        $this->setFullClassnameOnDirective($fieldDefinition, $classCandidate);
    }

    protected function setFullClassnameOnDirective(Node &$definition, string $classCandidate): void
    {
        $sanitizerClass = $this->namespaceSanitizerClass($classCandidate);

        foreach ($definition->directives as $directive) {
            if ($directive->name->value === $this->name()) {
                $directive->arguments = ASTHelper::mergeUniqueNodeList(
                    $directive->arguments,
                    [Parser::argument('class: "'.addslashes($sanitizerClass).'"')],
                    true
                );
            }
        }
    }

    protected function namespaceSanitizerClass(string $classCandidate): string
    {
        return $this->namespaceClassName(
            $classCandidate,
            (array) config('lighthouse.namespaces.sanitizers'),
            function (string $classCandidate): bool {
                return is_subclass_of($classCandidate, Sanitizer::class);
            }
        );
    }
}
