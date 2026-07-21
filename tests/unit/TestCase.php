<?php
/**
 * Base unit test case.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit;

use OpenFields\Tests\WpStubs;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

/**
 * Base test case that resets the WordPress function stub registry around each
 * test to keep them isolated.
 */
abstract class TestCase extends PhpUnitTestCase {

	/**
	 * Reset stubs before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		WpStubs::reset();
	}

	/**
	 * Reset stubs after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		WpStubs::reset();
		parent::tearDown();
	}
}
