Doctrine Implementations
========================

This chapter describes some things specific to these implementations.

Replacing the mapping of the bundle
-----------------------------------

None of the Doctrine projects currently allow overwriting part of the mapping
of a mapped superclass in the child entity.

If you need to change the mapping (for instance to adapt the field names
to a legacy database), one solution could be to write the whole mapping again
without inheriting the mapping from the mapped superclass. In such case,
your entity should extend directly from ``Nucleos\UserBundle\Model\User`` (and
``Nucleos\UserBundle\Model\Group`` for the group). Another solution can be through
`doctrine attribute and relations overrides`_.

.. caution::

    It is highly recommended to map all fields used by the bundle (see the
    mapping files of the bundle in ``src/Resources/config/doctrine-mapping/``). Omitting
    them can lead to unexpected behaviors and should be done carefully.

.. _doctrine attribute and relations overrides: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html#overrides
