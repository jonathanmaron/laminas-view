<?php

/**
 * @see       https://github.com/laminas/laminas-view for the canonical source repository
 * @copyright https://github.com/laminas/laminas-view/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-view/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\View\Helper\Navigation;

class AbstractHelperTest extends AbstractTest
{
    // @codingStandardsIgnoreStart
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Laminas\View\Helper\Navigation';

    /**
     * View helper
     *
     * @var \Laminas\View\Helper\Navigation\Breadcrumbs
     */
    protected $_helper;
    // @codingStandardsIgnoreEnd

    protected function tearDown()
    {
        parent::tearDown();

        if ($this->_helper) {
            $this->_helper->setDefaultAcl(null);
            $this->_helper->setAcl(null);
            $this->_helper->setDefaultRole(null);
            $this->_helper->setRole(null);
        }
    }

    public function testHasACLChecksDefaultACL()
    {
        $aclContainer = $this->_getAcl();
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->_helper->hasACL());
        $this->_helper->setDefaultAcl($acl);
        $this->assertEquals(true, $this->_helper->hasAcl());
    }

    public function testHasACLChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->_helper->hasAcl());
        $this->_helper->setAcl($acl);
        $this->assertEquals(true, $this->_helper->hasAcl());
    }

    public function testHasRoleChecksDefaultRole()
    {
        $aclContainer = $this->_getAcl();
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->_helper->hasRole());
        $this->_helper->setDefaultRole($role);
        $this->assertEquals(true, $this->_helper->hasRole());
    }

    public function testHasRoleChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->_helper->hasRole());
        $this->_helper->setRole($role);
        $this->assertEquals(true, $this->_helper->hasRole());
    }

    public function testEventManagerIsNullByDefault()
    {
        $this->assertNull($this->_helper->getEventManager());
    }
}
