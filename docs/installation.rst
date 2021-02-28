Installation
============

Prerequisites
-------------

Translations
~~~~~~~~~~~~

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

.. code-block:: yaml

    # config/packages/framework.yaml
    framework:
        translator: ~

For more information about translations, check `Symfony documentation`_.

Installation
------------

1. Download NucleosUserBundle using composer
2. Enable the Bundle
3. Create your User class
4. Configure your application's security.yaml
5. Configure the NucleosUserBundle
6. Import NucleosUserBundle routing
7. Update your database schema

Step 1: Download NucleosUserBundle using composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Require the bundle with composer:

.. code-block:: bash

    $ composer require nucleos/user-bundle

If you encounter installation errors pointing at a lack of configuration parameters, such as ``The child node "db_driver" at path "nucleos_user" must be configured``, you should complete the configuration in Step 5 first and then re-run this step.

Step 2: Enable the bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

Enable the bundle in the kernel:

.. code-block:: php-annotations

    // config/bundles.php
    return [
        // ...
        Nucleos\UserBundle\NucleosUserBundle::class => ['all' => true],
        // ...
    ]

Step 3: Create your User class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The goal of this bundle is to persist some ``User`` class to a database (MySql,
MongoDB, etc). Your first job, then, is to create the ``User`` class
for your application. This class can look and act however you want: add any
properties or methods you find useful. This is *your* ``User`` class.

The bundle provides base classes which are already mapped for most fields
to make it easier to create your entity. Here is how you use it:

1. Extend the base ``User`` class (from the ``Model`` folder if you are using
   any of the doctrine variants)
2. Map the ``id`` field. It must be protected as it is inherited from the parent class.

.. caution::

    When you extend from the mapped superclass provided by the bundle, don't
    redefine the mapping for the other fields as it is provided by the bundle.

In the following sections, you'll see examples of how your ``User`` class should
look, depending on how you're storing your users (Doctrine ORM or MongoDB ODM).

.. note::

    The doc uses a bundle named ``App`` according to the Symfony best
    practices. However, you can of course place your user class in the bundle
    you want.

.. caution::

    If you override the __construct() method in your User class, be sure
    to call parent::__construct(), as the base User class depends on
    this to initialize some fields.

a) Doctrine ORM User class
..........................

If you're persisting your users via the Doctrine ORM, then your ``User`` class
should live in the ``Entity`` namespace of your bundle and look like this to
start:


.. code-block:: php-annotations

    // src/Entity/User.php
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Nucleos\UserBundle\Model\User as BaseUser;

    /**
     * @ORM\Entity
     * @ORM\Table(name="nucleos_user__user")
     */
    class User extends BaseUser
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        public function __construct()
        {
            parent::__construct();
            // your own logic
        }
    }

.. caution::

    ``user`` is a reserved keyword in the SQL standard. If you need to use reserved words, surround them with backticks, *e.g.* ``@ORM\Table(name="`user`")``

b) MongoDB User class
.....................

If you're persisting your users via the Doctrine MongoDB ODM, then your ``User``
class should live in the ``Document`` namespace of your bundle and look like
this to start.

.. code-block:: php-annotations

    // src/Document/User.php
    namespace App\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Nucleos\UserBundle\Model\User as BaseUser;

    /**
     * @MongoDB\Document
     */
    class User extends BaseUser
    {
        /**
         * @MongoDB\Id(strategy="auto")
         */
        protected $id;

        public function __construct()
        {
            parent::__construct();
            // your own logic
        }
    }


Step 4: Configure your application's security.yaml
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order for Symfony's security component to use the NucleosUserBundle, you must
tell it to do so in the ``security.yaml`` file. The ``security.yaml`` file is where the
basic security configuration for your application is contained.

Below is a minimal example of the configuration necessary to use the NucleosUserBundle
in your application:

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        encoders:
            Nucleos\UserBundle\Model\UserInterface: auto

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: ROLE_ADMIN

        providers:
            nucleos_userbundle:
                id: nucleos_user.user_provider.username

        firewalls:
            main:
                pattern: ^/
                user_checker: Nucleos\UserBundle\Security\UserChecker
                form_login:
                    provider: nucleos_userbundle
                    csrf_token_generator: security.csrf.token_manager

                logout:       true
                anonymous:    true

        access_control:
            - { path: ^/change-password, role: IS_AUTHENTICATED_REMEMBERED }
            # If you have an admin backend, uncomment the following line
            # - { path: ^/admin/, role: ROLE_ADMIN }

Under the ``providers`` section, you are making the bundle's packaged user provider
service available via the alias ``nucleos_userbundle``. The id of the bundle's user
provider service is ``nucleos_user.user_provider.username``.

Next, take a look at and examine the ``firewalls`` section. Here we have
declared a firewall named ``main``. By specifying ``form_login``, you have
told the Symfony Framework that any time a request is made to this firewall
that leads to the user needing to authenticate himself, the user will be
redirected to a form where he will be able to enter his credentials. It should
come as no surprise then that you have specified the user provider service
we declared earlier as the provider for the firewall to use as part of the
authentication process.

.. note::

    Although we have used the form login mechanism in this example, the NucleosUserBundle
    user provider service is compatible with many other authentication methods
    as well. Please read the Symfony Security component documentation for
    more information on the other types of authentication methods.

The ``access_control`` section is where you specify the credentials necessary for
users trying to access specific parts of your application. The bundle requires
that the login form and all the routes used to create a user and reset the password
be available to unauthenticated users but use the same firewall as
the pages you want to secure with the bundle. This is why you have specified that
any request matching the ``/login`` pattern or starting with
``/resetting`` have been made available to anonymous users. You have also specified
that any request beginning with ``/admin`` will require a user to have the
``ROLE_ADMIN`` role.

For more information on configuring the ``security.yaml`` file please read the Symfony
`security component documentation`_.

.. note::

    Pay close attention to the name, ``main``, that we have given to the
    firewall which the NucleosUserBundle is configured in. You will use this
    in the next step when you configure the NucleosUserBundle.

Step 5: Configure the NucleosUserBundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that you have properly configured your application's ``security.yaml`` to work
with the NucleosUserBundle, the next step is to configure the bundle to work with
the specific needs of your application.

Add the following configuration to your ``config/packages/nucleos_user.yaml`` file according to which type
of datastore you are using.

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        db_driver: orm # other valid values is 'mongodb'
        firewall_name: main
        user_class: App\Entity\User
        from_email:   "%mailer_user%"


Only four configuration's nodes are required to use the bundle:

* The type of datastore you are using (``orm`` or ``mongodb``).
* The firewall name which you configured in Step 4.
* The fully qualified class name (FQCN) of the ``User`` class which you created in Step 3.

.. note::

    NucleosUserBundle uses a compiler pass to register mappings for the base
    User and Group model classes with the object manager that you configured
    it to use. (Unless specified explicitly, this is the default manager
    of your doctrine configuration.)

Step 6: Import NucleosUserBundle routing files
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that you have activated and configured the bundle, all that is left to do is
import the NucleosUserBundle routing files.

By importing the routing files you will have ready made pages for things such as
logging in, creating users, etc.

.. code-block:: yaml

    # config/routes/nucleos_user.yaml
    nucleos_user:
        resource: "@NucleosUserBundle/Resources/config/routing/all.xml"

Step 7: Update your database schema
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that the bundle is configured, the last thing you need to do is update your
database schema because you have added a new entity, the ``User`` class which you
created in Step 4.

For ORM run the following command.

.. code-block:: bash

    $ php bin/console doctrine:schema:update --force

For MongoDB users you can run the following command to create the indexes.

.. code-block:: bash

    $ php bin/console doctrine:mongodb:schema:create --index

.. _Symfony documentation: https://symfony.com/doc/current/book/translation.html
.. _security component documentation: https://symfony.com/doc/current/book/security.html
