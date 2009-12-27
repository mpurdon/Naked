<?php
require_once 'deploy\lib\Naked\Application\Dispatcher.php';
require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Dispatcher test case.
 */
class DispatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Dispatcher
     */
    private $Dispatcher;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated DispatcherTest::setUp()
        $this->Dispatcher = new Naked\Application\Dispatcher(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated DispatcherTest::tearDown()
        $this->Dispatcher = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {// TODO Auto-generated constructor
}

    /**
     * Tests Dispatcher->dispatch()
     */
    public function testDispatch()
    {
        // TODO Auto-generated DispatcherTest->testDispatch()
        $this->markTestIncomplete("dispatch test not implemented");
        $this->Dispatcher->dispatch(/* parameters */);
    }

    /**
     * Tests Dispatcher->hasDispatched()
     */
    public function testHasDispatched()
    {
        $this->assertFalse($this->Dispatcher->hasDispatched());
        $this->Dispatcher->dispatch('foo');
        $this->assertTrue($this->Dispatcher->hasDispatched());
    }
}
