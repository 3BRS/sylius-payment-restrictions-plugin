# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `symfony/framework-bundle:6.2.8`:

  This version is missing the service alias `validator.expression`
  which causes ValidatorException exception to be thrown when using `Expression` constraint.

- `api-platform/symfony`, `api-platform/core`, `api-platform/json-schema`, `api-platform/metadata` `<4.2.0`:

  Sylius 2.1+ requires API Platform 4.2+ due to `DefinitionNameFactory` constructor changes.

- `sylius/twig-hooks: >=0.9.0 <0.9.1`, `sylius/twig-extra: >=0.9.0 <0.9.1`:

  Version 0.9.0 causes HTTP 500 errors on admin pages. Fixed in 0.9.1.

- `knplabs/knp-menu-bundle: <3.5.0`:

  Older versions cause "no registered paths for namespace KnpMenu" errors.

- `symfony/var-exporter: >=8.0`, `symfony/error-handler: >=8.0`:

  On PHP 8.4, Composer may resolve unconstrained Symfony packages to v8.0 via
  transitive dependencies. Symfony 8.0 removed `ProxyHelper::generateLazyGhost()`
  which breaks Doctrine ORM.

- `sylius/resource-bundle: <1.13.1`:

  Version 1.13.0 has a bug causing "No locale has been set" errors.

- `doctrine/data-fixtures: <1.8.0`:

  Older versions are incompatible with Doctrine ORM 3.x (`ORMPurger::getJoinTableName()` type error).
