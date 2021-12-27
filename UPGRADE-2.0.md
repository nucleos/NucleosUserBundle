UPGRADE FROM 1.x to 2.0
=======================

## Removed XML configurations

All configuration files were migrated to PHP to improve overall performance.

The `@NucleosUserBundle/Resources/config/routing/all.xml` routing configuration was removed.

The `LoggedinAction` action and `nucleos_user_security_loggedin` route no longer exist.
You have to define a default loggedin route.

## Use symfony components where possible

The following classes were removed in favor of symfony components:

- `Nucleos\UserBundle\Util\PasswordUpdaterInterface`

## Deprecations

All the deprecated code introduced on 1.x is removed on 2.0.

Please read [1.x](https://github.com/nucleos/NucleosUserBundle/tree/1.x) upgrade guides for more information.

See also the [diff code](https://github.com/nucleos/NucleosUserBundle/compare/1.x...2.0.0).
