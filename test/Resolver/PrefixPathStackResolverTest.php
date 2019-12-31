<?php

/**
 * @see       https://github.com/laminas/laminas-view for the canonical source repository
 * @copyright https://github.com/laminas/laminas-view/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-view/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\View\Resolver;

use Laminas\View\Resolver\PrefixPathStackResolver;
use Laminas\View\Resolver\ResolverInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \Laminas\View\Resolver\PrefixPathStackResolver}
 *
 * @covers \Laminas\View\Resolver\PrefixPathStackResolver
 */
class PrefixPathStackResolverTest extends TestCase
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->basePath = realpath(__DIR__ . '/../_templates/prefix-path-stack-resolver');
    }

    public function testResolveWithoutPathPrefixes()
    {
        $resolver = new PrefixPathStackResolver();

        $this->assertNull($resolver->resolve(__DIR__));
        $this->assertNull($resolver->resolve(__FILE__));
        $this->assertNull($resolver->resolve('path/to/foo'));
        $this->assertNull($resolver->resolve('path/to/bar'));
    }

    public function testResolve()
    {
        $resolver = new PrefixPathStackResolver([
            'base1'  => $this->basePath,
            'base2' => $this->basePath . '/baz'
        ]);

        $this->assertEmpty($resolver->resolve('base1/foo'));
        $this->assertSame(realpath($this->basePath . '/bar.phtml'), $resolver->resolve('base1/bar'));
        $this->assertEmpty($resolver->resolve('base2/tab'));
        $this->assertSame(realpath($this->basePath . '/baz/taz.phtml'), $resolver->resolve('base2/taz'));
    }

    public function testResolveWithCongruentPrefix()
    {
        $resolver = new PrefixPathStackResolver([
            'foo'    => $this->basePath,
            'foobar' => $this->basePath . '/baz'
        ]);

        $this->assertSame(realpath($this->basePath . '/bar.phtml'), $resolver->resolve('foo/bar'));
        $this->assertSame(realpath($this->basePath . '/baz/taz.phtml'), $resolver->resolve('foobar/taz'));
    }

    public function testSetCustomPathStackResolver()
    {
        $mockResolver = $this->getMockBuilder(ResolverInterface::class)->getMock();

        $resolver = new PrefixPathStackResolver([
            'foo' => $mockResolver,
        ]);

        $mockResolver->expects($this->at(0))->method('resolve')->with('/bar')->will($this->returnValue('1111'));
        $mockResolver->expects($this->at(1))->method('resolve')->with('/baz')->will($this->returnValue('2222'));
        $mockResolver->expects($this->at(2))->method('resolve')->with('/tab')->will($this->returnValue(false));

        $this->assertSame('1111', $resolver->resolve('foo/bar'));
        $this->assertSame('2222', $resolver->resolve('foo/baz'));
        $this->assertNull($resolver->resolve('foo/tab'));
    }
}
