<?php
/**
 * @package    ContentFieldsFilter
 *
 * @copyright  (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * This is the configuration file for php-cs-fixer
 *
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer
 * @link https://mlocati.github.io/php-cs-fixer-configurator/#version:3.0
 *
 *
 * If you would like to run the automated clean up, then open a command line and type one of the commands below
 *
 * To run a quick dry run to see the files that would be modified:
 *
 *        php-cs-fixer fix --dry-run
 *
 * To run a full check, with automated fixing of each problem:
 *
 *        php-cs-fixer fix
 *
 * You can run the clean up on a single file if you need to, this is faster
 *
 *        php-cs-fixer fix --dry-run path/to/file.php
 *        php-cs-fixer fix path/to/file.php
 */

// Add all the extension folders
$finder = PhpCsFixer\Finder::create()
    ->in(
        [
            __DIR__ . '/com_jlcontentfieldsfilter',
            __DIR__ . '/mod_jlcontentfieldsfilter',
            __DIR__ . '/plg_system_jlcontentfieldsfilter',
            __DIR__ . '/file_jlcomponent_ajax',
        ]
    )
    // Ignore template files as PHP CS fixer can't handle them properly
    // https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/3702#issuecomment-396717120
    ->notPath('/tmpl/')
    ->notPath('/layouts/')
    // Ignore vendor libraries
    ->notPath('/media/js/vue.js')
    ->notPath('/media/js/nouislider.js')
    ->notPath('/media/js/nouislider.min.js');

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setHideProgress(false)
    ->setUsingCache(false)
    ->setRules(
        [
            // Basic ruleset is PSR 12
            '@PSR12'                                           => true,
            // Short array syntax
            'array_syntax'                                     => ['syntax' => 'short'],
            // Ensure there is no code on the same line as the PHP open tag
            'blank_line_after_opening_tag'                     => true,
            // Remove leading slashes from use clauses
            'no_leading_import_slash'                          => true,
            // Namespace must be on a line separate from opening tag
            'blank_line_after_namespace'                       => true,
            // Standardize the usage of isset() and empty()
            'modernize_strpos'                                 => true,
            // The PHP constants true, false, and null MUST be written using the correct casing
            'constant_case'                                    => ['case' => 'lower'],
            // PHPDoc should contain @param for all params
            'phpdoc_add_missing_param_annotation'              => true,
            // All items of the given phpdoc tags must be aligned vertically
            'phpdoc_align'                                     => ['align' => 'left'],
            // Annotations in PHPDoc should be ordered
            'phpdoc_order'                                     => true,
            // @return void should be omitted from PHPDoc
            'phpdoc_no_empty_return'                           => false,
            // Scalar types should always be written in the same form
            'phpdoc_scalar'                                    => true,
            // PHPDoc summary should end in either a full stop, exclamation mark, or question mark
            'phpdoc_summary'                                   => true,
            // Removes extra blank lines and/or blank lines following configuration
            'no_extra_blank_lines'                             => true,
            // List of values separated by a comma is contained on a single line should not have a trailing comma like [$foo, $bar,] = ...
            'no_trailing_comma_in_singleline'                  => true,
            // Arrays on multiline should have a trailing comma
            'trailing_comma_in_multiline'                      => ['elements' => ['arrays']],
            // Align elements in multiline array and variable declarations on new lines below each other
            'binary_operator_spaces'                           => ['operators' => ['=>' => 'align_single_space_minimal', '=' => 'align']],
            // The "No break" comment in switch statements
            'no_break_comment'                                 => ['comment_text' => 'No break'],
            // Remove unused imports
            'no_unused_imports'                                => true,
            // Classes from the global namespace should not be imported
            'global_namespace_import'                          => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
            // Alpha order imports
            'ordered_imports'                                  => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
            // There should not be useless else cases
            'no_useless_else'                                  => true,
            // Native function invocation
            'native_function_invocation'                       => ['include' => ['@compiler_optimized']],
            // Adds null to type declarations when parameter have a default null value
            'nullable_type_declaration_for_default_null_value' => true,
            // Removes unneeded parentheses around control statements
            'no_unneeded_control_parentheses'                  => true,
            // Using isset($var) && multiple times should be done in one call
            'combine_consecutive_issets'                       => true,
            // Calling unset on multiple items should be done in one call
            'combine_consecutive_unsets'                       => true,
            // There must be no sprintf calls with only the first argument
            'no_useless_sprintf'                               => true,
        ]
    )
    ->setFinder($finder);

return $config;
