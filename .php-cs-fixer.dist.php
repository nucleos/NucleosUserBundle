<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$header = <<<EOF
(c) Christian Gripp <mail@core23.de>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in([ __DIR__.'/src',  __DIR__.'/tests'])
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PHP70Migration' => true,
        '@PHPUnit60Migration:risky' => true,
        'header_comment' => [
            'header' => $header,
        ],
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces' => [
            'default' => 'align',
        ],
        'method_chaining_indentation' => false,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
        ],
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'remove_inheritdoc' => true,
        ],
        'static_lambda' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
    ])
    ->setFinder($finder);

