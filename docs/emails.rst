Sending E-Mails
===============

The NucleosUserBundle has built-in support for sending emails in two different
instances.

Password Reset
--------------

An email is also sent when a user has requested a password reset. The
NucleosUserBundle provides password reset functionality in a two-step process.
First the user must request a password reset. After the request has been
made, an email is sent containing a link to visit. Upon visiting the link,
the user will be identified by the token contained in the url. When the user
visits the link and the token is confirmed, the user will be presented with
a form to enter in a new password.

Default Mailer Implementations
------------------------------

The bundle comes with three mailer implementations. They are listed below
by service id:

- ``nucleos_user.mailer.simple`` is the default implementation, and uses symfony mailer to send emails.
- ``nucleos_user.mailer.noop`` is a mailer implementation which performs no operation, so no emails are sent.

Configuring the Sender Email Address
------------------------------------

The NucleosUserBundle default mailer allows you to configure the sender email address
of the emails sent out by the bundle. You can configure the address globally or on
a per email basis.

To configure the sender email address for all emails sent out by the bundle,
update your ``nucleos_user`` config as follows:

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        # ...
        from_email:   noreply@example.com

The bundle also provides the flexibility of allowing you to configure the sender
email address for the emails individually.

You can similarly update the ``nucleos_user`` config to change the sender email address for
the password reset request email:

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        # ...
        resetting:
            email:
                from_email:   resetting@example.com

Using A Custom Mailer
---------------------

The default mailer service used by NucleosUserBundle relies on the symfony mailer
library to send mail. If you would like to use a different library to send
emails or change the content of the email you
may do so by defining your own service.

First you must create a new class which implements ``Nucleos\UserBundle\Mailer\MailerInterface``
which is listed below:

.. code-block:: php-annotations

    namespace Nucleos\UserBundle\Mailer;

    use Nucleos\UserBundle\Model\UserInterface;

    interface MailerInterface
    {

        /**
         * Send an email to a user to confirm the password reset
         *
         * @param UserInterface $user
         */
        function sendResettingEmailMessage(UserInterface $user): void;
    }

After you have implemented your custom mailer class and defined it as a service,
you must update your bundle configuration so that NucleosUserBundle will use it.
Set the ``mailer`` configuration parameter under the ``service`` section.
An example is listed below.

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        # ...
        service:
            mailer: app.custom_nucleos_user_mailer

