<?php
/**
 * Class APBF_Widget_Field_APBF_Toggle
 */
class APBF_Widget_Field_APBF_Toggle extends SiteOrigin_Widget_Field_Base {

	protected function render_field( $value, $instance ) {
		?>
		<div class="apbf-toggle__container">
			<label for="<?php echo esc_attr( $this->element_id ) ?>" class="apbf-toggle">
				<input type="checkbox" name="<?php echo esc_attr( $this->element_name ) ?>" id="<?php echo esc_attr( $this->element_id ) ?>" class="apbf-toggle__checkbox" <?php checked( !empty( $value ) ) ?> />
				<div class="apbf-toggle__control">
					<div class="apbf-toggle__button"></div>
				</div>
			</label>
			<div>
				<div class="apbf-toggle__label"><?php echo esc_html( $this->label ) ?></div>
				<?php $this->render_field_description(); ?>
			</div>
		</div>
		<?php
	}

	protected function render_field_label( $value, $instance ) {
		// Empty override. This field renders it's own label in the render_field() function.
	}

	protected function render_after_field( $value, $instance ) {
		// Empty override. This field renders it's own description in the render_field() function.
	}

	protected function sanitize_field_input( $value, $instance ) {
		return ! empty( $value ) && ! ( is_string( $value ) && $value === 'false' );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'apbf-toggle', plugin_dir_url( __FILE__ ) . 'assets/css/apbf-toggle.min.css', array(), '1.0.0' );
    }
}