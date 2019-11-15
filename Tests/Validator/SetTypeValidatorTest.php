<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\Validator;

use Raksul\DoctrineSetTypeBundle\Exception\TargetClassNotExistException;
use Raksul\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType;
use Raksul\DoctrineSetTypeBundle\Validator\Constraints\SetType;
use Raksul\DoctrineSetTypeBundle\Validator\Constraints\SetTypeValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
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

    public function testNullIsValid(): void
    {
        $constraint = new SetType([
            'class' => UserGroupType::class
        ]);
        $this->validator->validate(null, $constraint);

        $this->assertNoViolation();
    }

    public function testEmptyArrayIsValid(): void
    {
        $constraint = new SetType([
            'class' => UserGroupType::class
        ]);
        $this->validator->validate([], $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider validParamProvider
     * @param array $param
     */
    public function testValidSetArray($param): void
    {
        $constraint = new SetType([
            'class' => UserGroupType::class,
        ]);
        $this->validator->validate($param, $constraint);

        $this->assertNoViolation();
    }

    public function testInvalidValue(): void
    {
        $constraint = new SetType([
            'class' => UserGroupType::class,
            'multipleMessage' => 'myMessage',
        ]);

        $this->validator->validate(['InvalidValue'], $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"InvalidValue"')
            ->setCode(Choice::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }

    public function testTargetOptionExpected(): void
    {
        $this->expectException(MissingOptionsException::class);
        new SetType();
    }

    public function testThrowsExceptionIfNoClassSpecified(): void
    {
        $this->expectException(ConstraintDefinitionException::class);
        $constraint = new SetType([
            'class' => null,
        ]);

        $this->validator->validate([UserGroupType::GROUP1], $constraint);
    }

    public function testThrowsExceptionIfNonExistentClassSpecified(): void
    {
        $this->expectException(TargetClassNotExistException::class);
        $constraint = new SetType([
            'class' => 'NotExistClassName',
        ]);

        $this->validator->validate([UserGroupType::GROUP1], $constraint);
    }

    /**
     * Data provider for method testValidParam
     */
    public function validParamProvider(): array
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
