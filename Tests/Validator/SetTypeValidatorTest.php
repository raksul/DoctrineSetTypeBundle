<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\Validator;

use Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType;
use Raksul\DoctrineSetTypeBundle\Validator\Constraints\SetType;
use Raksul\DoctrineSetTypeBundle\Validator\Constraints\SetTypeValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * SetTypeValidatorTest
 *
 * @author Yuichi Okada <y.okada@raksul.com>
 */
class SetTypeValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new SetTypeValidator();
    }

    public function testNullIsValid()
    {
        $constraint = new SetType([
            'class' => 'Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType'
        ]);
        $this->validator->validate(null, $constraint);

        $this->assertNoViolation();
    }

    public function testEmptyArrayIsValid()
    {
        $constraint = new SetType([
            'class' => 'Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType'
        ]);
        $this->validator->validate([], $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider testValidParamProvider
     * @param array $param
     */
    public function testValidSetArray($param)
    {
        $constraint = new SetType([
            'class' => 'Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType',
        ]);
        $this->validator->validate($param, $constraint);

        $this->assertNoViolation();
    }

    public function testInvalidValue()
    {
        $constraint = new SetType([
            'class' => 'Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType',
            'multipleMessage' => 'myMessage',
        ]);

        $this->validator->validate(['InvalidValue'], $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"InvalidValue"')
            ->setCode(Choice::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function testTargetOptionExpected()
    {
        new SetType();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function testThrowsExceptionIfNoClassSpecified()
    {
        $constraint = new SetType([
            'class' => null,
        ]);

        $this->validator->validate([UserGroupType::GROUP1], $constraint);
    }

    /**
     * @expectedException \Raksul\DoctrineSetTypeBundle\Exception\TargetClassNotExistException
     */
    public function testThrowsExceptionIfNonExistentClassSpecified()
    {
        $constraint = new SetType([
            'class' => 'NotExistClassName',
        ]);

        $this->validator->validate([UserGroupType::GROUP1], $constraint);
    }

    /**
     * Data provider for method testValidParam
     */
    public function testValidParamProvider()
    {
        return [
            [
                [UserGroupType::GROUP1],
            ],
            [
                [UserGroupType::GROUP1, UserGroupType::GROUP2],
            ],
        ];

    }
}
