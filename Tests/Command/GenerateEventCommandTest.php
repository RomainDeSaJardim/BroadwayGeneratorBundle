<?php

namespace RomainDeSaJardim\Bundle\BroadwayGeneratorBundle\Tests\Command;

use RomainDeSaJardim\Bundle\BroadwayGeneratorBundle\Command\GenerateEventCommand;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateEventCommandTest extends GenerateCommandTest
{
    protected $generator;

    protected $bundle;

    /**
     * @dataProvider getInteractiveCommandData
     */
    public function testInteractiveCommand($options, $input, $expected)
    {
        list($bundle, $name) = $expected;

        $generator = $this->getGenerator();
        $generator
            ->expects($this->once())
            ->method('generate')
            ->with($this->getBundle(), $name)
        ;

        $tester = new CommandTester($command = $this->getCommand($generator));
        $this->setInputs($tester, $command, $input);
        $tester->execute($options);
    }

    public function getInteractiveCommandData()
    {
        return array(
            array(
                array(),
                "FooBarBundle\nFooBar\n\n",
                array('FooBarBundle', 'FooBar'),
            ),

            array(
                array(),
                "FooBarBundle\nFooBar\napp.foo.projector\n",
                array('FooBarBundle', 'FooBar', 'app.foo.projector'),
            ),

            array(
                array('bundle' => 'FooBarBundle'),
                "FooBar\n\n",
                array('FooBarBundle', 'FooBar'),
            ),

            array(
                array('bundle' => 'FooBarBundle'),
                "FooBar\napp.foo.projector\n",
                array('FooBarBundle', 'FooBar'),
            ),

            array(
                array('name' => 'FooBar'),
                "FooBarBundle\n\n",
                array('FooBarBundle', 'FooBar'),
            ),

            array(
                array('name' => 'FooBar'),
                "FooBarBundle\napp.foo.projector\n",
                array('FooBarBundle', 'FooBar'),
            ),

            array(
                array('projector' => 'app.foo.projector'),
                "FooBarBundle\nFooBar\n",
                array('FooBarBundle', 'FooBar', 'app.foo.projector'),
            ),

            array(
                array('bundle' => 'FooBarBundle', 'name' => 'FooBar'),
                '\n',
                array('FooBarBundle', 'FooBar'),
            ),

            array(
                array('bundle' => 'FooBarBundle', 'projector' => 'app.foo.projector'),
                "FooBar\n",
                array('FooBarBundle', 'FooBar', 'app.foo.projector'),
            ),

            array(
                array('name' => 'FooBar', 'projector' => 'app.foo.projector'),
                "FooBarBundle\n",
                array('FooBarBundle', 'FooBar', 'app.foo.projector'),
            ),

            array(
                array('bundle' => 'FooBarBundle', 'name' => 'FooBar'),
                "app.foo.projector\n",
                array('FooBarBundle', 'FooBar', 'app.foo.projector'),
            ),

            array(
                array('bundle' => 'FooBarBundle', 'name' => 'FooBar', 'projector' => 'app.foo.projector'),
                '',
                array('FooBarBundle', 'FooBar', 'app.foo.projector'),
            ),
        );
    }

    /**
     * @dataProvider getNonInteractiveCommandData
     */
    public function testNonInteractiveCommand($options, $expected)
    {
        list($bundle, $name) = $expected;

        $generator = $this->getGenerator();
        $generator
            ->expects($this->once())
            ->method('generate')
            ->with($this->getBundle(), $name)
        ;

        $tester = new CommandTester($command = $this->getCommand($generator));
        $tester->execute($options, array('interactive' => false));
    }

    public function getNonInteractiveCommandData()
    {
        return array(
            array(
                array('bundle' => 'FooBarBundle', 'name' => 'FooBar', 'projector' => 'app.foo.projector'),
                array('FooBarBundle', 'FooBar', 'app.foo.projector'),
            ),
        );
    }

    protected function getCommand($generator)
    {
        $command = new GenerateEventCommand();

        $command->setContainer($this->getContainer());
        $command->setHelperSet($this->getHelperSet());
        $command->setGenerator($generator);

        return $command;
    }

    protected function getGenerator()
    {
        if (null === $this->generator) {
            $this->setGenerator();
        }

        return $this->generator;
    }

    protected function setGenerator()
    {
        // get a noop generator
        $this->generator = $this
            ->getMockBuilder('RomainDeSaJardim\Bundle\BroadwayGeneratorBundle\Generator\EventGenerator')
            ->disableOriginalConstructor()
            ->setMethods(array('generate'))
            ->getMock()
        ;
    }

    protected function getBundle()
    {
        if (null == $this->bundle) {
            $this->setBundle();
        }

        return $this->bundle;
    }

    protected function setBundle()
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue(''));
        $bundle->expects($this->any())->method('getName')->will($this->returnValue('FooBarBundle'));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue('Foo\BarBundle'));

        $this->bundle = $bundle;
    }
}
