<?php
/**
 * Class to create a custom layout control
 */
class Storefront_Pricing_Tables_Layout_Control extends WP_Customize_Control {

	public $type = 'radio';

	/**
	* Render the content on the theme customizer page
	*/
	public function render_content() {
		?>
		<div style="overflow: hidden; zoom: 1;">
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

			<label style="width: 30.75%; float: left; margin-right: 3.8%; text-align: center; margin-bottom: 1.618em;">
				<img src="<?php echo plugins_url( '../assets/img/admin/left.png', __FILE__ ); ?>" alt="Left" style="display: block; width: 100%; margin-bottom: .618em" />
				<input type="radio" value="left" style="margin: 5px 0 0 0;"name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); checked( $this->value(), 'left' ); ?> />
				<br/>
			</label>
			<label style="width: 30.75%; float: left; text-align: center; margin-right: 3.8%; margin-bottom: 1.618em;">
				<img src="<?php echo plugins_url( '../assets/img/admin/center.png', __FILE__ ); ?>" alt="Center" style="display: block; width: 100%; margin-bottom: .618em" />
				<input type="radio" value="center" style="margin: 5px 0 0 0;"name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); checked( $this->value(), 'center' ); ?> />
				<br/>
			</label>
			<label style="width: 30.75%; float: right; text-align: center; margin-bottom: 1.618em;">
				<img src="<?php echo plugins_url( '../assets/img/admin/right.png', __FILE__ ); ?>" alt="Right" style="display: block; width: 100%; margin-bottom: .618em" />
				<input type="radio" value="right" style="margin: 5px 0 0 0;"name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); checked( $this->value(), 'right' ); ?> />
				<br/>
			</label>
		</div>
		<?php
	}
}
