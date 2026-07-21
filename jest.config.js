/**
 * Jest configuration — extends the @wordpress/scripts unit preset and mocks
 * @wordpress/media-utils (which needs browser-only globals on load).
 */

const defaultConfig = require( '@wordpress/scripts/config/jest-unit.config' );

module.exports = {
	...defaultConfig,
	moduleNameMapper: {
		...( defaultConfig.moduleNameMapper || {} ),
		'^@wordpress/media-utils$':
			'<rootDir>/tests/js-mocks/wordpress-media-utils.js',
	},
};
