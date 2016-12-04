<?php

$finder = PhpCsFixer\Finder::create()->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_before_return' => false,
        'cast_spaces' => false,
        'concat_space' => false,
        'linebreak_after_opening_tag' => true,
        'modernize_types_casting' => true,
        'no_empty_comment' => false,
        'no_empty_phpdoc' => false,
        'no_empty_statement' => false,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_unneeded_control_parentheses' => false,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => false,
        'pre_increment' => false,
        'psr4' => true,
        'strict_param' => true,
        'trailing_comma_in_multiline_array' => false
    ))
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
