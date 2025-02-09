<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle;

/**
 * Contains all events thrown in the NucleosUserBundle.
 */
final class NucleosUserEvents
{
    /**
     * The UPDATE_SECURITY_COMPLETED event occurs after saving the user in the security update process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Nucleos\UserBundle\Event\FilterUserResponseEvent")
     */
    public const UPDATE_SECURITY_COMPLETED = 'nucleos_user.update_security.edit.completed';

    /**
     * The USER_CREATED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the created user and to add some behaviour after the creation.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_CREATED = 'nucleos_user.user.created';

    /**
     * The USER_PASSWORD_CHANGED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the created user and to add some behaviour after the password change.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_PASSWORD_CHANGED = 'nucleos_user.user.password_changed';

    /**
     * The USER_ACTIVATED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the activated user and to add some behaviour after the activation.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_ACTIVATED = 'nucleos_user.user.activated';

    /**
     * The USER_DEACTIVATED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the deactivated user and to add some behaviour after the deactivation.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_DEACTIVATED = 'nucleos_user.user.deactivated';

    /**
     * The USER_PROMOTED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the promoted user and to add some behaviour after the promotion.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_PROMOTED = 'nucleos_user.user.promoted';

    /**
     * The USER_DEMOTED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the demoted user and to add some behaviour after the demotion.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_DEMOTED = 'nucleos_user.user.demoted';

    /**
     * The USER_LOCALE_CHANGED event occurs when the user changed the locale.
     *
     * This event allows you to access the user settings and to add some behaviour after the locale change.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_LOCALE_CHANGED = 'nucleos_user.user.locale_changed';

    /**
     * The USER_TIMEZONE_CHANGED event occurs when the user changed the timezone.
     *
     * This event allows you to access the user settings and to add some behaviour after the timezone change.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const USER_TIMEZONE_CHANGED = 'nucleos_user.user.timezone_changed';

    /**
     * The UPDATE_SECURITY_INITIALIZE event occurs when the security update process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseUserEvent")
     */
    public const UPDATE_SECURITY_INITIALIZE = 'nucleos_user.update_security.edit.initialize';

    /**
     * The UPDATE_SECURITY_SUCCESS event occurs when the security update form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Nucleos\UserBundle\Event\FormEvent")
     */
    public const UPDATE_SECURITY_SUCCESS = 'nucleos_user.update_security.edit.success';

    /**
     * The RESETTING_RESET_REQUEST event occurs when a user requests a password reset of the account.
     *
     * This event allows you to check if a user is locked out before requesting a password.
     * The event listener method receives a Nucleos\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseUserEvent")
     */
    public const RESETTING_RESET_REQUEST = 'nucleos_user.resetting.reset.request';

    /**
     * The RESETTING_RESET_INITIALIZE event occurs when the resetting process is initialized.
     *
     * This event allows you to set the response to bypass the processing.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseUserEvent")
     */
    public const RESETTING_RESET_INITIALIZE = 'nucleos_user.resetting.reset.initialize';

    /**
     * The RESETTING_RESET_SUCCESS event occurs when the resetting form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Nucleos\UserBundle\Event\FormEvent ")
     */
    public const RESETTING_RESET_SUCCESS = 'nucleos_user.resetting.reset.success';

    /**
     * The RESETTING_RESET_COMPLETED event occurs after saving the user in the resetting process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Nucleos\UserBundle\Event\FilterUserResponseEvent")
     */
    public const RESETTING_RESET_COMPLETED = 'nucleos_user.resetting.reset.completed';

    /**
     * The SECURITY_LOGIN_INITIALIZE event occurs when the send email process is initialized.
     *
     * This event allows you to set the response to bypass the login.
     * The event listener method receives a Nucleos\UserBundle\Event\GetResponseLoginEvent instance.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseLoginEvent")
     */
    public const SECURITY_LOGIN_INITIALIZE = 'nucleos_user.security.login.initialize';

    /**
     * The SECURITY_LOGIN_COMPLETED event occurs after the user is logged in.
     *
     * This event allows you to set the response to bypass the the redirection after the user is logged in.
     * The event listener method receives a Nucleos\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseUserEvent")
     */
    public const SECURITY_LOGIN_COMPLETED = 'nucleos_user.security.login.completed';

    /**
     * The SECURITY_IMPLICIT_LOGIN event occurs when the user is logged in programmatically.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Nucleos\UserBundle\Event\UserEvent")
     */
    public const SECURITY_IMPLICIT_LOGIN = 'nucleos_user.security.implicit_login';

    /**
     * The RESETTING_SEND_EMAIL_INITIALIZE event occurs when the send email process is initialized.
     *
     * This event allows you to set the response to bypass the email confirmation processing.
     * The event listener method receives a Nucleos\UserBundle\Event\GetResponseNullableUserEvent instance.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseNullableUserEvent")
     */
    public const RESETTING_SEND_EMAIL_INITIALIZE = 'nucleos_user.resetting.send_email.initialize';

    /**
     * The RESETTING_SEND_EMAIL_CONFIRM event occurs when all prerequisites to send email are
     * confirmed and before the mail is sent.
     *
     * This event allows you to set the response to bypass the email sending.
     * The event listener method receives a Nucleos\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseUserEvent")
     */
    public const RESETTING_SEND_EMAIL_CONFIRM = 'nucleos_user.resetting.send_email.confirm';

    /**
     * The RESETTING_SEND_EMAIL_COMPLETED event occurs after the email is sent.
     *
     * This event allows you to set the response to bypass the the redirection after the email is sent.
     * The event listener method receives a Nucleos\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseUserEvent")
     */
    public const RESETTING_SEND_EMAIL_COMPLETED = 'nucleos_user.resetting.send_email.completed';

    /**
     * The ACCOUNT_DELETION_INITIALIZE event occurs when the account deletion is initialized.
     *
     * This event allows you to modify the default values of the deletion request before binding the form.
     *
     * @Event("Nucleos\UserBundle\Event\GetResponseAccountDeletionEvent")
     */
    public const ACCOUNT_DELETION_INITIALIZE = 'nucleos_user.account_deletion.initialize';

    /**
     * The ACCOUNT_DELETION event occurs when the account deletion is processed.
     *
     * This event allows you to process the user deletion request.
     *
     * @Event("Nucleos\UserBundle\Event\AccountDeletionEvent")
     */
    public const ACCOUNT_DELETION = 'nucleos_user.account_deletion';

    /**
     * The ACCOUNT_DELETION_SUCCESS event occurs when the account was deleted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Nucleos\UserBundle\Event\AccountDeletionResponseEvent")
     */
    public const ACCOUNT_DELETION_SUCCESS = 'nucleos_user.account_deletion.success';
}
