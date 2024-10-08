<?php
/**
 * Redux_Travelpayouts Field Class
 *
 * @class Redux_Travelpayouts_Field
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

use Redux_Travelpayouts_Descriptor_Types as RDT;
// TODO Require instead!

if ( ! class_exists( 'Redux_Travelpayouts_Field', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Field
	 */
	abstract class Redux_Travelpayouts_Field {
        /**
         * CSS styling per field output/compiler.
         *
         * @var string
         */
        public $style = null;

        /**
         * Class dir.
         *
         * @var string
         */
        public $dir = null;

        /**
         * Class URL.
         *
         * @var string
         */
        public $url = null;

        /**
         * Timestamp for ver append in dev_mode
         *
         * @var string
         */
        public $timestamp = null;

        /**
         * TravelpayoutsSettingsFramework object pointer.
         *
         * @var TravelpayoutsSettingsFramework
         */
        public $parent;

        /**
         * Field values.
         *
         * @var string|array
         */
        public $value;

		/**
		 * Array of descriptors.
		 *
		 * @var Redux_Travelpayouts_Descriptor[]
		 */
		public static $descriptors = array();

        /**
         * Field values.
         *
         * @var string|array
         */
        public $field;
        /**
         * Select2 options.
         *
         * @var array
         */
        public $select2_config = [];
        /**
         * @var
         */
        public $default;
        /**
         * @var
         */
        public $section_id;
        /**
         * @var
         */
        public $name;
        /**
         * @var
         */
        public $type;

        /**
         * @var
         */
        public $select2;
        /**
         * @var
         */
        public $id;
        /**
         * @var
         */
        public $title;
        /**
         * @var
         */
        public $subtitle;
        /**
         * @var
         */
        public $desc;
        /**
         * @var
         */
        public $hideTitle;
        /**
         * @var
         */
        public $skipSave;
        /**
         * @var
         */
        public $wrapField;
        /**
         * @var
         */
        public $priority;
        /**
         * @var
         */
        public $class;
        /**
         * @var
         */
        public $name_suffix;

        public $args;

        /**
		 * Make base descriptor.
		 *
		 * @return Redux_Travelpayouts_Descriptor
		 */
		public static function make_base_descriptor() {
			$d                                       = new Redux_Travelpayouts_Descriptor( get_called_class() );
			self::$descriptors[ get_called_class() ] = $d;

			$d->add_field( 'id', __( 'Field ID', 'redux-framework' ), RDT::TEXT )->set_order( 0 )->set_required();
			$d->add_field( 'title', __( 'Title', 'redux-framework' ), RDT::TEXT, '' )->set_order( 1 );
			$d->add_field( 'subtitle', __( 'Subtitle', 'redux-framework' ), RDT::TEXT, '' )->set_order( 2 );
			$d->add_field( 'desc', __( 'Description', 'redux-framework' ), RDT::TEXT, '' )->set_order( 3 );
			$d->add_field( 'class', __( 'Class', 'redux-framework' ), RDT::TEXT, '' )->set_order( 3 );
			$d->add_field( 'compiler', __( 'Compiler', 'redux-framework' ), RDT::BOOL, '', false )->set_order( 60 );
			$d->add_field( 'default', __( 'Default', 'redux-framework' ), RDT::OPTIONS, '', false )->set_order( 60 );
			$d->add_field( 'disabled', __( 'Disabled', 'redux-framework' ), RDT::BOOL, '', false )->set_order( 60 );
			$d->add_field( 'hint', __( 'Hint', 'redux-framework' ), RDT::OPTIONS, '', false )->set_order( 60 );
			$d->add_field( 'hint', __( 'Permissions', 'redux-framework' ), RDT::OPTIONS, '', false )->set_order( 60 );
			$d->add_field( 'required', __( 'Required', 'redux-framework' ), RDT::BOOL, '', false )->set_order( 60 );

			return $d;
		}

		/**
		 * Renders an attribute array into an html attributes string.
		 *
		 * @param array $attributes HTML attributes.
		 *
		 * @return string
		 */
		public static function render_attributes( $attributes = array() ) {
			$output = '';

			if ( empty( $attributes ) ) {
				return $output;
			}

			foreach ( $attributes as $key => $value ) {
				if ( false === $value || '' === $value ) {
					continue;
				}

				if ( is_array( $value ) ) {
					$value = wp_json_encode( $value );
				}

				$output .= sprintf( true === $value ? ' %s' : ' %s="%s"', $key, esc_attr( $value ) );
			}

			return $output;
		}

		/**
		 * Get descriptor.
		 *
		 * @return Redux_Travelpayouts_Descriptor
		 */
		public static function get_descriptor() {
			if ( ! isset( static::$descriptors[ get_called_class() ] ) ) {
				static::make_descriptor();
			}

			$d = self::$descriptors[ get_called_class() ];

			static::make_descriptor();

			// This part is out of opt name because it's non vendor dependant!
			return apply_filters( 'redux_travelpayouts/field/' . $d->get_field_type() . '/get_descriptor', $d ); // phpcs:ignore WordPress.NamingConventions.ValidHookName
		}

		/**
		 * Build the field descriptor in this function.
		 */
		public static function make_descriptor() {
			static::make_base_descriptor();
		}



		/**
		 * Redux_Travelpayouts_Field constructor.
		 *
		 * @param array  $field Field array.
		 * @param string $value Field values.
		 * @param null   $parent TravelpayoutsSettingsFramework object pointer.
		 *
		 * @throws ReflectionException Comment.
		 */
		public function __construct( $field = array(), $value = null, $parent = null ) {
			$this->parent = $parent;
			$this->field  = $field;
			$this->value  = $value;

			$this->select2_config = array(
				'width'      => 'resolve',
				'allowClear' => false,
				'theme'      => 'default',
			);

			$this->set_defaults();

			$class_name = get_class( $this );
			$reflector  = new ReflectionClass( $class_name );
			$path       = $reflector->getFilename();
			$path_info  = Redux_Travelpayouts_Helpers::path_info( $path );
			$this->dir  = trailingslashit( dirname( $path_info['real_path'] ) );
			$this->url  = trailingslashit( dirname( $path_info['url'] ) );

			$this->timestamp = Redux_Travelpayouts_Core::$version;
			if ( $parent->args['dev_mode'] ) {
				$this->timestamp .= '.' . time();
			}
		}

		/**
		 * Retrive dirname.
		 *
		 * @return string
		 */
		protected function get_dir() {
			return $this->dir;
		}

		/**
		 * Media query compiler for Redux_Travelpayouts Pro,
		 *
		 * @param string $style_data CSS string.
		 */
		public function media_query( $style_data = '' ) {
			$query_arr = $this->field['media_query'];
			$css       = '';

			if ( isset( $query_arr['queries'] ) ) {
				foreach ( $query_arr['queries'] as $idx => $query ) {
					$rule      = isset( $query['rule'] ) ? $query['rule'] : '';
					$selectors = isset( $query['selectors'] ) ? $query['selectors'] : array();

					if ( ! is_array( $selectors ) && '' !== $selectors ) {
						$selectors = array( $selectors );
					}

					if ( '' !== $rule && ! empty( $selectors ) ) {
						$selectors = implode( ',', $selectors );

						$css .= '@media ' . $rule . '{';
						$css .= $selectors . '{' . $style_data . '}';
						$css .= '}';
					}
				}
			} else {
				return;
			}

			if ( isset( $query_arr['output'] ) && $query_arr['output'] ) {
				$this->parent->outputCSS .= $css;
			}

			if ( isset( $query_arr['compiler'] ) && $query_arr['compiler'] ) {
				$this->parent->compilerCSS .= $css;
			}
		}

		/**
		 * CSS for field output, if set.
		 *
		 * @param string $style CSS string.
		 */
		public function output( $style = '' ) {
			if ( '' !== $style ) {

				// Force output value into an array.
				if ( isset( $this->field['output'] ) && ! is_array( $this->field['output'] ) ) {
					$this->field['output'] = array( $this->field['output'] );
				}

				if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
					$keys                     = implode( ',', $this->field['output'] );
					$this->parent->outputCSS .= $keys . '{' . $style . '}';
				}

				// Force compiler value into an array.
				if ( isset( $this->field['compiler'] ) && ! is_array( $this->field['compiler'] ) ) {
					$this->field['compiler'] = array( $this->field['compiler'] );
				}

				if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
					$keys                       = implode( ',', $this->field['compiler'] );
					$this->parent->compilerCSS .= $keys . '{' . $style . '}';
				}
			}
		}

		/**
		 * Unused for now.
		 *
		 * @param string $data CSS data.
		 */
		public function css_style( $data ) {

		}

		/**
		 * Unused for now.
		 */
		public function set_defaults() {

		}

		/**
		 * Unused for now.
		 */
		public function render() {

		}

		/**
		 * Unused for now.
		 */
		public function enqueue() {

		}

		/**
		 * Unused for now.
		 *
		 * @param array  $field Field array.
		 * @param string $value Value array.
		 */
		public function localize( $field, $value = '' ) {

		}
	}
}
