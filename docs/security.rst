Advanced routing configuration
==============================

The bundle itself provides no speical security handling. There is no brutforce protection or IP blocking.

Password restrictions
---------------------

There is a pattern constraint that can be used to create stronger user passwords:

.. code-block:: yaml

    # config/validator/validation.yaml
    Nucleos\UserBundle\Form\Model\ChangePassword:
        properties:
            plainPassword:
                - Nucleos\UserBundle\Validator\Constraints\Pattern:
                    minUpper: 1
                    minLower: 1
                    minNumeric: 1
                    minSpecial: 1
                - Length:
                    min: 12

.. code-block:: php

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

        #[\Nucleos\UserBundle\Validator\Constraints\Pattern(minUpper: 1, minLower: 1, minNumeric: 1, minSpecial: 1)]
        protected ?string $plainPassword = null;
    }
