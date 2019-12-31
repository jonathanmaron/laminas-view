<?php

/**
 * @see       https://github.com/laminas/laminas-view for the canonical source repository
 * @copyright https://github.com/laminas/laminas-view/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-view/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\View\Helper;

use Laminas\Paginator;
use Laminas\View\Helper;
use Laminas\View\Renderer\PhpRenderer as View;
use Laminas\View\Resolver;

/**
 * @group      Laminas_View
 * @group      Laminas_View_Helper
 */
class PaginationControlTest extends \PHPUnit_Framework_TestCase
{
    // @codingStandardsIgnoreStart
    /**
     * @var Helper\PaginationControl
     */
    private $_viewHelper;

    private $_paginator;
    // @codingStandardsIgnoreEnd

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->markTestIncomplete('Re-enable after laminas-paginator is updated to laminas-servicemanager v3');

        $resolver = new Resolver\TemplatePathStack(['script_paths' => [
            __DIR__ . '/_files/scripts',
        ]]);
        $view = new View();
        $view->setResolver($resolver);

        Helper\PaginationControl::setDefaultViewPartial(null);
        $this->_viewHelper = new Helper\PaginationControl();
        $this->_viewHelper->setView($view);
        $adapter = new Paginator\Adapter\ArrayAdapter(range(1, 101));
        $this->_paginator = new Paginator\Paginator($adapter);
    }

    public function tearDown()
    {
        unset($this->_viewHelper);
        unset($this->_paginator);
    }

    public function testGetsAndSetsView()
    {
        $view   = new View();
        $helper = new Helper\PaginationControl();
        $this->assertNull($helper->getView());
        $helper->setView($view);
        $this->assertInstanceOf('Laminas\View\Renderer\RendererInterface', $helper->getView());
    }

    public function testGetsAndSetsDefaultViewPartial()
    {
        $this->assertNull(Helper\PaginationControl::getDefaultViewPartial());
        Helper\PaginationControl::setDefaultViewPartial('partial');
        $this->assertEquals('partial', Helper\PaginationControl::getDefaultViewPartial());
        Helper\PaginationControl::setDefaultViewPartial(null);
    }

    public function testUsesDefaultViewPartialIfNoneSupplied()
    {
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->__invoke($this->_paginator);
        $this->assertContains('pagination control', $output, $output);
        Helper\PaginationControl::setDefaultViewPartial(null);
    }

    public function testThrowsExceptionIfNoViewPartialFound()
    {
        try {
            $this->_viewHelper->__invoke($this->_paginator);
        } catch (\Exception $e) {
            $this->assertInstanceOf('Laminas\View\Exception\ExceptionInterface', $e);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }

    /**
     * @group Laminas-4037
     */
    public function testUsesDefaultScrollingStyleIfNoneSupplied()
    {
        // First we'll make sure the base case works
        $output = $this->_viewHelper->__invoke($this->_paginator, 'All', 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Paginator\Paginator::setDefaultScrollingStyle('All');
        $output = $this->_viewHelper->__invoke($this->_paginator, null, 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->__invoke($this->_paginator);
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }

    /**
     * @group Laminas-4153
     */
    public function testUsesPaginatorFromViewIfNoneSupplied()
    {
        $this->_viewHelper->getView()->paginator = $this->_paginator;
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $output = $this->_viewHelper->__invoke();

        $this->assertContains('pagination control', $output, $output);
    }

    /**
     * @group Laminas-4153
     */
    public function testThrowsExceptionIfNoPaginatorFound()
    {
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $this->setExpectedException(
            'Laminas\View\Exception\ExceptionInterface',
            'No paginator instance provided or incorrect type'
        );
        $this->_viewHelper->__invoke();
    }

    /**
     * @group Laminas-4233
     */
    public function testAcceptsViewPartialInOtherModule()
    {
        try {
            $this->_viewHelper->__invoke($this->_paginator, null, ['partial.phtml', 'test']);
        } catch (\Exception $e) {
            /* We don't care whether or not the module exists--we just want to
             * make sure it gets to Laminas_View_Helper_Partial and it's recognized
             * as a module. */
            $this->assertInstanceOf(
                'Laminas\View\Exception\RuntimeException',
                $e,
                sprintf(
                    'Expected View RuntimeException; received "%s" with message: %s',
                    get_class($e),
                    $e->getMessage()
                )
            );
            $this->assertContains('could not resolve', $e->getMessage());
        }
    }

    /**
     * @group Laminas-4328
     */
    public function testUsesPaginatorFromViewOnlyIfNoneSupplied()
    {
        $this->_viewHelper->getView()->vars()->paginator  = $this->_paginator;
        $paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter(range(1, 30)));
        Helper\PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $output = $this->_viewHelper->__invoke($paginator);
        $this->assertContains('page count (3)', $output, $output);
    }

    /**
     * @group Laminas-4878
     */
    public function testCanUseObjectForScrollingStyle()
    {
        $all = new Paginator\ScrollingStyle\All();

        $output = $this->_viewHelper->__invoke($this->_paginator, $all, 'testPagination.phtml');

        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }
}
