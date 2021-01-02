<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('resources')
    ->notPath('vendor')
    ->notPath('bootstrap')
    ->notPath('storage')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('_ide_helper.php')
    ->notName('*.blade.php');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2'             => true,
        'array_syntax'      => ['syntax' => 'short'],
        'ordered_imports'   => ['sortAlgorithm' => 'alpha'],
        'no_unused_imports' => true,
        '@PHP70Migration' => true,
        '@PHP71Migration:risky' => true,
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'native_function_invocation' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_order' => true,
        'semicolon_after_instruction' => true,
        '@PHP71Migration' => true,
        'dir_constant' => true,
        'heredoc_to_nowdoc' => true,
        'modernize_types_casting' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'ordered_class_elements' => true,
        'declare_strict_types' => true,
        'psr4' => true,
        'no_short_echo_tag' => true,
        'align_multiline_comment' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ['author', 'package']],
        'list_syntax' => ['syntax' => 'short'],
        'phpdoc_types_order' => ['null_adjustment'=> 'always_last'],
        'single_line_comment_style' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
