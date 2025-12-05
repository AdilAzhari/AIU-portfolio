<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

try {
    return RectorConfig::configure()
        ->withPaths([
            __DIR__ . '/app',
            __DIR__ . '/bootstrap',
            __DIR__ . '/config',
            __DIR__ . '/public',
            __DIR__ . '/resources',
            __DIR__ . '/routes',
            __DIR__ . '/tests',
        ])
        // uncomment to reach your current PHP version
        ->withPhpSets()
        ->withAttributesSets()
        ->withDeadCodeLevel(1)
        ->withPreparedSets(typeDeclarations: true)
        ->withCodeQualityLevel(1)
        ->withConfiguredRule(DisallowedEmptyRuleFixerRector::class, [
            'treat_as_non_empty' => false,
        ])
        ->withRules([
            DeclareStrictTypesRector::class,
        ]);
} catch (InvalidConfigurationException $e) {

}
