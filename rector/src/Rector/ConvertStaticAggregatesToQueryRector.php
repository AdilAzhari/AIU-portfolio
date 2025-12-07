<?php


declare(strict_types=1);

namespace Rector\LaravelRules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ConvertStaticAggregatesToQueryRector extends AbstractRector
{
    private const array AGGREGATE_METHODS = [
        'count',
        'sum',
        'avg',
        'min',
        'max',
    ];

    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Rewrite static Eloquent aggregate calls to query()->aggregate() style',
            [
                new CodeSample(
                    <<<'CODE'
Credential::count();
CODE,
                    <<<'CODE'
Credential::query()->count();
CODE
                )
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [
            StaticCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof StaticCall) {
            return null;
        }

        if (!$node->name instanceof Identifier) {
            return null;
        }

        $methodName = $node->name->toString();

        // Only apply to aggregate methods
        if (!in_array($methodName, self::AGGREGATE_METHODS, true)) {
            return null;
        }

        // Ensure it's an Eloquent model
        $staticType = $this->nodeTypeResolver->getType($node->class);

        if (!$staticType instanceof ObjectType) {
            return null;
        }

        // Rewrite: Model::count() → Model::query()->count()
        return new MethodCall(
            new MethodCall(
                new Node\Expr\StaticCall($node->class, 'query'),
                'count'
            ),
            $methodName,
            $node->args
        );
    }
}
