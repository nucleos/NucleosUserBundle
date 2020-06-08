Two Factor Auth
===============

If you want to secure the login, you could enable a two factor protection. When enabled
the user is asked to enter a security token after the credentials have been entered.

The temporary token is send via mail and is valid for a short time. When entered correctly,
a device token is stored locally via cookie. The next time the user logins (using the same
browser), the token is recognized and no new 2FA token is needed.

To enable this feature, you have to create two new entities and define it your configuration.

The ``trusted_device_class`` key must implement the ``Nucleos\UserBundle\Model\TrustedDeviceInterface``
interface.

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        two_factor:
            trusted_device_class:    App\Entity\DeviceToken

By default a token is valid for 30 minutes and has a length of 4 characters.

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        two_factor:
            token_length:   4
            token_ttl:      1800

After entering 5 wrong tokens, a new token can be requested after 5 minutes.

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        two_factor:
            retry_delay:    300
            retry_limit:    5

The device token is stored into a cookie.

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        two_factor:
            cookie_name:    device_token

The Token class
---------------

The simplest way to create a Token class is to extend the mapped superclass
provided by the bundle.

a) ORM Token class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php-annotations

    // src/Entity/Token.php
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Nucleos\UserBundle\Model\Token as BaseToken;

    /**
     * @ORM\Entity
     * @ORM\Table(name="nucleos_user__token")
     */
    class Token extends BaseToken
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
         protected $id;

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\User",
         *     cascade={"persist"}
         * )
         * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
         */
        protected $user;
    }

b) MongoDB Token class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/Document/Token.php
    namespace App\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Nucleos\UserBundle\Model\Token as BaseToken;

    /**
     * @MongoDB\Document
     */
    class Token extends BaseToken
    {
        /**
         * @MongoDB\Id(strategy="auto")
         */
        protected $id;

        /**
         * @MongoDB\ManyToOne(targetEntity="App\Entity\User",
         *     cascade={"persist"}
         * )
         * @MongoDB\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
         */
        protected $user;
    }

The TrustedDevice class
-----------------------

The simplest way to create a TrustedDevice class is to extend the mapped superclass
provided by the bundle.

a) ORM TrustedDevice class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php-annotations

    // src/Entity/TrustedDevice.php
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Nucleos\UserBundle\Model\TrustedDevice as BaseTrustedDevice;

    /**
     * @ORM\Entity
     * @ORM\Table(name="nucleos_user__trusted_device")
     */
    class TrustedDevice extends BaseTrustedDevice
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
         protected $id;

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\User",
         *     cascade={"persist"}
         * )
         * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
         */
        protected $user;
    }

b) MongoDB TrustedDevice class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/Document/TrustedDevice.php
    namespace App\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Nucleos\UserBundle\Model\TrustedDevice as BaseTrustedDevice;

    /**
     * @MongoDB\Document
     */
    class TrustedDevice extends BaseTrustedDevice
    {
        /**
         * @MongoDB\Id(strategy="auto")
         */
        protected $id;

        /**
         * @MongoDB\ManyToOne(targetEntity="App\Entity\User",
         *     cascade={"persist"}
         * )
         * @MongoDB\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
         */
        protected $user;
    }
