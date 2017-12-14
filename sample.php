<?php
add_filter( 'flex_metabox', 'my_metabox' );
function my_metabox( $metaboxes ) {
	$prefix      = 'flex_';
	$metaboxes[] = array(
		'id'       => $prefix . 'settings',
		'title'    => esc_attr__( 'Flex Metabox', 'textdomain' ),
		'screens'  => array( 'post' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			array(
				'id'          => $prefix . 'text_field',
				'title'       => esc_attr__( 'Text', 'textdomain' ),
				'type'        => 'text',
				'default'     => 'default content',
				'description' => esc_attr__( 'Enter description here', 'textdomain' ),
			),

			array(
				'id'          => $prefix . 'number_field',
				'title'       => esc_attr__( 'Number', 'textdomain' ),
				'type'        => 'number',
				'default'     => '0',
				'description' => esc_attr__( 'Enter description here', 'textdomain' ),
			),

			array(
				'id'          => $prefix . 'text_area_field',
				'title'       => esc_attr__( 'Textarea', 'textdomain' ),
				'type'        => 'textarea',
				'description' => esc_attr__( 'Enter description here', 'textdomain' ),
			),

			array(
				'id'          => $prefix . 'select_field',
				'title'       => esc_attr__( 'Select', 'textdomain' ),
				'type'        => 'select',
				'options'     => array(
					'key_a' => esc_attr__( 'option A', 'textdomain' ),
					'key_b' => esc_attr__( 'option B', 'textdomain' ),
					'key_c' => esc_attr__( 'option C', 'textdomain' ),
					'key_d' => esc_attr__( 'option D', 'textdomain' ),
				),
				'description' => esc_attr__( 'Enter description here', 'textdomain' ),
			),

			array(
				'id'          => $prefix . 'wpeditor_field',
				'title'       => esc_attr__( 'WP Editor', 'textdomain' ),
				'type'        => 'wpeditor',
				'description' => esc_attr__( 'Enter description here', 'textdomain' ),
			),

			array(
				'id'        => $prefix . 'group',
				'title'     => esc_attr__( 'Repeater Group', 'textdomain' ),
				'sub-title' => esc_attr__( 'Sub Title', 'textdomain' ),
				'type'      => 'repeater',
				'fields'    => array(
					array(
						'id'    => 'title',
						'title' => esc_attr__( 'Title', 'textdomain' ),
						'type'  => 'text',
					),
					array(
						'id'    => 'content',
						'title' => esc_attr__( 'Content', 'textdomain' ),
						'type'  => 'textarea',
					),
				),
			)
		)
	);

	return $metaboxes;
}