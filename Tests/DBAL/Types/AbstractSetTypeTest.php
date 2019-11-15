<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use Phake;
use PHPUnit\Framework\TestCase;
use Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;
use Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType;

/**
 * AbstractSetTypeTest
 *
 * @author Yuichi Okada <y.okada@raksul.com>
 *
 * @coversDefaultClass \Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType
 */
class AbstractSetTypeTest extends TestCase
{
    /**
     * @var AbstractSetType $type AbstractSetType
     */
    private $type;

    public static function setUpBeforeClass()
    {
        Type::addType('UserGroupType', UserGroupType::class);
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

    public function testThrowsExceptionConvertToDatabaseValueInCaseInvalidValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->type->convertToDatabaseValue(['InvalidValue'], new MySqlPlatform());
    }

    /**
     * @dataProvider convertToPHPValueProvider
     * @param mixed $value
     * @param array $expected
     */
    public function testConvertToPHPValue($value, array $expected): void
    {
        $this->assertEquals($expected, $this->type->convertToPHPValue($value, new MySqlPlatform()));
    }

    /**
     * Data provider for method convertToPHPValue
     */
    public function convertToPHPValueProvider(): array
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

    public function testGetSqlDeclaration(): void
    {
        $fieldDeclaration = ['name' => 'groups'];
        $platform  = new MySqlPlatform();
        $expected = "SET('group1', 'group2', 'group3')";

        $this->assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
    }

    public function testGetSqlDeclarationIfNotMySqlPlatform(): void
    {
        $fieldDeclaration = ['name' => 'groups'];
        $platform = Phake::mock(AbstractPlatform::class);
        Phake::when($platform)->getClobTypeDeclarationSQL($fieldDeclaration)->thenReturn('CLOB');

        $this->assertEquals('CLOB', $this->type->getSqlDeclaration($fieldDeclaration, $platform));
    }

    public function testRequiresSQLCommentHint(): void
    {
        $platform = Phake::mock(AbstractPlatform::class);
        $this->assertTrue($this->type->requiresSQLCommentHint($platform));
    }

    public function testGetName(): void
    {
        $this->assertEquals('UserGroupType', $this->type->getName());
    }
}
