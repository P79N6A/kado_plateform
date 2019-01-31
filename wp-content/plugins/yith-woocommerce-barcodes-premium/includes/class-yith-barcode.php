<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_Barcode' ) ) {
	
	/**
	 *
	 * @class   YITH_Barcode
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_Barcode {
		
		/** Define constants for post meta key */
		const YITH_YWBC_META_KEY_BARCODE_PROTOCOL = '_ywbc_barcode_protocol';
		const YITH_YWBC_META_KEY_BARCODE_VALUE = '_ywbc_barcode_value';
		const YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE = '_ywbc_barcode_display_value';
		const YITH_YWBC_META_KEY_BARCODE_IMAGE = '_ywbc_barcode_image';
		const YITH_YWBC_META_KEY_BARCODE_FILENAME = '_ywbc_barcode_filename';
		
		/**
		 * @var int the object(Order or Product) id related to the current barcode
		 */
		public $object_id;
		
		/**
		 * @var string barcode protocol
		 */
		private $protocol;
		
		/**
		 * @var string barcode value
		 */
		private $value;
		
		/**
		 * @var string the value being displayed
		 */
		private $display_value;
		
		
		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param int $object_id
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function __construct( $object_id = 0 ) {
			
			if ( $object_id ) {
				$this->object_id = $object_id;
				
				$this->load_by_object_type ();
				
			}
		}
		
		/**
		 * Load barcode attributes based on the object type, added after WC 3.0
		 */
		private function load_by_object_type() {
			if ( ( $object = wc_get_product ( $this->object_id ) ) ||
			     ( $object = wc_get_order ( $this->object_id ) )
			) {
				$this->load_wc_object ( $object );
			} else {
				$this->load_cpt_object ();
			}
		}
		
		/**
		 * Load barcode attributes for custom post type objects
		 *
		 */
		private function load_cpt_object() {
			$this->protocol       = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL, true );
			$this->value          = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_VALUE, true );
			$this->display_value  = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE, true );
			$this->image          = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_IMAGE, true );
			$this->image_filename = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_FILENAME, true );
		}
		
		/**
		 * Load barcode attributes for WC3.0+ objects
		 *
		 * @param WC_Order|WC_Product $object
		 */
		private function load_wc_object( $object ) {
			$this->protocol       = yit_get_prop ( $object, self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL, true );
			$this->value          = yit_get_prop ( $object, self::YITH_YWBC_META_KEY_BARCODE_VALUE, true );
			$this->display_value  = yit_get_prop ( $object, self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE, true );
			$this->image          = yit_get_prop ( $object, self::YITH_YWBC_META_KEY_BARCODE_IMAGE, true );
			$this->image_filename = yit_get_prop ( $object, self::YITH_YWBC_META_KEY_BARCODE_FILENAME, true );
		}
		
		/**
		 * Retrieve the barcode by id
		 *
		 * @param int $id
		 *
		 * @return YITH_Barcode
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public static function get( $id ) {
			return new YITH_Barcode( $id );
		}
		
		/**
		 * Retrieve current formatted value
		 * @return mixed|string
		 */
		public function get_display_value() {
			return $this->display_value;
		}
		
		/**
		 * Retrieve current formatted value
		 * @return mixed|string
		 */
		public function get_protocol() {
			return $this->protocol;
		}
		
		/**
		 * save the barcode instance
		 *
		 */
		public function save() {
			
			if ( $this->object_id ) {
				
				if ( ( $object = wc_get_product ( $this->object_id ) ) ||
				     ( $object = wc_get_order ( $this->object_id ) )
				) {
					$this->save_wc_object ( $object );
				} else {
					$this->save_cpt_object ();
				}
			}
		}
		
		/**
		 * Save barcode attributes for custom post types objects
		 */
		private function save_cpt_object() {
			if ( $this->object_id ) {
				
				update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL, $this->protocol );
				update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_VALUE, $this->value );
				update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE, $this->display_value );
				update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_IMAGE, $this->image );
				update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_FILENAME, $this->image_filename );
			}
		}
		
		/**
		 * Save barcode attributes for custom post types objects
		 *
		 * @param WC_Order|WC_Product $object
		 */
		private function save_wc_object( $object ) {
			
			yit_save_prop ( $object,
				array(
					self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL      => $this->protocol,
					self::YITH_YWBC_META_KEY_BARCODE_VALUE         => $this->value,
					self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE => $this->display_value,
					self::YITH_YWBC_META_KEY_BARCODE_IMAGE         => $this->image,
					self::YITH_YWBC_META_KEY_BARCODE_FILENAME      => $this->image_filename
				),
				false,
				true );
		}
		
		/**
		 * Generate a barcode image
		 *
		 * @param string $protocol
		 * @param string $value
		 * @param string $path
		 */
		public function generate( $protocol, $value, $path = '' ) {
			
			$this->protocol       = $protocol;
			$this->value          = $value;
			$this->image_filename = $path;
			
			if ( 'qrcode' == strtolower ( $this->protocol ) ) {
				$this->create_qrcode_image ();
			} else {
				if ( ( $is_ean8 = strtolower ( $this->protocol ) == 'ean8' ) ||
				     ( strtolower ( $this->protocol ) == 'ean13' )
				) {
					$len         = $is_ean8 ? 7 : 12;
					$this->value = substr ( $this->value, 0, $len );
				}
				
				$this->create_barcode_image ();
			}
		}
		
		/**
		 * Retrieve if the barcode exists for the current object
		 * @return bool
		 */
		public function exists() {
			
			return $this->image_filename || $this->image;
		}
		
		public static function get_protocols() {
			$defaults = array(
				'EAN13'   => 'EAN-13',
				'EAN8'    => 'EAN-8',
				'STD25'   => 'STD 25',
				'INT25'   => 'INT 25',
				'CODE39'  => 'CODE 39',
				'code93'  => 'CODE 93',
				'code128' => 'CODE 128',
				'Codabar' => 'Codabar',
				'QRcode'  => 'QR code',
			);
			
			return $defaults;
		}
		
		/**
		 * Check if the value is in a valid format and fix it if possible
		 *
		 * @param string $protocol
		 * @param string $value
		 *
		 * @return null|string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		private function formatted_value( $protocol, $value ) {
			/*
			 * //  return a value obtained appending [sum of single order_id digit] and the order_id
						return array_sum( str_split( $order->id ) ) . $order->id;
			 */
			$formatted_value = $value;
			
			switch ( strtolower ( $protocol ) ) {
				case 'ean8' :
					$formatted_value = sprintf ( '%07s', $value );
					break;
				case 'ean13' :
					$formatted_value = apply_filters('yith_ywbc_ean13_formatted_value',sprintf ( '%012s', $value ));
					break;
			}
			
			return $formatted_value;
		}
		
		/**
		 * Create a QR code image
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 *
		 */
		private function create_qrcode_image() {
			
			$formatted_value = $this->formatted_value ( $this->protocol, $this->value );
			$formatted_value = apply_filters('yith_ywbc_formatted_value',$formatted_value, $this->protocol, $this->value);
			$image_filename = apply_filters('yith_ywbc_image_filename',$this->image_filename);
			$size = apply_filters('yith_ywbc_image_size',3);
			$margin = apply_filters('yith_wcbc_image_margin',4);
			
			if ( $this->image_filename ) {				
				QRcode::png ( $formatted_value, $image_filename, QR_ECLEVEL_L, $size, $margin, true );

			} else {
				ob_start ();
				if( apply_filters('yith_ywcb_execute_default_qrcode_generation_process',true) ){
					QRcode::png ( $formatted_value );
				}				
				do_action('yith_ywbc_qrcode_generation',$this->value);

				$image_data = ob_get_contents();

				ob_end_clean ();

				$this->image = base64_encode ( $image_data );

			}

			$this->display_value = $formatted_value;
		}
		
		/**
		 * Create a barcode image
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 *
		 */
		private function create_barcode_image() {
			
			$formatted_value = $this->formatted_value ( $this->protocol, $this->value );

			// Settings for barcode
			$x      = 100;  // barcode center
			$y      = 25;   //15;  // barcode center
			$height = 50;   // barcode height in 1D ; module size in 2D
			$width  = 2;    // barcode height in 1D ; not use in 2D
			$angle  = 0;    // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
			$type   = $this->protocol;
			
			// ALLOCATE GD RESOURCE
			$im    = imagecreatetruecolor ( 200, 50 );
			$black = ImageColorAllocate ( $im, 0x00, 0x00, 0x00 );
			$white = ImageColorAllocate ( $im, 0xff, 0xff, 0xff );
			imagefilledrectangle ( $im, 0, 0, 200, 50, $white );

			// Create barcode

			$data = Barcode::gd ( $im, $black, $x, $y, $angle, $type, array(
				'code' => $formatted_value,
				'crc'  => false
			), $width, $height );

			if ( $this->image_filename ) {
				$this->image = null;
				imagepng ( $im, $this->image_filename );
			} else {
				ob_start ();
				imagepng ( $im );
				$image_data = ob_get_contents ();
				ob_end_clean ();
				
				$this->image = base64_encode ( $image_data );
			}
            $this->display_value = apply_filters( 'yith_barcode_display_value', $data['hri'], $formatted_value);
		}
	}
}