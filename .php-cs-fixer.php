<?php

$rules = [
    '@PSR2' => true,
    'array_syntax' => ['syntax' => 'short'],
    'multiline_whitespace_before_semicolons' => false,
    'echo_tag_syntax' => true,
    'no_unused_imports' => true
];

$excludes = [
    'vendor',
    'node_modules',
    'resources'
];

$phpCsFixerConfig = new PhpCsFixer\Config();
return $phpCsFixerConfig
    ->setRules($rules)
    ->setFinder(
        PhpCsFixer\Finder::create()
                         ->exclude($excludes)
                         ->notName('README.md')
                         ->notName('*.xml')
                         ->notName('*.yml')
    );
