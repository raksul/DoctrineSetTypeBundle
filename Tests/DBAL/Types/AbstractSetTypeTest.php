<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\DBAL\Types;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;

/**
 * AbstractSetTypeTest
 *
 * @author Yuichi Okada <yuuichi177@gmail.com>
 *
 * @coversDefaultClass \Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType
 */
class AbstractSetTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractSetType $type AbstractSetType
     */
    private $type;

    public static function setUpBeforeClass()
    {
        Type::addType('UserGroupType', '\Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType');
    }

    public function setUp()
    {
        $this->type = Type::getType('UserGroupType');
    }

    /**
     * @dataProvider convertToDatabaseValueProvider
     */
    public function testConvertToDatabaseValue($value, $expected)
    {
        $this->assertEquals($expected, $this->type->convertToDatabaseValue($value, new MySqlPlatform()));
    }

    /**
     * Data provider for method convertToDatabaseValue
     */
    public function convertToDatabaseValueProvider()
    {
        return [
            [
                null,
                null,
            ],
            [
                '',
                null,
            ],
            [
                [],
                null,
            ],
            [
                ['group1'],
                'group1',
            ],
            [
                ['group1', 'group2'],
                'group1,group2',
            ]
        ];

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionConvertToDatabaseValueInCaseInvalidValue()
    {
        $this->type->convertToDatabaseValue(['InvalidValue'], new MySqlPlatform());
    }

    /**
     * @dataProvider convertToPHPValueProvider
     */
    public function testConvertToPHPValue($value, $expected)
    {
        $this->assertEquals($expected, $this->type->convertToPHPValue($value, new MySqlPlatform()));
    }

    /**
     * Data provider for method convertToPHPValue
     */
    public function convertToPHPValueProvider()
    {
        return [
            [
                null,
                [],
            ],
            [
                '0',
                ['0'],
            ],
            [
                'group1',
                ['group1'],
            ],
            [
                'group1,group2',
                ['group1', 'group2'],
            ]
        ];
    }

    public function testGetSqlDeclaration()
    {
        $fieldDeclaration = ['name' => 'position'];
        $platform  = new MySqlPlatform();
        $expected = "SET('group1', 'group2', 'group3')";

        $this->assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
    }
}
