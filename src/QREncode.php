<?php

namespace victorlazov\qrcode;

class QREncode {

	public $casesensitive = true;
	public $eightbit = false;

	public $version = 0;
	public $size = 3;
	public $margin = 4;

	public $structured = 0; // not supported yet

	public $level = QR_ECLEVEL_L;
	public $hint = QR_MODE_8;


	public static function factory( $level = QR_ECLEVEL_L, $size = 3, $margin = 4 ) {
		$enc         = new QREncode();
		$enc->size   = $size;
		$enc->margin = $margin;

		switch ( $level . '' ) {
			case '0':
			case '1':
			case '2':
			case '3':
				$enc->level = $level;
				break;
			case 'l':
			case 'L':
				$enc->level = QR_ECLEVEL_L;
				break;
			case 'm':
			case 'M':
				$enc->level = QR_ECLEVEL_M;
				break;
			case 'q':
			case 'Q':
				$enc->level = QR_ECLEVEL_Q;
				break;
			case 'h':
			case 'H':
				$enc->level = QR_ECLEVEL_H;
				break;
		}

		return $enc;
	}


	public function encodeRAW( $intext, $outfile = false ) {
		$code = new QRCode();

		if ( $this->eightbit ) {
			$code->encodeString8bit( $intext, $this->version, $this->level );
		} else {
			$code->encodeString( $intext, $this->version, $this->level, $this->hint, $this->casesensitive );
		}

		return $code->data;
	}


	public function encode( $intext, $outfile = false ) {
		$code = new QRCode();

		if ( $this->eightbit ) {
			$code->encodeString8bit( $intext, $this->version, $this->level );
		} else {
			$code->encodeString( $intext, $this->version, $this->level, $this->hint, $this->casesensitive );
		}

		QRTools::markTime( 'after_encode' );

		if ( $outfile !== false ) {
			file_put_contents( $outfile, join( "\n", QRTools::binarize( $code->data ) ) );
		} else {
			return QRTools::binarize( $code->data );
		}
	}

	public function encodePNG( $intext, $outfile = false, $saveandprint = false ) {
		try {

			ob_start();
			$tab = $this->encode( $intext );
			$err = ob_get_contents();
			ob_end_clean();

			if ( $err != '' ) {
				QRTools::log( $outfile, $err );
			}

			$maxSize = (int) ( QR_PNG_MAXIMUM_SIZE / ( count( $tab ) + 2 * $this->margin ) );

			QRImage::png( $tab, $outfile, min( max( 1, $this->size ), $maxSize ), $this->margin, $saveandprint );

		} catch ( \Exception $e ) {

			QRTools::log( $outfile, $e->getMessage() );

		}
	}
}
