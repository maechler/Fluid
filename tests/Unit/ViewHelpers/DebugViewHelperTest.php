<?php

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3Fluid\Fluid\Tests\Unit\ViewHelpers;

use TYPO3Fluid\Fluid\Tests\Unit\Core\Rendering\RenderingContextFixture;
use TYPO3Fluid\Fluid\Tests\Unit\ViewHelpers\Fixtures\UserWithoutToString;
use TYPO3Fluid\Fluid\ViewHelpers\DebugViewHelper;

class DebugViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @test
     */
    public function testInitializeArgumentsRegistersExpectedArguments()
    {
        $instance = $this->getMock(DebugViewHelper::class, ['registerArgument']);
        $instance->expects(self::atLeastOnce())->method('registerArgument')->withConsecutive(
            ['typeOnly', 'boolean', self::anything(), false, false],
            ['levels', 'integer', self::anything(), false, 5, null]
        );
        $instance->setRenderingContext(new RenderingContextFixture());
        $instance->initializeArguments();
    }

    /**
     * @dataProvider getRenderTestValues
     * @param mixed $value
     * @param array $arguments
     * @param string $expected
     */
    public function testRender($value, array $arguments, $expected = null)
    {
        $instance = $this->getMock(DebugViewHelper::class, ['renderChildren']);
        $instance->expects(self::once())->method('renderChildren')->willReturn($value);
        $instance->setArguments($arguments);
        $instance->setRenderingContext(new RenderingContextFixture());
        $result = $instance->render();
        if ($expected) {
            self::assertEquals($expected, $result);
        }
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        $arrayObject = new \ArrayObject(['foo' => 'bar']);
        $recursive = clone $arrayObject;
        $recursive['recursive'] = $arrayObject;
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'Hello world');
        return [
            ['test', ['typeOnly' => false, 'html' => false, 'levels' => 1], "string 'test'" . PHP_EOL],
            ['test', ['typeOnly' => true, 'html' => false, 'levels' => 1], 'string'],
            [
                'test<strong>bold</strong>',
                ['typeOnly' => false, 'html' => true, 'levels' => 1],
                '<code>string = \'test&lt;strong&gt;bold&lt;/strong&gt;\'</code>'
            ],
            [
                ['nested' => 'test<strong>bold</strong>'],
                ['typeOnly' => false, 'html' => true, 'levels' => 1],
                '<code>array</code><ul><li>nested: <code>string = \'test&lt;strong&gt;bold&lt;/strong&gt;\'</code></li></ul>'
            ],
            [
                ['foo' => 'bar'],
                ['typeOnly' => false, 'html' => true, 'levels' => 2],
                '<code>array</code><ul><li>foo: <code>string = \'bar\'</code></li></ul>'
            ],
            [
                $arrayObject,
                ['typeOnly' => false, 'html' => true, 'levels' => 2],
                '<code>ArrayObject</code><ul><li>foo: <code>string = \'bar\'</code></li></ul>'
            ],
            [
                new \ArrayIterator(['foo' => 'bar']),
                ['typeOnly' => false, 'html' => true, 'levels' => 2],
                '<code>ArrayIterator</code><ul><li>foo: <code>string = \'bar\'</code></li></ul>'
            ],
            [
                ['foo' => 'bar'],
                ['typeOnly' => false, 'html' => false, 'levels' => 3],
                'array: ' . PHP_EOL . '  "foo": string \'bar\'' . PHP_EOL
            ],
            [
                $arrayObject,
                ['typeOnly' => false, 'html' => false, 'levels' => 3],
                'ArrayObject: ' . PHP_EOL . '  "foo": string \'bar\'' . PHP_EOL
            ],
            [
                new \ArrayIterator(['foo' => 'bar']),
                ['typeOnly' => false, 'html' => false, 'levels' => 3],
                'ArrayIterator: ' . PHP_EOL . '  "foo": string \'bar\'' . PHP_EOL
            ],
            [
                new UserWithoutToString('username'),
                ['typeOnly' => false, 'html' => false, 'levels' => 3],
                UserWithoutToString::class . ': ' . PHP_EOL . '  "name": string \'username\'' . PHP_EOL
            ],
            [
                null,
                ['typeOnly' => false, 'html' => false, 'levels' => 3],
                'null' . PHP_EOL
            ],
            [
                $recursive,
                ['typeOnly' => false, 'html' => false, 'levels' => 1],
                'ArrayObject: ' . PHP_EOL . '  "foo": string \'bar\'' . PHP_EOL . '  "recursive": ArrayObject: *Recursion limited*'
            ],
            [
                $recursive,
                ['typeOnly' => false, 'html' => true, 'levels' => 1],
                '<code>ArrayObject</code><ul><li>foo: <code>string = \'bar\'</code></li><li>recursive: <code>ArrayObject</code><i>Recursion limited</i></li></ul>'
            ],
            [
                $stream,
                ['typeOnly' => false, 'html' => false, 'levels' => 1]
            ],
            [
                \DateTime::createFromFormat('U', '1468328915'),
                ['typeOnly' => false, 'html' => false, 'levels' => 3],
                'DateTime: ' . PHP_EOL . '  "class": string \'DateTime\'' . PHP_EOL .
                '  "ISO8601": string \'2016-07-12T13:08:35+00:00\'' . PHP_EOL . '  "UNIXTIME": integer 1468328915' . PHP_EOL
            ]
        ];
    }
}
