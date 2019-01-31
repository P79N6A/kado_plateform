<?php

class WC_pdf_admin_funtions {

    public function __construct() {

    	// Get PDF Invoice Options
    	$woocommerce_pdf_invoice_options = get_option('woocommerce_pdf_invoice_settings');
    	$this->id 	 = 'woocommerce_pdf_invoice';
    	$this->debug = false;

    	if( isset( $woocommerce_pdf_invoice_options["pdf_debug"] ) && $woocommerce_pdf_invoice_options["pdf_debug"] == "true" ) {
        	$this->debug = true;
        }

		// Add Invoice Number column to orders page in admin
		add_action( 'admin_init' , array( $this, 'pdf_manage_edit_shop_order_columns' ), 10, 2 );

		// Add Invoice Number to column
		add_action( 'manage_shop_order_posts_custom_column' , array( $this, 'invoice_number_admin_init') , 2 );

    	// Update Invoice Meta, only available if debugging is on
		if ( $this->debug == true ) {
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_update_invoice_meta' ) );
    		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_update_invoice_meta' ), 10, 3 );
		}

		// Create Invoice option
    	add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_create_invoice' ) );
    	add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_create_invoice' ), 10, 3 );


    }

	/**
	 * Add Invoice Number column to orders page in admin
	 */
	function pdf_manage_edit_shop_order_columns( $columns ) {
		add_filter( 'manage_edit-shop_order_columns', 'invoice_column_admin_init' );
	}

	/**
	 * Add invoice number to invoice column
	 */
	function invoice_number_admin_init( $column ) {
		global $post, $woocommerce, $the_order;

		if ( $column == 'pdf_invoice_num' ) {

			if ( get_post_meta( $post->ID, '_invoice_number_display', TRUE ) ) {

				$invoice_number = get_post_meta( $post->ID, '_invoice_number_display', TRUE );
				$invoice_date 	= get_post_meta( $post->ID, '_invoice_date', TRUE );
				$output 		=  '<a href="' . $_SERVER['REQUEST_URI'] . '&pdfid=' . $post->ID .'">' . $invoice_number . '<br />' . $invoice_date . '</a>';

				/**
				 * Filter the output
				 * $output: default HTML
				 * $invoice_number: stored '_invoice_number_display' from order meta
				 * $invoice_date: stored '_invoice_date' from order meta
				 * $post->ID: $order_id
				 */
				echo apply_filters( 'pdf_invoice_number_order_screen_output', $output, $invoice_number, $invoice_date, $post->ID );

			}

		}

	}

    public function bulk_update_invoice_meta( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_update_invoice_meta'] = __( 'Update PDF Meta', 'woocommerce-pdf-invoice' );

    	return $actions;

    }

    public function bulk_create_invoice( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_create_invoice'] = __( 'Create and Email Invoice', 'woocommerce-pdf-invoice' );

    	return $actions;

    }

	public function handle_update_invoice_meta( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_update_invoice_meta' && $ids != NULL ) {

			require_once( 'class-pdf-send-pdf-class.php' );
			require_once( 'class-pdf-functions-class.php' );

			// Get PDF Invoice Options
			$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

			$ids 	 = array_map( 'absint', $ids );

			foreach ( $ids as $id ) {

				$order 	 = new WC_Order( $id );

				$old_pdf_invoice_meta_items		 = get_post_meta( $id, '_invoice_meta', TRUE );
				$ordernote 						 = '';

				$new_invoice_meta = array( 
					'invoice_created' 			=> isset( $old_pdf_invoice_meta_items['invoice_created'] ) ? $old_pdf_invoice_meta_items['invoice_created'] : '',
					'invoice_date' 				=> WC_pdf_admin_funtions::handle_pdf_date( $id, $woocommerce_pdf_invoice_options['pdf_date'], isset( $old_pdf_invoice_meta_items['invoice_date'] ) ? $old_pdf_invoice_meta_items['invoice_date'] : '' ),
					'invoice_number' 			=> isset( $old_pdf_invoice_meta_items['invoice_number'] ) ? $old_pdf_invoice_meta_items['invoice_number'] : '',
					'invoice_number_display' 	=> WC_pdf_functions::create_display_invoice_number( $id ),
					'pdf_company_name' 			=> $woocommerce_pdf_invoice_options['pdf_company_name'],
					'pdf_company_information' 	=> nl2br( $woocommerce_pdf_invoice_options['pdf_company_details'] ),
					'pdf_registered_name' 		=> $woocommerce_pdf_invoice_options['pdf_registered_name'],
					'pdf_registered_office' 	=> $woocommerce_pdf_invoice_options['pdf_registered_address'],
					'pdf_company_number' 		=> $woocommerce_pdf_invoice_options['pdf_company_number'],
					'pdf_tax_number' 			=> $woocommerce_pdf_invoice_options['pdf_tax_number'],
				);

				// Only update if the invoice meta has changed.
				if( md5( json_encode($old_pdf_invoice_meta_items) ) !== md5( json_encode($new_invoice_meta) ) ) {

					// Update the invoice_meta
					update_post_meta( $id, '_invoice_meta', $new_invoice_meta );

					// Update the individual invoice meta
					foreach( $new_invoice_meta as $key => $value ) {
						update_post_meta( $id, '_'.$key, $value );
					}

					// Add an order note with the original infomation
					if( isset( $old_pdf_invoice_meta_items ) && is_array( $old_pdf_invoice_meta_items ) ) {

						foreach( $old_pdf_invoice_meta_items as $key => $value ) {
							$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
						}

						// Add order note
						$order->add_order_note( __("Invoice information changed. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

					} // if( isset( $old_pdf_invoice_meta_items ) && is_array( $old_pdf_invoice_meta_items ) )

				} // if( md5( json_encode($old_pdf_invoice_meta_items) ) !== md5( json_encode($new_invoice_meta) ) )

			} // foreach ( $ids as $id ) {

		}

		return esc_url_raw( $redirect_to );

	}

	public function handle_create_invoice( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_create_invoice' ) {

			require_once( 'class-pdf-send-pdf-class.php' );

			$ids 	 = array_map( 'absint', $ids );

			foreach ( $ids as $id ) {

				if ( get_post_meta( $id, '_invoice_number', TRUE ) == '' ) {
					// Crreate the invoice
                    WC_pdf_functions::woocommerce_completed_order_create_invoice( $id );
                    
                    // Send the 'Order Complete' email again, complete with PDF invoice!
                    // to stop the email being sent use the filter
                    // add_filter( 'pdf_invoice_bulk_send_invoice', 'remove_pdf_invoice_bulk_send_invoice' );
                    // function remove_pdf_invoice_bulk_send_invoice() {
                    // 	return false;
                    // }
                    $send_email = apply_filters( 'pdf_invoice_bulk_send_invoice', true );
                    if( $send_email ) {
						do_action( 'woocommerce_order_status_completed' , $id );
					}
                }

			}

		}

		return esc_url_raw( $redirect_to );

	}

	public static function handle_pdf_date( $order_id, $usedate, $invoice_date ) {
		global $woocommerce;
		
		$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
		$date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];
		$date 		 = NULL;  

		require_once( 'class-pdf-send-pdf-class.php' );
		require_once( 'class-pdf-functions-class.php' );

		// Force a $date_format if one is not set
		if ( !isset( $date_format ) || $date_format == '' ) {
			$date_format = "j F, Y";
		}

		$order 	 = new WC_Order( $order_id );

		if ( $usedate == 'completed' ) {
			$order_status	= is_callable( array( $order, 'get_status' ) ) ? $order->get_status() : $order->order_status;
			if( $order_status == 'completed' ) {
				$date = WC_send_pdf::get_completed_date( $order_id );
			}

		} elseif ( $usedate == 'order' ) {
			$date = is_callable( array( $order, 'get_date_created' ) ) ? $order->get_date_created() : $order->order_date;
		}
		
		if ( $date ) {

			// Return a date in the format that matches the PDF Ivoice settings.
			return WC_pdf_admin_funtions::format_pdf_date( $date) ;

		} else {
			// No changes to the date are being made or the order status is not completed but the settings say use the completed date
			// return the date already being used on the invoice.
			return $invoice_date;
		}

	}

	public static function format_pdf_date( $date = NULL ) {
		global $woocommerce;

		if( $date ) {

			$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
			$date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];

			// Make sure the date is formated correctly
			$date_check = DateTime::createFromFormat( get_option( 'date_format' ), $date );

			if( $date_check ) {
				$date = $date_check->format( $date_format );
			}

			if( strtotime( $date ) ) {
				$date = date( $date_format, strtotime( $date ) );
			}

		}

		// Return a date in the format that matches the PDF Ivoice settings.
		return $date;

	}

}

$GLOBALS['WC_pdf_admin_funtions'] = new WC_pdf_admin_funtions();