<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\DBAL\Types;

use Raksul\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser;
use Phake;

/**
 * SetTypeGueserTest
 *
 * @author Yuichi Okada <y.okada@raksul.com>
 */
class SetTypeGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $managerRegistory = Phake::mock('Doctrine\Common\Persistence\ManagerRegistry');
        $registeredTypes = ['UserGroupType' => ['class' => 'Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType']];
        /*
         * @var Raksul\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser
         */
        $this->guesser = Phake::partialMock(
            'Raksul\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser',
            $managerRegistory,
            $registeredTypes,
            'Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType'
        );
    }

    public function testNotGuessType()
    {
        $class = 'Raksul\SomeEntity';
        $property = 'groups';

        Phake::when($this->guesser)->getMetadata($class)->thenReturn(null);
        $this->assertNull($this->guesser->guessType($class, $property));
    }

    public function testNotRegisteredType()
    {
        $class = 'Raksul\SomeEntity';
        $property = 'string_field';

        $classMetadata = Phake::mock('Doctrine\ORM\Mapping\ClassMetadata');
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('string');

        Phake::when($this->guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertNull($this->guesser->guessType($class, $property));
    }

    public function testThrowsException()
    {
        $managerRegistory = Phake::mock('Doctrine\Common\Persistence\ManagerRegistry');
        $registeredTypes = ['InvalidType' => ['class' => 'Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\InvalidType']];

        $guesser = Phake::partialMock(
            'Raksul\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser',
            $managerRegistory,
            $registeredTypes,
            'Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType'
        );

        $class = 'Raksul\SomeEntity';
        $property = 'groups';

        $classMetadata = Phake::mock('Doctrine\ORM\Mapping\ClassMetadata');
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('InvalidType');

        Phake::when($guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertNull($guesser->guessType($class, $property));
    }

    public function testGessingSetType()
    {
        $class = 'Raksul\SomeEntity';
        $property = 'groups';

        $classMetadata = Phake::mock('Doctrine\ORM\Mapping\ClassMetadata');
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('UserGroupType');

        Phake::when($this->guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertInstanceOf('Symfony\Component\Form\Guess\TypeGuess', $this->guesser->guessType($class, $property));
    }
}
