NucleosUserBundle
=================

[![Latest Stable Version](https://poser.pugx.org/nucleos/user-bundle/v/stable)](https://packagist.org/packages/nucleos/user-bundle)
[![Latest Unstable Version](https://poser.pugx.org/nucleos/user-bundle/v/unstable)](https://packagist.org/packages/nucleos/user-bundle)
[![License](https://poser.pugx.org/nucleos/user-bundle/license)](LICENSE.md)

[![Total Downloads](https://poser.pugx.org/nucleos/user-bundle/downloads)](https://packagist.org/packages/nucleos/user-bundle)
[![Monthly Downloads](https://poser.pugx.org/nucleos/user-bundle/d/monthly)](https://packagist.org/packages/nucleos/user-bundle)
[![Daily Downloads](https://poser.pugx.org/nucleos/user-bundle/d/daily)](https://packagist.org/packages/nucleos/user-bundle)

[![Continuous Integration](https://github.com/nucleos/NucleosUserBundle/workflows/Continuous%20Integration/badge.svg)](https://github.com/nucleos/NucleosUserBundle/actions)
[![Code Coverage](https://codecov.io/gh/nucleos/NucleosUserBundle/branch/master/graph/badge.svg)](https://codecov.io/gh/nucleos/NucleosUserBundle)

The NucleosUserBundle is a fork of [FOSUSerBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/) which adds a lightweight support for a database-backed user system in symfony.

There are some major changes and refactorings if you want to migrate from FOS:

- It does not provide any advanced features like profile management or registration
- Swift mailer was dropped in favor of symfony mailer
- Couch DB support was removed
- Only symfony 4.4 / 5.x support
- There are only two *optional* dependencies: **doctrine/orm** and **doctrine/mongodb-orm**

Features include:

- Users can be stored via Doctrine ORM or MongoDB ODM
- Password reset support
