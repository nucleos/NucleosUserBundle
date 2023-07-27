UPGRADE FROM 2.x to 3.0
=======================

## Remove canonical feature

The fields `usernameCanonical` and `emailCanonical` are removed from the `User` class in favor of using lowercase
username and e-mail by default. You need to check your database on your own in order to have no duplicates after the
update.

Because of the feature removal, the following classes were removed:

- `Nucleos\UserBundle\Canonicalizer`
- `Nucleos\UserBundle\CanonicalFieldsUpdater`
- `Nucleos\UserBundle\SimpleCanonicalizer`
- `Nucleos\UserBundle\UtilCanonicalFieldsUpdater`

## Deprecations

All the deprecated code introduced on 2.x is removed on 3.0.
