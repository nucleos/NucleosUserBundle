UPGRADE FROM 2.x to 3.0
=======================

## Change "Update Password" to "Update Security"

The action was refactored to have one common page to update all security information. This bundle only allow password changes, but it could be extended to allow more changes (e.g. e-mail).

- Class `ChangePasswordAction` is renamed to `UpdateSecurityAction` and corresponding templates too
- Class `ChangePasswordFormType` is renamed to `UpdateSecurityFormType` and uses the `UserInterface` as model class
- Constant `NucleosUserEvents::CHANGE_PASSWORD_COMPLETED` is renamed to `NucleosUserEvents::UPDATE_SECURITY_COMPLETED`
- Route `nucleos_user_change_password` is renamed to `nucleos_user_update_security`

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
