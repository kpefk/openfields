<?php
/**
 * Tests for the Sanitizer.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Support;

use OpenFields\Support\Sanitizer;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\Support\Sanitizer
 */
final class SanitizerTest extends TestCase {

	public function test_key_normalises_to_underscore_slug(): void {
		$this->assertSame( 'field_hero_1', Sanitizer::key( 'Field-Hero 1!' ) );
		$this->assertSame( 'a_b_c', Sanitizer::key( 'A/B\\C' ) );
		$this->assertSame( 'already_valid', Sanitizer::key( 'already_valid' ) );
	}

	public function test_config_sanitizes_strings_recursively(): void {
		$input = array(
			'title'  => '  Hello <b>World</b>  ',
			'nested' => array(
				'label' => 'Nice <script>alert(1)</script>',
				'count' => 5,
				'flag'  => true,
			),
		);

		$clean = Sanitizer::config( $input );

		$this->assertSame( 'Hello World', $clean['title'] );
		$this->assertSame( 'Nice alert(1)', $clean['nested']['label'] );
		$this->assertSame( 5, $clean['nested']['count'] );
		$this->assertTrue( $clean['nested']['flag'] );
	}
}
