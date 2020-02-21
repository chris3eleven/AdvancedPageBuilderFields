<?php
/**
 * Class APBF_Widget_Field_APBF_MultiSelect
 */
class APBF_Widget_Field_APBF_MultiSelect extends SiteOrigin_Widget_Field_Base {
	/**
	 * The post type selected by default
	 */
	protected $post_type;

	/**
	 * Boolean to set whether the post types filter should be displayed, defaults to true
	 */
	protected $filter_post_types;

	/**
	 * Boolean to set whether the taxonomies filter should be displayed, defaults to true
	 */
	protected $filter_taxonomies;

	/**
	 * Boolean to set whether users should be able to search the post types, defaults to true
	 */
	protected $search_enabled;

	/**
	 * An array of allowed post types
	 */
	protected $post_types;

	/**
	 * Fetches the post types displayed in the post types filter
	 */
	protected function apbf_get_post_types() {
		// post types to be excluded
		$exclude = array();
		
		// exclude ACF post types
		$exclude[] = 'acf-field';
		$exclude[] = 'acf-field-group';

		// the enabled post types, if specified by the $post_types property
		$enabled_post_types = isset( $this->post_types ) && is_array( $this->post_types ) ? $this->post_types : false;

		// get all the post types
		$post_types = get_post_types( array(), 'objects' );
		
		// variable containing the post types to appear in the post types filter
		$filter_post_types = array();

		// iterate through all the post types, adding those that are enabled to the $filter_post_types array
		foreach( $post_types as $i => $object ) {
			// don't include post types in the $exclude array
			if( in_array( $i, $exclude ) ) continue;
			
			// don't include if it's a private built in post type
			if( $object->_builtin && !$object->public ) continue;
			
			// don't include if not in the $enabled_post_types array, unless it has not been set
			if ( $enabled_post_types == false || in_array( $i, $enabled_post_types ) ) $filter_post_types[ $i ] = $object->label;
		}

		return $filter_post_types;
	}

	/**
	 * Fetches the taxonomies + terms displayed in the taxonomies filter
	 */
	protected function apbf_get_taxonomy_terms() {
		// the taxonomies to be exluded
		$exclude = array();
		$exclude[] = 'post_format';

		// get all the taxonomies
		$objects = get_taxonomies( array(), 'objects' );
		$taxonomies = array();

		foreach( $objects as $i => $object ) {
			// don't include if it's in the $exclude array
			if ( in_array( $i, $exclude ) ) continue;

			// don't include if it's a private built in type
			if ( $object->_builtin && !$object->public ) continue;

			// get taxonomy terms
			$terms = get_terms( array( 'taxonomy' => $i ) );

			// if there are terms for this taxonomy...
			if ( !empty( $terms ) ) {
				// add taxonomy
				$taxonomies[ $i ] = array(
					'name' => $object->label,
					'slug' => $i,
					'terms' => array()
				);

				// add taxonomy terms
				foreach( $terms as $term ) {
					$taxonomies[ $i ][ 'terms' ][] = array(
						'slug' => $term->slug,
						'name' => $term->name,
					);
				}
			}
		}

		return $taxonomies;
	}

	protected function render_field( $value, $instance ) {
		$post_type = $this->product_type ? $this->product_type : 'post';

		if ( is_array( $this->taxonomies ) ) {
			if ( !in_array( $post_type, $this->taxonomies ) ) $post_type = $this->taxonomies[ 0 ];
		}

		$filter_post_types = isset( $this->filter_post_types ) ? $this->filter_post_types : true;
		$filter_taxonomies = isset( $this->filter_taxonomies ) ? $this->filter_taxonomies : true;
		$search_enabled = isset( $this->search_enabled ) ? $this->search_enabled : true;
		?>
		<div class="apbf-multiselect">
			<?php if ( $filter_post_types || $filter_taxonomies || $search_enabled ) { ?>
				<div class="apbf-multiselect__row apbf-multiselect__row-filter">
					<?php if ( $search_enabled ) { ?>
						<div class="apbf-multiselect__col">
							<input type="text" class="apbf-multiselect__search" placeholder="Search...">
						</div>
					<?php } ?>
					<?php if ( $filter_post_types || $filter_taxonomies ) { ?>
						<div class="apbf-multiselect__col">
							<div class="apbf-multiselect__filters">
								<?php
								if ( $filter_post_types ) {
									$post_types = $this->apbf_get_post_types();
									?>
									<select>
										<?php foreach( $post_types as $key => $name ) { ?>
											<option value="post_type:<?php echo $key; ?>" <?php selected( $key, $post_type ); ?>><?php echo $name; ?></option>
										<?php } ?>
									</select>
								<?php }
								if ( $filter_taxonomies ) {
									$taxonomy_terms = $this->apbf_get_taxonomy_terms();
									?>
									<select>
										<option value="term:all">All</option>
										<?php foreach( $taxonomy_terms as $taxonomy ) { ?>
											<optgroup label="<?php echo $taxonomy[ 'name' ]; ?>">
												<?php foreach( $taxonomy[ 'terms' ] as $taxonomy_term ) { ?>
													<option value="term:<?php echo $taxonomy[ 'slug' ]; ?>:<?php echo $taxonomy_term[ 'slug' ]; ?>"><?php echo $taxonomy_term[ 'name' ]; ?></option>
												<?php } ?>
											</optgroup>
										<?php } ?>
									</select>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="apbf-multiselect__row">
				<div class="apbf-multiselect__col">
					<ul class="apbf-multiselect__posts apbf-multiselect__posts-posts" data-post_type="<?php echo $post_type; ?>">
					</ul>
				</div>
				<div class="apbf-multiselect__col">
					<ul class="apbf-multiselect__posts apbf-multiselect__posts-selected ui-sortable"></ul>
					<?php $value = !$value ? '[]' : json_encode( $value ); ?>
					<input type="hidden" class="apbf-multiselect__value" name="<?php echo esc_attr( $this->element_name ) ?>" id="<?php echo esc_attr( $this->element_id ) ?>" value="<?php echo $value; ?>">
				</div>
			</div>
		</div>
		<?php
	}

	protected function sanitize_field_input( $value, $instance ) {
		$value = is_array( $value ) ? $value : json_decode( $value );
		return $value;
	}

	public function enqueue_scripts() {
        wp_enqueue_script( 'apbf-multiselect', plugin_dir_url( __FILE__ ) . 'assets/js/apbf-multiselect.min.js', array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/apbf-multiselect.min.js' ) );
		wp_enqueue_style( 'apbf-multiselect', plugin_dir_url( __FILE__ ) . 'assets/css/apbf-multiselect.min.css', array(), '1.0.0' );
		wp_localize_script( 'apbf-multiselect', 'ajax', array(
			'url' => admin_url( 'admin-ajax.php' ),
		) );
    }
}
