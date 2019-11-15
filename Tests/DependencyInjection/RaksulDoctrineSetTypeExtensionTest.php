<?php

namespace Raksul\DoctrineSetTypeBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Raksul\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;
use Raksul\DoctrineSetTypeBundle\DependencyInjection\RaksulDoctrineSetTypeExtension;
use Raksul\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RaksulDoctrineSetTypeExtensionTest extends TestCase
{
    public function testDefault(): void
    {
        $container = new ContainerBuilder();
        $loader = new RaksulDoctrineSetTypeExtension();
        $loader->load(array(array()), $container);
        $this->assertTrue($container->hasDefinition('doctrine_set_type.set_type_guesser'), 'SetTypeGuesser is loaded');
        $this->assertEquals(AbstractSetType::class, $container->getParameter('doctrine_set_type.set_type.class_name'));
        $this->assertEquals(SetTypeGuesser::class, $container->getParameter('doctrine_set_type.set_type_guesser.class'));
    }
}
