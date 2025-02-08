NucleosUserBundle
=================

[![Latest Stable Version](https://poser.pugx.org/nucleos/user-bundle/v/stable)](https://packagist.org/packages/nucleos/user-bundle)
[![Latest Unstable Version](https://poser.pugx.org/nucleos/user-bundle/v/unstable)](https://packagist.org/packages/nucleos/user-bundle)
[![License](https://poser.pugx.org/nucleos/user-bundle/license)](LICENSE.md)

[![Total Downloads](https://poser.pugx.org/nucleos/user-bundle/downloads)](https://packagist.org/packages/nucleos/user-bundle)
[![Monthly Downloads](https://poser.pugx.org/nucleos/user-bundle/d/monthly)](https://packagist.org/packages/nucleos/user-bundle)
[![Daily Downloads](https://poser.pugx.org/nucleos/user-bundle/d/daily)](https://packagist.org/packages/nucleos/user-bundle)

[![Continuous Integration](https://github.com/nucleos/NucleosUserBundle/actions/workflows/continuous-integration.yml/badge.svg?event=push)](https://github.com/nucleos/NucleosUserBundle/actions?query=workflow%3A"Continuous+Integration"+event%3Apush)
[![Code Coverage](https://codecov.io/gh/nucleos/NucleosUserBundle/graph/badge.svg)](https://codecov.io/gh/nucleos/NucleosUserBundle)

The NucleosUserBundle is a fork of [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/) which adds a lightweight support for a database-backed user system in symfony.

There are some major changes and refactorings if you want to migrate from FOS:

- It does not provide any advanced features like profile management or registration
- Swift mailer was dropped in favor of symfony mailer
- Couch DB support was removed
- Only symfony 6.4 / 7.* support
- There are only two *optional* dependencies: **doctrine/orm** and **doctrine/mongodb-odm**

Features included:

- Users can be stored via Doctrine ORM or MongoDB ODM
- Password reset support

Documentation
-------------

The source of the documentation is stored in the `docs/` folder
in this bundle.

[Read the Documentation](https://docs.nucleos.rocks/projects/user-bundle/)

Installation
------------

All the installation instructions are located in the documentation.
