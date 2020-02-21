<?php
/**
 * Class APBF_Widget_Field_APBF_Select
 */
class APBF_Widget_Field_APBF_Select extends SiteOrigin_Widget_Field_Base {
	/**
	 * The args for the query that populates the select
	 *
	 * @access protected
	 * @var array
	 */
	protected $query;

	protected function render_field( $value, $instance ) { ?>
		<select name="<?php echo esc_attr( $this->element_name ) ?>" id="<?php echo esc_attr( $this->element_id ) ?>"
		        class="apbf-dataselect siteorigin-widget-input<?php if ( ! empty( $this->input_css_classes ) ) echo ' ' . implode( ' ', $this->input_css_classes ) ?>"
			<?php if ( isset( $this->query[ 'post_type' ] ) ) echo 'data-post_type="' . $this->query[ 'post_type' ] . '"' ?>
			<?php if ( isset( $this->query[ 'post_status' ] ) ) echo 'data-post_status="' . $this->query[ 'post_status' ] . '"' ?>
			<?php if ( isset( $this->query[ 'orderby' ] ) ) echo 'data-orderby="' . $this->query[ 'orderby' ] . '"' ?>
			<?php if ( isset( $this->query[ 'order' ] ) ) echo 'data-order="' . $this->query[ 'order' ] . '"' ?>>
			<?php if ( $value != '' ) { ?>
				<option value="<?php echo $value; ?>"><?php echo get_the_title( $value ); ?></option>
			<?php } ?>
		</select>
		<?php
	}

	protected function sanitize_field_input( $value, $instance ) {
		return $value;
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'assets/lib/select2/js/select2.full.min.js', array( 'jquery' ), '4.0.13' );
		wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'assets/lib/select2/css/select2.min.css', array(), '4.0.13' );
        wp_enqueue_script( 'apbf-select', plugin_dir_url( __FILE__ ) . 'assets/js/apbf-select.min.js', array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/apbf-select.min.js' ) );
		wp_enqueue_style( 'apbf-select', plugin_dir_url( __FILE__ ) . 'assets/css/apbf-select.min.css', array(), '1.0.0' );
		wp_localize_script( 'apbf-select', 'ajax', array(
			'url' => admin_url( 'admin-ajax.php' )
		) );
    }
}
