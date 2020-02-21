# Advanced Page Builder Fields

Advanced Page Builder Fields is a small collection of custom fields which can be used in custom widgets developed for the WordPress page creation plugin, SiteOrigin Page Builder.

## Getting Started

Clone into your site's wp-content/plugins folder and activate the plugin through your WordPress admin



## The fields

* **APBF Select**: A [Select2](https://select2.org/) powered select box with search functionality for selecting single posts of whichever post type you specify.
* **APBF MultiSelect**: An ajax powered control with search functionality enabling you to select multiple posts of whichever post type you specify.
* **APBF Toggle**: A simple on/off toggle switch.
* **APBF Message**: A simple non-input field for displaying an informational message, for example to instruct users how to use your widget.
* **APBF Tabs**: Group related form fields into tabbed sections to neaten up your plugin interface.



## Usage

### ABPF Select

* label _(string)_ - Render a label for the field with the given value.
* query _(array)_ - Arguments used to fetch posts to be displayed.
* description _(string)_ - Render small italic text below the field to describe the field's purpose.

```
$args = array(
	'post_type' => 'post',
	'post_status' => 'publish',
	'orderby' => 'publish_date',
	'order' => 'DESC'
);

$form_options = array(
    'some_post' => array(
			'type' => 'apbf-select',
			'label' => __( 'Select a post', 'widget-form-fields-text-domain' ),
			'query' => $args,
			'description' => __( 'Choose a single post.', 'widget-form-fields-text-domain' )
		)
);
```



### APBF MultiSelect

* label _(string)_ - Render a label for the field with the given value.
* description _(string)_ - Render small italic text below the field to describe the field's purpose.
* post_types _(array, optional)_ - The post types that should be included. Defaults to all.
* default _(string, options)_ - The post type that should be displayed by default. Defaults to posts.
* filter_post_types _(boolean, optional)_ - whether the post types filter should be displayed. Defaults to true.
* filter_taxonomies _(boolean, optional)_ - whether the taxonomies filter should be displayed. Defaults to true.
* search_enabled _(boolean, optional)_ - whether the search field should be displayed. Defaults to true.

```
$form_options = array(
	'some_posts' => array(
    'type' => 'apbf-multiselect',
    'label' => __( 'Select some posts', 'widget-form-fields-text-domain' ),
    'description' => __( 'Choose multiple posts of multiple types.' ),
    'post_types' => array( 'post', 'page' ),
    'default' => 'post',
    'filter_post_types' => true,
    'filter_taxonomies' => true,
    'search_enabled' => true
	)
);
```



### APBF Toggle

* label _(string)_ - Render a label for the field with the given value.
* description _(string)_ - Render small italic text below the field to describe the field's purpose.

```
$form_options = array(
	'some_boolean' => array(
    'type' => 'apbf-toggle',
    'label' => __( 'This thing', 'widget-form-fields-text-domain' ),
    'description' => 'Turn this thing on.'
	)
);
```

### 

### APBF Message

* label _(string)_ - Render a label for the field with the given value.
* description _(string)_ - Render small italic text below the field to describe the field's purpose.

```
$form_options = array(
	'some_message' => array(
    'type' => 'apbf-message',
    'label' => __( 'This is an APBF Message field', 'widget-form-fields-text-domain' ),
    'description' => 'Any information you wish to relay.'
	)
);
```

### 

### APBF Tabs

* label _(string)_ - Render a label for the field with the given value.
* description _(string)_ - Render small italic text below the field to describe the field's purpose.

```
$form_options = array(
	'some_tabs' => array(
    'type' => 'apbf-tabs',
		'fields' => array(
			'tab1' => array(
				'type' => 'apbf-tab',
				'label' => __( 'The first tab', 'widget-form-fields-text-domain' ),
			),
			'some_text' => array(
        'type' => 'text',
        'label' => __('Some text goes here', 'widget-form-fields-text-domain'),
	    ),
	    'tab2' => array(
				'type' => 'apbf-tab',
				'label' => __( 'The second tab', 'widget-form-fields-text-domain' ),
			),
			'some_more_text' => array(
        'type' => 'text',
        'label' => __('Some text goes here', 'widget-form-fields-text-domain'),
	    )
		)
	)
);
```

_Note: Tabs are denoted via the pseudo-field type 'apbf-tab'._



## More Information

* [SiteOrigin Page Builder](https://siteorigin.com/page-builder/)
* [Creating a widget](https://siteorigin.com/docs/widgets-bundle/getting-started/creating-a-widget/) - SiteOrigin's docs on creating custom widgets
