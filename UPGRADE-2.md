# UPGRADE from Sylius 1.14 to Sylius 2.0

## File Location Changes

-   Moved config/ dir. out of src/ and into plugin root dir.
-   Moved templates/ dir. out of src/ and into plugin root dir.
-   Moved translations/ dir. out of src/ and into plugin root dir.

## Config Changes

-   The main config file is now located at: `config/config.yml`

## Twig Hooks

Templates are now rendered using Twig hooks, which is the standard in Sylius 2:

-   **Admin**

    -   [hook.yaml](https://github.com/3BRS/sylius-payment-restrictions-plugin/blob/sylius_2_upgrade_AK/config/app/twig_hooks/admin/hooks.yaml) contains config for Twig hooks used in Admin
