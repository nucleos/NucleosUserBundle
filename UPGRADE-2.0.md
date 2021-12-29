UPGRADE FROM 1.x to 2.0
=======================

## Removed XML configurations

All configuration files were migrated to PHP to improve overall performance.

The `@NucleosUserBundle/Resources/config/routing/all.xml` routing configuration was removed.

The `LoggedinAction` action and `nucleos_user_security_loggedin` route no longer exist.
You have to define a default `loggedin` route.

## Use symfony components where possible

The following classes were removed in favor of symfony components:

- `Nucleos\UserBundle\Util\PasswordUpdaterInterface`

## Removed user and group id

The `id` property was removed from the `User` class in favor of `UserInterface::getUserIdentifier`.

The `id` property was removed from the `Group` class.

## Removed `Interface` suffix

The `Interface` suffix was removed from all* interfaces. All default implementation use specific class prefix.

- `Nucleos\UserBundle\Mailer\MailerInterface` => `Nucleos\UserBundle\Mailer\ResettingMailer`
- `Nucleos\UserBundle\Mailer\NoopMailer` => `Nucleos\UserBundle\Mailer\NoopResettingMailer`
- `Nucleos\UserBundle\Mailer\Mailer` => `Nucleos\UserBundle\Mailer\SimpleResettingMailer`
- `Nucleos\UserBundle\Model\GroupableInterface` => `Nucleos\UserBundle\Model\GroupAwareUser`
- `Nucleos\UserBundle\Model\GroupManagerInterface` => `Nucleos\UserBundle\Model\GroupManager`
- `Nucleos\UserBundle\Model\GroupManager` => `Nucleos\UserBundle\Model\BaseGroupManager`
- `Nucleos\UserBundle\Model\LocaleAwareInterface` => `Nucleos\UserBundle\Model\LocaleAwareUser`
- `Nucleos\UserBundle\Model\UserManagerInterface` => `Nucleos\UserBundle\Model\UserManager`
- `Nucleos\UserBundle\Model\UserManager` => `Nucleos\UserBundle\Model\BaseUserManager`
- `Nucleos\UserBundle\Security\LoginManagerInterface` => `Nucleos\UserBundle\Security\LoginManager`
- `Nucleos\UserBundle\Security\LoginManager` => `Nucleos\UserBundle\Security\SimpleLoginManager`
- `Nucleos\UserBundle\Util\CanonicalizerInterface` => `Nucleos\UserBundle\Util\Canonicalizer`
- `Nucleos\UserBundle\Util\Canonicalizer` => `Nucleos\UserBundle\Util\SimpleCanonicalizer`
- `Nucleos\UserBundle\Util\UserManipulator` => `Nucleos\UserBundle\Util\SimpleUserManipulator`
- `Nucleos\UserBundle\Util\TokenGeneratorInterface` => `Nucleos\UserBundle\Util\TokenGenerator`
- `Nucleos\UserBundle\Util\TokenGenerator` => `Nucleos\UserBundle\Util\SimpleTokenGenerator`

Only two interface keep the suffix to stay consistent with the parent symfony interfaces:

- `Nucleos\UserBundle\Model\UserInterface`
- `Nucleos\UserBundle\Model\GroupInterface`

## Removed salt from `User`

The salt was removed from the `User` class.

If you want to use this bundle for an existing user basis, you should add the `LegacyPasswordAuthenticatedUserInterface` interface to your user Entity:

```php
namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nucleos\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

#[ORM\Entity]
class User extends BaseUser implements LegacyPasswordAuthenticatedUserInterface
{
    // ...

    #[ORM\Column(type: Types::STRING)]
    protected ?string $salt = null;

    public function getSalt(): ?string
    {
        return $this->salt;
    }

}
```

## Deprecations

All the deprecated code introduced on 1.14.x is removed on 2.0.

Please read [1.14.x](https://github.com/nucleos/NucleosUserBundle/tree/1.14.x) upgrade guides for more information.

See also the [diff code](https://github.com/nucleos/NucleosUserBundle/compare/1.14.x...2.0.0).
