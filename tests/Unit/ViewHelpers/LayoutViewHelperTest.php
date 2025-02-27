<?php

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3Fluid\Fluid\Tests\Unit\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Tests\Unit\Core\Rendering\RenderingContextFixture;
use TYPO3Fluid\Fluid\ViewHelpers\LayoutViewHelper;

class LayoutViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @test
     */
    public function testInitializeArgumentsRegistersExpectedArguments()
    {
        $instance = $this->getMock(LayoutViewHelper::class, ['registerArgument']);
        $instance->expects(self::exactly(1))->method('registerArgument')->with('name', 'string', self::anything());
        $instance->initializeArguments();
    }

    /**
     * @test
     * @dataProvider getPostParseEventTestValues
     * @param string $expectedLayoutName
     */
    public function testPostParseEvent(array $arguments, $expectedLayoutName)
    {
        $variableContainer = new StandardVariableProvider();
        $node = new ViewHelperNode(new RenderingContextFixture(), 'f', 'layout', $arguments, new ParsingState());
        $result = LayoutViewHelper::postParseEvent($node, $arguments, $variableContainer);
        self::assertNull($result);
        self::assertEquals($expectedLayoutName, $variableContainer->get('layoutName'));
    }

    /**
     * @return array
     */
    public function getPostParseEventTestValues()
    {
        return [
            [['name' => 'test'], 'test'],
            [[], new TextNode('Default')],
        ];
    }
}
