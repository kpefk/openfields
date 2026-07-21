<?php
/**
 * Tests for the dependency-injection container.
 *
 * @package OpenFields\Tests
 */

declare( strict_types=1 );

namespace OpenFields\Tests\Unit\Core;

use OpenFields\Core\Container;
use OpenFields\Core\NotFoundException;
use OpenFields\Tests\Unit\TestCase;

/**
 * @covers \OpenFields\Core\Container
 */
final class ContainerTest extends TestCase {

	public function test_bind_returns_a_new_instance_each_time(): void {
		$container = new Container();
		$container->bind( 'service', static fn (): \stdClass => new \stdClass() );

		$this->assertNotSame( $container->get( 'service' ), $container->get( 'service' ) );
	}

	public function test_singleton_returns_the_same_instance(): void {
		$container = new Container();
		$container->singleton( 'service', static fn (): \stdClass => new \stdClass() );

		$this->assertSame( $container->get( 'service' ), $container->get( 'service' ) );
	}

	public function test_instance_stores_a_shared_object(): void {
		$container = new Container();
		$object    = new \stdClass();
		$container->instance( 'service', $object );

		$this->assertSame( $object, $container->get( 'service' ) );
	}

	public function test_has_reflects_registration(): void {
		$container = new Container();

		$this->assertFalse( $container->has( 'service' ) );

		$container->bind( 'service', static fn (): \stdClass => new \stdClass() );

		$this->assertTrue( $container->has( 'service' ) );
	}

	public function test_factory_receives_the_container(): void {
		$container = new Container();
		$container->instance( 'dependency', 'value' );
		$container->bind(
			'service',
			static fn ( Container $c ): string => (string) $c->get( 'dependency' )
		);

		$this->assertSame( 'value', $container->get( 'service' ) );
	}

	public function test_get_throws_for_unregistered_service(): void {
		$container = new Container();

		$this->expectException( NotFoundException::class );
		$container->get( 'missing' );
	}
}
