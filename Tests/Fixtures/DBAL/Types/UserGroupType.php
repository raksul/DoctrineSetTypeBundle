<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types;

use Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;

class UserGroupType extends AbstractSetType
{
    public const GROUP1 = 'group1';
    public const GROUP2 = 'group2';
    public const GROUP3 = 'group3';

    /**
     * {@inheritdoc}
     */
     protected $name = 'UserGroupType';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        self::GROUP1 => 'Group 1',
        self::GROUP2 => 'Group 2',
        self::GROUP3 => 'Group 3',
    ];
}
