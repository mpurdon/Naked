<?php
require_once 'deploy\lib\Naked\Application\Environment.php';
require_once 'PHPUnit\Framework\TestCase.php';
/**
 * Environment test case.
 */
class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Environment
     */
    private $Environment;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        putenv('ENVIRONMENT=development');
        $this->Environment = new Naked\Application\Environment();
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated EnvironmentTest::tearDown()
        $this->Environment = null;
        parent::tearDown();
    }
    /**
     * Constructs the test case.
     */
    public function __construct ()
    {}

    /**
     * Tests Environment->verifyInstall()
     */
    public function testVerifyInstall ()
    {
        $environment = $_ENV['ENVIRONMENT'];

        if ($environment) {
            try {
                $this->environment->verifyInstall();
                $this->pass();
            } catch (RuntimeException $e) {
                $this->fail('Should not have thrown an exception on a set environment variable');
            }
        }

        unset($_ENV['ENVIRONMENT']);

        try {
            $this->environment->verifyInstall();
            $this->fail('Should have thrown an exception on a missing environment variable');
        } catch (RuntimeException $e) {
            $this->pass();
        }

        $_ENV['ENVIRONMENT'] = $environment;
    }
}

