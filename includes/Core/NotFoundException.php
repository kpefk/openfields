<?php
/**
 * Container not-found exception.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Thrown when a requested service is not registered in the container.
 *
 * Shaped after PSR-11's NotFoundExceptionInterface without requiring the
 * package as a dependency.
 */
final class NotFoundException extends \RuntimeException {

}
