UPGRADE FROM 3.x to 4.0
=======================

## Remove MongoDB support

MongoDB is not supported by this bundle anymore. All classes related to `doctrine/mongodb-odm` were removed.

## Updated namespace for Doctrine mapped super classes.

Your entities should now extend `Nucleos\UserBundle\Entity\BaseGroup` and `Nucleos\UserBundle\Entity\BaseUser` instead of `Nucleos\UserBundle\Model\Group` and `Nucleos\UserBundle\Model\User`.
