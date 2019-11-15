<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\DBAL\Types;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Phake;
use PHPUnit\Framework\TestCase;
use Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;
use Raksul\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser;
use Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\InvalidType;
use Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * SetTypeGueserTest
 *
 * @author Yuichi Okada <y.okada@raksul.com>
 */
class SetTypeGuesserTest extends TestCase
{
    /**
     * @var \Phake_IMock
     */
    private $guesser;

    public function setUp()
    {
        $managerRegistry = Phake::mock(ManagerRegistry::class);
        $registeredTypes = ['UserGroupType' => ['class' => UserGroupType::class]];
        /*
         * @var SetTypeGuesser
         */
        $this->guesser = Phake::partialMock(
            SetTypeGuesser::class,
            $managerRegistry,
            $registeredTypes,
            AbstractSetType::class
        );
    }

    public function testNotGuessType(): void
    {
        $class = 'Raksul\SomeEntity';
        $property = 'groups';

        Phake::when($this->guesser)->getMetadata($class)->thenReturn(null);
        $this->assertNull($this->guesser->guessType($class, $property));
    }

    public function testNotRegisteredType(): void
    {
        $class = 'Raksul\SomeEntity';
        $property = 'string_field';

        $classMetadata = Phake::mock(ClassMetadata::class);
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('string');

        Phake::when($this->guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertNull($this->guesser->guessType($class, $property));
    }

    public function testThrowsException(): void
    {
        $managerRegistry = Phake::mock(ManagerRegistry::class);
        $registeredTypes = ['InvalidType' => ['class' => InvalidType::class]];

        $guesser = Phake::partialMock(
            SetTypeGuesser::class,
            $managerRegistry,
            $registeredTypes,
            AbstractSetType::class
        );

        $class = 'Raksul\SomeEntity';
        $property = 'groups';

        $classMetadata = Phake::mock(ClassMetadata::class);
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('InvalidType');

        Phake::when($guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertNull($guesser->guessType($class, $property));
    }

    public function testGessingSetType(): void
    {
        $class = 'Raksul\SomeEntity';
        $property = 'groups';

        $classMetadata = Phake::mock(ClassMetadata::class);
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('UserGroupType');

        Phake::when($this->guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertInstanceOf(TypeGuess::class, $this->guesser->guessType($class, $property));
    }
}
