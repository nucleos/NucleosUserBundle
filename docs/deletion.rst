Account deletion
================

The NucleosUserBundle has built-in support for deleting the user account.

Enable feature
--------------

The feature is disabled by default. You can enable it by using the following configuration:

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        # ...
        deletion:
            enabled: true

Add the routing config:

.. code-block:: yaml

    # config/routes/nucleos_user.yaml
    nucleos_user_deletion:
        resource: "@NucleosUserBundle/Resources/config/routing/deletion.xml"
