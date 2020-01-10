Advanced routing configuration
==============================

By default, the routing file ``@NucleosUserBundle/src/Resources/config/routing/all.xml`` imports
all the routing files and enables all the routes.
In the case you want to enable or disable the different available routes, use the
single routing configuration files.

.. code-block:: yaml

    # config/routes/nucleos_user.yaml
    nucleos_user_security:
        resource: "@NucleosUserBundle/src/Resources/config/routing/security.xml"

    nucleos_user_resetting:
        resource: "@NucleosUserBundle/src/Resources/config/routing/resetting.xml"
        prefix: /resetting

    nucleos_user_change_password:
        resource: "@NucleosUserBundle/src/Resources/config/routing/change_password.xml"
        prefix: /security

