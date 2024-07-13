Using groups
============

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

.. code-block:: yaml

    # config/packages/nucleos_user.yaml
    nucleos_user:
        firewall_name: main
        user_class: App\Entity\User
        group:
            group_class: App\Entity\Group

The Group class
---------------

The simplest way to create a Group class is to extend the mapped superclass
provided by the bundle.

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

Defining the User-Group relation
--------------------------------

The next step is to map the relation in your ``User`` class.

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
