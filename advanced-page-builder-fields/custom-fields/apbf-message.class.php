<?php
/**
 * Class APBF_Widget_Field_Message
 */
class APBF_Widget_Field_APBF_Message extends SiteOrigin_Widget_Field_Base {

	protected function render_field( $value, $instance ) {
		// Empty. This widget only displays a title and description
	}

	protected function sanitize_field_input( $value, $instance ) {
		// Empty, there is no input to sanitize
	}

	// don't apply default widget classes
	protected function get_description_classes() {
		return array();
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'apbf-message', plugin_dir_url( __FILE__ ) . 'assets/css/apbf-message.min.css', array(), '1.0.0' );
    }
}
