<?php
/**
 * PHP QR Code encoder
 *
 * This file contains MERGED version of PHP QR Code library.
 * It was auto-generated from full version for your convenience.
 *
 * This merged version was configured to not requre any external files,
 * with disabled cache, error loging and weker but faster mask matching.
 * If you need tune it up please use non-merged version.
 *
 * For full version, documentation, examples of use please visit:
 *
 *    http://phpqrcode.sourceforge.net/
 *    https://sourceforge.net/projects/phpqrcode/
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

namespace victorlazov\qrcode;

class QRCode {

	public $version;
	public $width;
	public $data;

	public function encodeMask( QRInput $input, $mask ) {
		if ( $input->getVersion() < 0 || $input->getVersion() > QRSpec::QRSPEC_VERSION_MAX ) {
			throw new \Exception( 'wrong version' );
		}
		if ( $input->getErrorCorrectionLevel() > QRCodeCore::QR_ECLEVEL_H ) {
			throw new \Exception( 'wrong level' );
		}

		$raw = new QRRawCode( $input );

		QRTools::markTime( 'after_raw' );

		$version = $raw->version;
		$width   = QRSpec::getWidth( $version );
		$frame   = QRSpec::newFrame( $version );

		$filler = new FrameFiller( $width, $frame );
		if ( is_null( $filler ) ) {
			return null;
		}

		// inteleaved data and ecc codes
		for ( $i = 0; $i < $raw->dataLength + $raw->eccLength; $i ++ ) {
			$code = $raw->getCode();
			$bit  = 0x80;
			for ( $j = 0; $j < 8; $j ++ ) {
				$addr = $filler->next();
				$filler->setFrameAt( $addr, 0x02 | ( ( $bit & $code ) != 0 ) );
				$bit = $bit >> 1;
			}
		}

		QRTools::markTime( 'after_filler' );

		unset( $raw );

		// remainder bits
		$j = QRSpec::getRemainder( $version );
		for ( $i = 0; $i < $j; $i ++ ) {
			$addr = $filler->next();
			$filler->setFrameAt( $addr, 0x02 );
		}

		$frame = $filler->frame;
		unset( $filler );


		// masking
		$maskObj = new QRMask();
		if ( $mask < 0 ) {

			if ( QRCodeCore::QR_FIND_BEST_MASK ) {
				$masked = $maskObj->mask( $width, $frame, $input->getErrorCorrectionLevel() );
			} else {
				$masked = $maskObj->makeMask( $width, $frame, ( intval( QRCodeCore::QR_DEFAULT_MASK ) % 8 ), $input->getErrorCorrectionLevel() );
			}
		} else {
			$masked = $maskObj->makeMask( $width, $frame, $mask, $input->getErrorCorrectionLevel() );
		}

		if ( $masked == null ) {
			return null;
		}

		QRTools::markTime( 'after_mask' );

		$this->version = $version;
		$this->width   = $width;
		$this->data    = $masked;

		return $this;
	}


	public function encodeInput( QRInput $input ) {
		return $this->encodeMask( $input, - 1 );
	}

	public function encodeString8bit( $string, $version, $level ) {
		if ( $string == null ) {
			throw new \Exception( 'empty string!' );
		}

		$input = new QRInput( $version, $level );
		if ( $input == null ) {
			return null;
		}

		$ret = $input->append( $input, QRCodeCore::QR_MODE_8, strlen( $string ), str_split( $string ) );
		if ( $ret < 0 ) {
			unset( $input );

			return null;
		}

		return $this->encodeInput( $input );
	}


	public function encodeString( $string, $version, $level, $hint, $casesensitive ) {

		if ( $hint != QRCodeCore::QR_MODE_8 && $hint != QRCodeCore::QR_MODE_KANJI ) {
			throw new \Exception( 'bad hint' );
		}

		$input = new QRInput( $version, $level );
		if ( $input == null ) {
			return null;
		}

		$ret = QRSplit::splitStringToQRinput( $string, $input, $hint, $casesensitive );
		if ( $ret < 0 ) {
			return null;
		}

		return $this->encodeInput( $input );
	}


	public static function png( string $text, $outfile = false, $level = QRCodeCore::QR_ECLEVEL_L, $size = 3, $margin = 4, $saveAndPrint = false ) {
		$enc = QREncode::factory( $level, $size, $margin );

		$enc->encodePNG( $text, $outfile, $saveAndPrint );
	}


	public static function text( $text, $outfile = false, $level = QRCodeCore::QR_ECLEVEL_L, $size = 3, $margin = 4 ) {
		$enc = QREncode::factory( $level, $size, $margin );

		return $enc->encode( $text, $outfile );
	}


	public static function raw( $text, $outfile = false, $level = QRCodeCore::QR_ECLEVEL_L, $size = 3, $margin = 4 ) {
		$enc = QREncode::factory( $level, $size, $margin );

		return $enc->encodeRAW( $text, $outfile );
	}
}


