<?php
/**
 * Tests for the built-in field types.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldTypes;

use OpenFields\FieldTypes\Types\Checkbox;
use OpenFields\FieldTypes\Types\Email;
use OpenFields\FieldTypes\Types\Image;
use OpenFields\FieldTypes\Types\Message;
use OpenFields\FieldTypes\Types\Number;
use OpenFields\FieldTypes\Types\Select;
use OpenFields\FieldTypes\Types\Text;
use OpenFields\FieldTypes\Types\TrueFalse;
use OpenFields\FieldTypes\Types\Url;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\FieldTypes\AbstractFieldType
 * @covers \OpenFields\FieldTypes\Types\Text
 * @covers \OpenFields\FieldTypes\Types\Number
 * @covers \OpenFields\FieldTypes\Types\Email
 * @covers \OpenFields\FieldTypes\Types\Url
 * @covers \OpenFields\FieldTypes\Types\TrueFalse
 * @covers \OpenFields\FieldTypes\Types\Image
 * @covers \OpenFields\FieldTypes\Types\AbstractChoiceFieldType
 * @covers \OpenFields\FieldTypes\Types\Select
 * @covers \OpenFields\FieldTypes\Types\Checkbox
 * @covers \OpenFields\FieldTypes\Types\Message
 */
final class FieldTypesTest extends TestCase {

	public function test_required_empty_value_returns_error(): void {
		$result = ( new Text() )->validate( '', array( 'required' => true ) );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'openfields_required', $result->get_error_code() );
	}

	public function test_optional_empty_value_is_valid(): void {
		$this->assertTrue( ( new Text() )->validate( '', array() ) );
	}

	public function test_text_enforces_maxlength(): void {
		$text  = new Text();
		$field = array( 'settings' => array( 'maxlength' => 3 ) );

		$this->assertTrue( $text->validate( 'abc', $field ) );
		$this->assertInstanceOf( \WP_Error::class, $text->validate( 'abcd', $field ) );
	}

	public function test_number_sanitizes_to_int_or_float(): void {
		$number = new Number();

		$this->assertSame( 5, $number->sanitize( '5' ) );
		$this->assertSame( 5.5, $number->sanitize( '5.5' ) );
		$this->assertSame( '', $number->sanitize( 'abc' ) );
	}

	public function test_number_enforces_bounds(): void {
		$number = new Number();
		$field  = array(
			'settings' => array(
				'min' => 1,
				'max' => 10,
			),
		);

		$this->assertTrue( $number->validate( '5', $field ) );
		$this->assertInstanceOf( \WP_Error::class, $number->validate( '0', $field ) );
		$this->assertInstanceOf( \WP_Error::class, $number->validate( '11', $field ) );
		$this->assertInstanceOf( \WP_Error::class, $number->validate( 'abc', $field ) );
	}

	public function test_email_validation(): void {
		$email = new Email();

		$this->assertTrue( $email->validate( 'user@example.com' ) );
		$this->assertInstanceOf( \WP_Error::class, $email->validate( 'not-an-email' ) );
	}

	public function test_url_validation(): void {
		$url = new Url();

		$this->assertTrue( $url->validate( 'https://example.com/page' ) );
		$this->assertInstanceOf( \WP_Error::class, $url->validate( 'not a url' ) );
	}

	public function test_true_false_stores_and_formats_boolean(): void {
		$field = new TrueFalse();

		$this->assertSame( 1, $field->sanitize( 'yes' ) );
		$this->assertSame( 1, $field->sanitize( true ) );
		$this->assertSame( 0, $field->sanitize( '0' ) );
		$this->assertTrue( $field->format_value( 1 ) );
		$this->assertFalse( $field->format_value( 0 ) );
		$this->assertSame( 'boolean', $field->get_rest_type() );
	}

	public function test_image_sanitizes_to_attachment_id(): void {
		$image = new Image();

		$this->assertSame( 42, $image->sanitize( '42' ) );
		$this->assertSame( 0, $image->sanitize( 'abc' ) );
		$this->assertSame( 42, $image->format_value( 42 ) );
		$this->assertNull( $image->format_value( 0 ) );
		$this->assertSame( 'integer', $image->get_rest_type() );
	}

	public function test_select_validates_against_choices(): void {
		$select = new Select();
		$field  = array(
			'settings' => array(
				'choices' => array(
					'a' => 'Apple',
					'b' => 'Banana',
				),
			),
		);

		$this->assertTrue( $select->validate( 'a', $field ) );
		$this->assertInstanceOf( \WP_Error::class, $select->validate( 'z', $field ) );
		$this->assertTrue( $select->validate( 'anything', array() ) );
	}

	public function test_checkbox_sanitizes_to_a_list(): void {
		$checkbox = new Checkbox();

		$this->assertSame( array( 'a', 'b' ), $checkbox->sanitize( array( 'a', 'b' ) ) );
		$this->assertSame( array(), $checkbox->sanitize( '' ) );
		$this->assertSame( array( 'x' ), $checkbox->sanitize( 'x' ) );
	}

	public function test_message_stores_no_value_and_is_always_valid(): void {
		$message = new Message();

		$this->assertFalse( $message->has_value() );
		$this->assertNull( $message->sanitize( 'anything' ) );
		$this->assertTrue( $message->validate( 'anything', array( 'required' => true ) ) );
	}
}
