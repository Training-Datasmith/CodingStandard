# CodingStandard Architecture

## Purpose

A shared Easy Coding Standard (ECS) configuration package for Sylius-Labs and
related PHP projects, enforcing a consistent code style across multiple
repositories via a single versioned dependency.

## Directory Structure

```
ecs.php             — ECS configuration file; the single deliverable
tests/              — PHP files used as fixtures/samples during CI linting
  Annotations.php   — sample annotated class
  BehatContext.php  — Behat context stub
  FooBar.php        — generic class stub
  Generics.php      — generic types stub
  Sample.php        — PSR-compliant sample class
  SampleSpec.php    — PhpSpec stub (VisibilityRequiredFixer excluded here)
```

## Key Design Decisions

- **Single config file**: `ecs.php` is the entire public API; projects require
  this package and point ECS at `vendor/sylius-labs/coding-standard/ecs.php`.
- **PHP-CS-Fixer + Slevomat + ECS**: combines PHP-CS-Fixer rules,
  `slevomat/coding-standard` sniffs, and ECS configuration into one coherent
  rule set.
- **Opinionated defaults**: enforces `declare(strict_types=1)`, short array
  syntax, single-import-per-line, pre-increment style, ordered imports, no unused
  imports, and strict PHPDoc type ordering.
- **Spec file exclusion**: `VisibilityRequiredFixer` is skipped for `*Spec.php`
  files to accommodate PhpSpec's public method convention.
- **Forbidden annotations**: removes noisy tags (`@author`, `@package`,
  `@copyright`, etc.) that add no machine-readable value.

## Extension Points

- Import this config in a project's own `ecs.php` and call `$ecsConfig->paths()`
  to specify the directories to lint.
- Add project-specific `$ecsConfig->skip()` rules on top of the base config.

## Dependency Flow

```
Project ecs.php
  └── (require) sylius-labs/coding-standard/ecs.php
        ├── PHP-CS-Fixer fixers
        ├── slevomat/coding-standard sniffs
        └── symplify/easy-coding-standard runner
```
