# Conflicts

This file documents conflicts defined in `composer.json`.

## `slevomat/coding-standard: >=8.23`

Incompatible with PHP_CodeSniffer 4.x due to a changed method signature in PHPCS 4, causing fatal errors:

```
Fatal error: Declaration of ... must be compatible with ...
```

## Slevomat vs PHP_CodeSniffer bundled in Easy Coding Standard

Easy Coding Standard ships its own PHP_CodeSniffer copy. Slevomat Coding Standard references PHPCS tokenizer constants that differ between PHPCS 3.x and 4.x:

- **Slevomat 8.16+** uses `T_TYPE_OPEN_PARENTHESIS` (`PHPCS_T_TYPE_OPEN_PARENTHESIS`), added in PHP_CodeSniffer 3.8+. ECS **10** and **11** bundle an older PHPCS without that constant, so Slevomat 8.16+ can fatally error with `Undefined constant "T_TYPE_OPEN_PARENTHESIS"`.
- **Slevomat before 8.16** uses `T_ARRAY_HINT` (`PHPCS_T_ARRAY_HINT`), which was deprecated in PHPCS 3.3.0 and **removed in PHPCS 4.0**. ECS **13** bundles PHPCS 4.x, so Slevomat &lt;8.16 can fatally error with `Undefined constant "T_ARRAY_HINT"`.

These are PHP_CodeSniffer token constants, not PHP runtime tokenizer constants.

CI pins Slevomat per ECS matrix leg (for example 8.15.x on ECS 10/11 and 8.16+ on ECS 12/13) so each leg uses a Slevomat release compatible with the bundled PHPCS. `composer.json` keeps `slevomat/coding-standard` at `^8.0`; consumers must resolve a compatible Slevomat version for their ECS major release.
