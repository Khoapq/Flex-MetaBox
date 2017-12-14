<?php

/**
 * Class Flex_MetaBox
 * @version 0.1.0
 * @author  khoapq
 * @created 21/11/2017
 */
class Flex_MetaBox {

	private $default = array();
	private $meta_boxes = array();

	public function __construct() {

		define( 'FMTB_URL', plugin_dir_url( __FILE__ ) );

		add_action( 'after_setup_theme', array( $this, 'init' ) );

	}

	public function admin_assets() {
		wp_enqueue_style( 'fmtb-backend', FMTB_URL . 'assets/css/backend.css' );
		wp_enqueue_script( 'jquery.repeater.min', FMTB_URL . 'assets/js/jquery.repeater.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'fmtb-backend', FMTB_URL . 'assets/js/backend.js', array(
			'jquery.repeater.min',
			'jquery-ui-sortable',
			'jquery'
		) );
	}

	public function init() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		add_action( 'before_render_repeater', array( $this, 'before_render_repeater' ) );
		add_action( 'after_render_repeater', array( $this, 'after_render_repeater' ) );
		add_action( 'before_render_repeater_field', array( $this, 'before_render_repeater_field' ) );
		add_action( 'after_render_repeater_field', array( $this, 'after_render_repeater_field' ) );
		add_action( 'before_render_field', array( $this, 'before_render_field' ) );
		add_action( 'after_render_field', array( $this, 'after_render_field' ) );


		$this->meta_boxes = wp_parse_args( apply_filters( 'flex_metaboxes', array() ) );
		if ( $this->meta_boxes ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		}

	}


	public function add_meta_boxes() {
		foreach ( $this->meta_boxes as $meta_box ) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				array( $this, 'html' ),
				$meta_box['screens'],
				$meta_box['context'],
				$meta_box['priority'],
				$meta_box['fields']
			);
		}
	}


	public function save_meta_boxes( $post_id ) {

		/* don't save if $_POST is empty */
		if ( empty( $_POST ) ) {
			return $post_id;
		}

		/* don't save during autosave */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}


		foreach ( $this->meta_boxes as $meta_box ) {
			foreach ( $meta_box['fields'] as $field ) {
				$value = $_POST[ $field['id'] ];
				update_post_meta(
					$post_id,
					$field['id'],
					$value
				);
			}
		}

	}

	public function html( $post, $metabox ) {

		$this->default = array(
			'id'          => '_flex_metabox',
			'title'       => '',
			'sub-title'   => '',
			'description' => '',
			'default'     => '',
			'type'        => 'text',
		);

		echo '<div class="metabox-wrap">';
		foreach ( $metabox['args'] as $meta_field ) {
			$meta_field = wp_parse_args( $meta_field, $this->default );
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( 'repeater' == $meta_field['type'] ) {
				do_action( 'before_render_repeater', $meta_field );
				if ( is_array( $meta_value ) ) {
					foreach ( $meta_value as $key => $value ) {
						$this->render_repeater( $value, $meta_field );
					}
				} else {
					$this->render_repeater( $meta_value, $meta_field );
				}

				do_action( 'after_render_repeater', $meta_field );
			} else {
				$this->reader_field( $meta_value, $meta_field );
			}
		}
		echo '</div>';
	}

	public function before_render_repeater( $field ) {
		echo '<div class="field-wrap type-' . $field['type'] . '">';
		echo '<div class="repeater-title"><label>' . $field['title'] . '</label></div>';
		echo '<div class="repeater-wrap">';
		echo '<div class="repeater-list" data-repeater-list="' . $field['id'] . '">';
	}

	public function after_render_repeater( $field ) {
		echo '</div><!-- end: repeater-list -->';
		echo '</div><!-- end: repeater-wrap -->';
		if ( $field['description'] ) {
			echo '<div class="field-description">' . $field['description'] . '</div>';
		}
		echo '<input class="btn-add" data-repeater-create type="button" value="Add"/>';
		echo '</div><!-- end: field-wrap -->';
	}

	public function render_repeater( $value, $meta_field ) {
		do_action( 'before_render_repeater_field', $meta_field );
		if ( is_array( $meta_field['fields'] ) ) {
			foreach ( $meta_field['fields'] as $key => $field ) {
				$field = wp_parse_args( $field, $this->default );
				if ( isset( $value[ $field['id'] ] ) ) {
					$this->reader_field( $value[ $field['id'] ], $field );
				} else {
					$this->reader_field( $value, $field );
				}
			}
		}
		do_action( 'after_render_repeater_field', $meta_field );
	}

	public function before_render_repeater_field( $field ) {
		echo '<div class="repeater-item" data-repeater-item>';
		echo '<div class="item-title"><label>' . $field['sub-title'] . '</label>';
		echo '<div class="action-buttons"><a data-repeater-delete class="btn-delete"><span class="dashicons dashicons-no-alt"></span></a><a class="btn-toggle"><span class="dashicons dashicons-arrow-down"></span></a></div>';
		echo '</div>';
	}

	public function after_render_repeater_field( $field ) {
		echo '</div><!-- end: repeater-item -->';
	}


	public function reader_field( $meta_value, $field ) {
		$value        = $meta_value ? $meta_value : $field['default'];
		$name         = $field['id'];

		do_action( 'before_render_field', $field );

		switch ( $field['type'] ) {
			case 'select':
				?>
				<select name="<?php echo $name; ?>">
					<?php foreach ( $field['options'] as $key => $text ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( $key, $value ); ?>><?php echo $text; ?></option>
					<?php endforeach; ?>
				</select>
				<?php
				break;
			case 'number':
				$max = isset( $field['max'] ) ? $field['max'] : '';
				$min  = isset( $field['min'] ) ? $field['min'] : ''; ?>
				<input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" max="<?php echo $max; ?>" min="<?php echo $min; ?>">
				<?php
				break;
			case 'textarea':
				$rows = isset( $field['rows'] ) ? $field['rows'] : 4;
				$cols = isset( $field['cols'] ) ? $field['cols'] : 50;
				?>
				<textarea name="<?php echo $name; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $value; ?></textarea>
				<?php
				break;
			case 'wpeditor':
				wp_editor( $value, uniqid( 'flex_metabox' ), array( 'textarea_name' => $name, 'textarea_rows' => 5 ) );
				break;
			default:
				echo '<input type="text" name="' . $name . '" value="' . $value . '">';
				break;
		}

		do_action( 'after_render_field', $field );

	}

	public function before_render_field( $field ) {
		echo '<div class="field-wrap type-' . $field['type'] . '">';
		echo '<div class="field-title"><label>' . $field['title'] . '</label></div>';
		echo '<div class="field-input">';
	}

	public function after_render_field( $field ) {
		if ( $field['description'] ) {
			echo '<div class="field-description">' . $field['description'] . '</div>';
		}
		echo '</div><!-- end: field-input -->';
		echo '</div><!-- end: field-wrap -->';
	}

}

new Flex_MetaBox();

if ( ! function_exists( 'fmtb_get_meta' ) ) {
	function fmtb_get_meta( $meta_key, $single = false, $post_id = null ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

		$post_meta = get_post_meta( $post_id, $meta_key, $single );

		return $post_meta;
	}
}