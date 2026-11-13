# Contributing

The unit tests and PHP_CodeSniffer do not boot Magento.

```bash
phpunit -c phpunit.xml.dist
phpcs
```

PHP_CodeSniffer uses PSR-12 with a 120 character line limit. Magento's underscored resource hooks (`_construct`, `_beforeSave`, `_afterSave`, `_afterDelete`) are excluded.

Keep store-specific hosts, report URIs and report-only mode out of this repository. Those belong in the project that uses the module. `Magento_Csp` already has configuration for the mode.

The fetch directive list in `Model/Directive.php` matches `Magento\Csp\Model\Policy\FetchPolicy::POLICIES` for Magento 2.4. Change it only when that constant changes, and update `DirectiveTest`.
