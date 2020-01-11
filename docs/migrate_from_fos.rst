Migrate from FOSUserBundle
==========================

Prerequisites
-------------

In order to migrate from `FOSUserBundle`_, you must use a `supported PHP version`_ and `supported symfony version`_.
There is no support for unmaintained PHP or symfony versions!

Start migration
---------------

The main difference between both bundles is the service and configuration prefix. FOS uses `fos_user` and our bundles
uses `nucleos_user` (respectively `nucleos_profile`)

Step 1: Update configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Install this bundle and the `NucleosProfileBundle`_:

.. code-block:: bash

    $ composer require nucleos/user-bundle nucleos/profile-bundle

Step 2: Update configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Inside the configurations under `config/packages/` you need to replace the prefix `fos_user` with `nucleos_user` for
the most keys. Some keys related to profile management or registration need a `nucleos_profile` prefix.

Step 3: Update routing
~~~~~~~~~~~~~~~~~~~~~~

The same rule applies for the routing files located under `config/routes`.
You need to replace the `@FOSUserBundle` import with `@NucleosProfileBundle`.

Step 4: Clean cache
~~~~~~~~~~~~~~~~~~~

Now the migration is finished. The last step if to clear the cache.

.. code-block:: bash

    $ php bin/console cache:clear

Optional: Use FOSUserBundle polyfill
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There is a polyfill for the most used FOS classes and interfaces.
This will use PHP aliases to map the old components to the new namespace.

Some interfaces/classes have a different signature and could cause problems.
It is safe to use if you or a third party library is not implementing one of the old FOS classes.

Require the bundle with composer:

.. code-block:: bash

    $ composer require nucleos/fos-user-bundle-polyfill

.. warning::

    Be aware this library uses the composer replaces function to fake the FOSUserBundle.
    This can cause problems, if you need specific bundle features.

Problems
--------

If you have problems feel free to open an issue or have a look at the docs as there are some more internal refactorings:

- Using symfony mailer instead of swiftmailer
- Add strict type hints
- Closing API. Most classes have beed marked as final
- E-Mail address and Usernames are non-nullable
- Forms do not use the user model and have specific form models

.. _NucleosProfileBundle: https://github.com/nucleos/NucleosProfileBundle/
.. _FOSUserBundle: https://github.com/FriendsOfSymfony/FOSUserBundle/
.. _supported PHP version: https://www.php.net/supported-versions.php
.. _supported symfony version: https://symfony.com/releases
