<?php

namespace victorlazov\qrcode;

class QRCodeCore extends QRCode {
	// Encoding modes
	public const QR_MODE_NUL = - 1;
	public const QR_MODE_NUM = 0;
	public const QR_MODE_AN = 1;
	public const QR_MODE_8 = 2;
	public const QR_MODE_KANJI = 3;
	public const QR_MODE_STRUCTURE = 4;

	// Levels of error correction.
	public const QR_ECLEVEL_L = 0;
	public const QR_ECLEVEL_M = 1;
	public const QR_ECLEVEL_Q = 2;
	public const QR_ECLEVEL_H = 3;

	// Supported output formats
	public const QR_FORMAT_TEXT = 0;
	public const QR_FORMAT_PNG = 1;

	/**
	 * use cache - more disk reads but less CPU power, masks and format templates are stored there
	 */
	public const QR_CACHEABLE = false;
	/**
	 * used when QR_CACHEABLE === true
	 */
	public const QR_CACHE_DIR = false;
	/**
	 * default error logs dir
	 */
	public const QR_LOG_DIR = false;
	/**
	 * if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
	 */
	public const QR_FIND_BEST_MASK = true;
	/**
	 * if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
	 */
	public const QR_FIND_FROM_RANDOM = 2;
	/**
	 * when QR_FIND_BEST_MASK === false
	 */
	public const QR_DEFAULT_MASK = 2;
	/**
	 * maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images
	 */
	public const QR_PNG_MAXIMUM_SIZE = 1024;

	public function timeBenchmark() {
		QRTools::timeBenchmark();
	}
}
