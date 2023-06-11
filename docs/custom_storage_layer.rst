Using a custom storage layer
============================

NucleosUserBundle has been designed to allow you to change the storage
layer used by your application and keep all of the functionality
provided by the bundle.

Implementing a new storage layer requires providing two classes: the user
implementation and the corresponding user manager (you will of course need
two other classes if you want to use the groups).

The user implementation must implement ``Nucleos\UserBundle\Model\UserInterface``
and the user manager must implement ``Nucleos\UserBundle\Model\UserManagerInterface``.
The ``Nucleos\UserBundle\Model`` namespace provides base classes to make it easier to
implement these interfaces.

.. note::

    You need to take care to always call ``updatePassword``
    before saving a user. This is done when calling ``updateUser`` so you will
    be safe if you always use the user manager to save the users.
    If your storage layer gives you a hook in its saving process, you can use
    it to make your implementation more flexible (this is done for Doctrine
    using listeners for instance)

Configuring NucleosUserBundle to use your implementation
--------------------------------------------------------

To use your own implementation, create a service for your user manager. The
following example will assume that its id is ``app.custom_user_manager``.

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        db_driver: custom  # custom means that none of the built-in implementation is used
        user_class: App\Model\CustomUser
        service:
            user_manager: app.custom_user_manager
        firewall_name: main

.. note::

    Your own service can be a private one. NucleosUserBundle will create an alias
    to make it available through ``nucleos_user.user_manager``.

.. caution::

    The validation of the uniqueness of the username and email fields is done
    using the constraints provided by DoctrineBundle. You will
    need to take care of this validation when using a custom storage layer,
    using a `custom constraint`_

.. _custom constraint: https://symfony.com/doc/current/cookbook/validation/custom_constraint.html
