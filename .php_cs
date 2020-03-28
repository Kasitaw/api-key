<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sortAlgorithm' => 'length'],
        'no_unused_imports' => true,
        'blank_line_after_namespace' => true,
        'elseif' => true,
        'switch_case_space' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_null_coalescing' => true,
        'binary_operator_spaces' => ['align_double_arrow' => false],
        'linebreak_after_opening_tag' => true,
        'not_operator_with_successor_space' => false,
        'phpdoc_order' => true,
        'phpdoc_align' => ['align' => 'left'],
        'concat_space'=> ['spacing' => 'one'],
        'new_with_braces' => false,
        'phpdoc_no_empty_return' => false,
    ])
    ->setFinder($finder);