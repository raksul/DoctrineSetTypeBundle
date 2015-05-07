<?php

namespace Raksul\DoctrineSetTypeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice;

/**
 * SET type constraint
 *
 * @author Yuichi Okada <y.okada@raksul.com>
 *
 * @Annotation
 */
class SetType extends Choice
{
    /**
     * @var string $class validation target class name
     */
    public $class;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['class'];
    }
}
