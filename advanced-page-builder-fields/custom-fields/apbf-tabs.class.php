<?php

/**
 * Class APBF_Widget_Field_APBF_Tabs
 */
class APBF_Widget_Field_APBF_Tabs extends SiteOrigin_Widget_Field_Container_Base {

	protected function render_field( $value, $instance ) { ?>
		<ul class="apbf-tabs__tabs">
		<?php
		$tabCount = 0;
		// iterate through the fields rendering tabs when the pseudo-field 'apbf-tab' is discovered
		foreach( $this->fields as $field ) { 
			if ( $field[ 'type' ] == 'apbf-tab' ) { ?>
				<li class="apbf-tabs__tab<?php echo $tabCount == 0 ? ' apbf-tabs__tab--active' : ''; ?>" data-tab="<?php echo $tabCount; ?>">
					<?php echo $field[ 'label' ]; ?>
				</li>
				<?php
				$tabCount++;
			}
		} ?>
		</ul>
		<?php
		// if there are tabs, start rendering the ul containing the tab panels
		if ( $tabCount > 0 ) { ?>
		<ul class="apbf-tabs__panels">
			<li class="apbf-tabs__panel apbf-tabs__panel--active" data-tab="0">
		<?php } ?>
		<?php $this->create_and_render_sub_fields( $value, array( 'name' => $this->base_name, 'type' => 'apbf-tabs' ) ); ?>
		<?php
		// if there are tabs, end rendering the ul containing the tab panels
		if ( $tabCount > 0 ) { ?>
			</li>
		</ul>
		<?php } ?>
	<?php }

	// taken from SiteOrigin_Widget_Field_Container_Base and modified to ignore fields of type 'apbf-tab', a pseudo-field for denoting tabs, and outputting closing and opening <li> tags where they are discovered
	protected function create_and_render_sub_fields( $values, $parent_container = null, $is_template = false ) {
		$tabCount = -1;
		$this->sub_fields = array();
		if( isset( $parent_container )) {
			if( ! in_array( $parent_container, $this->parent_container, true ) ){
				$this->parent_container[] = $parent_container;
			}
		}
		/* @var $field_factory SiteOrigin_Widget_Field_Factory */
		$field_factory = SiteOrigin_Widget_Field_Factory::single();
		foreach( $this->fields as $sub_field_name => $sub_field_options ) {
			if ( strtolower( $sub_field_options[ 'type' ] ) == 'apbf-tab' ) {
				$tabCount++;
				if ( $tabCount > 0 ) {
					?>
					</li>
					<li class="apbf-tabs__panel" data-tab="<?php echo $tabCount; ?>">
					<?php
				}
			} else {
				/* @var $field SiteOrigin_Widget_Field_Base */
				$field = $field_factory->create_field(
					$sub_field_name,
					$sub_field_options,
					$this->for_widget,
					$this->parent_container,
					$is_template
				);
				$sub_value = ( ! empty( $values ) && isset( $values[$sub_field_name] ) ) ? $values[$sub_field_name] : null;
				$field->render( $sub_value, $values );
				$field_js_vars = $field->get_javascript_variables();
				if( ! empty( $field_js_vars ) ) {
					$this->javascript_variables[$sub_field_name] = $field_js_vars;
				}
				$field->enqueue_scripts();
				$this->sub_fields[$sub_field_name] = $field;
			}
		}
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'apbf-tabs', plugin_dir_url( __FILE__ ) . 'assets/js/apbf-tabs.min.js', array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/apbf-tabs.min.js' ) );
		wp_enqueue_style( 'apbf-tabs', plugin_dir_url( __FILE__ ) . 'assets/css/apbf-tabs.min.css', array(), '1.0.0' );
    }
}