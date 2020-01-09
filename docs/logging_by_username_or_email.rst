Logging in by Username or Email
===============================

The bundle provides a built-in user provider implementation
using both the username and email fields. To use it, change the id
of your user provider to use this implementation instead of the base one
using only the username:

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        providers:
            nucleos_userbundle:
                id: nucleos_user.user_provider.username_email
