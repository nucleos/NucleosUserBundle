Using Groups With NucleosUserBundle
===================================

NucleosUserBundle allows you to associate groups to your users. Groups are a
way to group a collection of roles. The roles of a group will be granted
to all users belonging to it.

.. note::

    Symfony supports role inheritance so inheriting roles from groups is
    not always needed. If the role inheritance is enough for your use case,
    it is better to use it instead of groups as it is more efficient (loading
    the groups triggers the database).

The only mandatory configuration is the fully qualified class
name (FQCN) of your ``Group`` class which must implement ``Nucleos\UserBundle\Model\GroupInterface``.

Below is an example configuration for enabling groups support.

.. configuration-block::

    .. code-block:: yaml

        # config/packages/nucleos_user.yaml
        nucleos_user:
            db_driver: orm
            firewall_name: main
            user_class: App\Entity\User
            group:
                group_class: App\Entity\Group

The Group class
---------------

The simplest way to create a Group class is to extend the mapped superclass
provided by the bundle.

a) ORM Group class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. configuration-block::

    .. code-block:: php-annotations

        // src/Entity/Group.php
        namespace App\Entity;

        use Doctrine\ORM\Mapping as ORM;
        use Nucleos\UserBundle\Model\Group as BaseGroup;

        /**
         * @ORM\Entity
         * @ORM\Table(name="nucleos_user__group")
         */
        class Group extends BaseGroup
        {
            /**
             * @ORM\Id
             * @ORM\Column(type="integer")
             * @ORM\GeneratedValue(strategy="AUTO")
             */
             protected $id;
        }

.. note::

    ``Group`` is a reserved keyword in SQL so it cannot be used as the table name.

b) MongoDB Group class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/Document/Group.php
    namespace App\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Nucleos\UserBundle\Model\Group as BaseGroup;

    /**
     * @MongoDB\Document
     */
    class Group extends BaseGroup
    {
        /**
         * @MongoDB\Id(strategy="auto")
         */
        protected $id;
    }

Defining the User-Group relation
--------------------------------

The next step is to map the relation in your ``User`` class.

a) ORM User-Group mapping
~~~~~~~~~~~~~~~~~~~~~~~~~

.. configuration-block::

    .. code-block:: php-annotations

        // src/Entity/User.php
        namespace App\Entity;

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

            /**
             * @ORM\ManyToMany(targetEntity="App\Entity\Group")
             * @ORM\JoinTable(name="nucleos_user_user_group",
             *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
             *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
             * )
             */
            protected $groups;
        }

b) MongoDB User-Group mapping
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/Document/User.php
    namespace App\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Nucleos\UserBundle\Model\User as BaseUser;

    /**
     * @MongoDB\Document
     */
    class User extends BaseUser
    {
        /** @MongoDB\Id(strategy="auto") */
        protected $id;

        /**
         * @MongoDB\ReferenceMany(targetDocument="App\Document\Group")
         */
        protected $groups;
    }
