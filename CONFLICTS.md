# Conflicts

This file documents conflicts defined in `composer.json`.

## `slevomat/coding-standard: >=8.23`

Incompatible with PHP_CodeSniffer 4.x due to a changed method signature in PHPCS 4, causing fatal errors:

```
Fatal error: Declaration of ... must be compatible with ...
```

## `slevomat/coding-standard: >=8.16` on PHP 8.0

Slevomat 8.16+ references the `T_TYPE_OPEN_PARENTHESIS` tokenizer constant, which exists only in PHP 8.4+. On PHP 8.0–8.3, ECS can fatally error while checking files with `@param` docblocks. This package requires `slevomat/coding-standard` `^8.0,<8.16` for PHP `^8.0` compatibility.

## ECS 13 on PHP 8.0

Easy Coding Standard 13 bundles PHP_CodeSniffer 4.x tooling that references tokenizer constants (for example `T_ARRAY_HINT`) not defined on PHP 8.0. Running ECS 13 against `tests/Annotations.php` on PHP 8.0 can therefore raise a system error even when the Sylius preset itself is valid. Use PHP 8.4+ with ECS 13, or stay on ECS 10–12 for PHP 8.0 CI.
