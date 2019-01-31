<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YWCPS_Ajax_Category' ) ){

    class YWCPS_Ajax_Category{


        public static function output( $option ){

            $placeholder    =   isset( $option['placeholder'] ) ? $option['placeholder'] : '';
            $multiple       =   isset( $option['multiple'] ) ?  $option['multiple'] : 'false';

            $category_ids =  get_option( $option['id']   ) ;
            if( !is_array( $category_ids ) ){
                $category_ids = explode(',', $category_ids );
            }

            $json_ids   =   array();

            foreach( $category_ids as $category_id ){

                $cat_name   =   get_term_by( 'slug',  $category_id , 'product_cat' );

                if( !empty( $cat_name ) )
                   $json_ids[ $category_id ] = '#'.$cat_name->term_id.'-'.$cat_name->name;
            }

            $args = array(
                'id' => $option['id'],
                'class' => 'wc-product-search',
                'name' => $option['id'],
                'data-action' => 'yith_json_search_product_categories',
                'data-multiple' => true,
                'data-placeholder' => $placeholder,
                'data-selected' => $json_ids,
                'value' =>  implode( ',',array_keys( $json_ids ) )
            );

        ?>

            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
                   <?php yit_add_select2_fields( $args );?>
                </td>
            </tr>
<?php
        }
    }
}