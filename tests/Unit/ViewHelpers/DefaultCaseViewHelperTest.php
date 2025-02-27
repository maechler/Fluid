<?php

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3Fluid\Fluid\Tests\Unit\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3Fluid\Fluid\ViewHelpers\DefaultCaseViewHelper;

class DefaultCaseViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @test
     */
    public function testThrowsExceptionIfUsedOutsideSwitch()
    {
        $viewHelper = new DefaultCaseViewHelper();
        $this->injectDependenciesIntoViewHelper($viewHelper);
        $this->setExpectedException(Exception::class);
        $viewHelper->render();
    }

    /**
     * @test
     */
    public function testCallsRenderChildrenWhenUsedInsideSwitch()
    {
        $viewHelper = $this->getAccessibleMock(DefaultCaseViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren');
        $renderingContext = $this->getMock(RenderingContext::class, ['getViewHelperVariableContainer'], [], '', false);
        $variableContainer = $this->getMock(ViewHelperVariableContainer::class, ['exists']);
        $variableContainer->expects(self::once())->method('exists')->willReturn(true);
        $renderingContext->expects(self::once())->method('getViewHelperVariableContainer')->willReturn($variableContainer);
        $viewHelper->_set('renderingContext', $renderingContext);
        $viewHelper->render();
    }
}
