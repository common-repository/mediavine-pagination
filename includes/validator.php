<?php
if ( ! class_exists( 'Validator' ) ) {
	class Validator {
		public static function sanitize( $input ) {
			return sanitize_text_field( esc_html( $input ) );
		}

		public static function hex_color( $hex ) {
			if ( preg_match( '/^#?(?:[A-Fa-f0-9]{3}){1,2}$/', $hex ) ) {
				if ( '#' === $hex[ 0 ] ) {
					$hex = substr( $hex, 1 );
				}

				return self::sanitize( '#' . $hex );
			}

			return FALSE;
		}

		public static function integer( $input ) {
			return intval( $input );
		}

		public static function validate( $input, $type ) {
			switch ( $type ) {
				case 'int':
					return self::integer( $input );
				case 'color':
					return self::hex_color( $input );
			}

			return self::sanitize( $input );
		}
	}
}
?>
