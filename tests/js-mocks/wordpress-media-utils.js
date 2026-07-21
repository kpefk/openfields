/**
 * Jest mock for @wordpress/media-utils.
 *
 * The real package touches browser-only globals (wp.media) on load, which fails
 * under jsdom. Field-definition tests only need the module to import cleanly.
 */

module.exports = {
	MediaUpload: () => null,
};
