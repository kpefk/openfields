<?php
/**
 * Tests for the LocalStore.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\FieldGroups;

use OpenFields\FieldGroups\LocalStore;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\FieldGroups\LocalStore
 */
final class LocalStoreTest extends TestCase {

	public function test_add_and_retrieve_a_group(): void {
		$store = new LocalStore();
		$store->add(
			array(
				'key'    => 'group_a',
				'title'  => 'A',
				'fields' => array(
					array(
						'key'  => 'field_x',
						'name' => 'x',
						'type' => 'text',
					),
				),
			)
		);

		$this->assertTrue( $store->has( 'group_a' ) );

		$groups = $store->all();
		$this->assertCount( 1, $groups );
		$this->assertSame( 'group_a', $groups[0]->key() );
		$this->assertSame( 'A', $groups[0]->title() );
	}

	public function test_adding_the_same_key_replaces_the_group(): void {
		$store = new LocalStore();
		$store->add( array( 'key' => 'group_a', 'title' => 'First' ) );
		$store->add( array( 'key' => 'group_a', 'title' => 'Second' ) );

		$this->assertCount( 1, $store->all() );
		$this->assertSame( 'Second', $store->all()[0]->title() );
	}
}
