<?php
/**
 * Tests for the Validator.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\FieldGroups\FieldGroup;
use OpenFields\FieldGroups\Validator;
use OpenFields\FieldTypes\FieldTypeRegistry;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\FieldGroups\Validator
 */
final class ValidatorTest extends TestCase {

	/**
	 * @return Validator
	 */
	private function validator(): Validator {
		$registry = new FieldTypeRegistry();
		$registry->register_defaults();

		return new Validator( $registry );
	}

	/**
	 * @return FieldGroup
	 */
	private function group(): FieldGroup {
		return FieldGroup::from_array(
			array(
				'fields' => array(
					array(
						'key'      => 'field_email',
						'name'     => 'email',
						'type'     => 'email',
						'required' => true,
					),
					array(
						'key'  => 'field_headline',
						'name' => 'headline',
						'type' => 'text',
					),
				),
			)
		);
	}

	public function test_valid_values_produce_no_errors(): void {
		$errors = $this->validator()->validate(
			$this->group(),
			array(
				'email'    => 'user@example.com',
				'headline' => 'Hello',
			)
		);

		$this->assertFalse( $errors->has_errors() );
	}

	public function test_errors_are_keyed_by_field_key(): void {
		$errors = $this->validator()->validate(
			$this->group(),
			array(
				'email'    => 'not-an-email',
				'headline' => 'Hello',
			)
		);

		$this->assertTrue( $errors->has_errors() );

		$map = Validator::to_map( $errors );

		$this->assertArrayHasKey( 'field_email', $map );
		$this->assertArrayNotHasKey( 'field_headline', $map );
		$this->assertNotSame( '', $map['field_email'] );
	}

	public function test_required_field_reports_an_error_when_empty(): void {
		$map = Validator::to_map(
			$this->validator()->validate( $this->group(), array() )
		);

		$this->assertArrayHasKey( 'field_email', $map );
	}
}
