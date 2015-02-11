<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Block direct access to this file

if ( ! class_exists( 'TISSheets_Plugin_Functions' ) ) {
	class TISSheets_Plugin_Functions {
		// Extend allowed file types in Media Uploader to include CSS files if not already allowed
		public static function custom_upload_mimes( $existing_mimes = array() ) {
			if ( current_user_can( 'edit_theme_options' ) && ! isset( $existing_mimes['css'] ) ) {
				$existing_mimes['css'] = 'text/css';
			}

			return $existing_mimes;
		}

		// Links on "Plugins" admin page
		public static function tissheets_plugin_links( $links, $file ) {
			$this_plugin = TISSHEETS_PLUGIN_BASENAME;

			if ( $file === $this_plugin ) {
				$settings_link = '<a href="options-general.php?page=tissheets_options">' . __( 'Settings', TISSHEETS_TEXT_DOMAIN ) . '</a>';

				$help_link     = '<a href="options-general.php?page=tissheets_options#help">' . __( 'Help', TISSHEETS_TEXT_DOMAIN ) . '</a>';

				$links[] = $settings_link;
				$links[] = $help_link;
			}

			return $links;
		}

		// Create default settings if they don't exist
		public static function tissheets_activate() {
			$default_settings = array(
				'stylesheets'    => array(),
				'full_uninstall' => 0,
			);

			add_option( 'tissheets_settings', $default_settings );
		}

		// Load any translations
		public static function load_translation_files() {
			$translation_path = plugin_basename( TISSHEETS_PATH . '/translations' );
			load_plugin_textdomain( TISSHEETS_TEXT_DOMAIN, false, $translation_path );
		}

		public static function sort_stylesheets( $a, $b ) {
			if ( is_array( $a ) ) {
				$a = ( object ) $a;
				$b = ( object ) $b;
			} else {
				$options  = get_option( 'tissheets_settings' );
				$sort_ids = array();

				$sort_ids['a'] = $a->ID;
				$sort_ids['b'] = $b->ID;

				$a = ( object ) $options['stylesheets'][ 'id-' . $sort_ids['a'] ];
				$b = ( object ) $options['stylesheets'][ 'id-' . $sort_ids['b'] ];
			}
			

			if ( $a->active === $b->active ) {
				if ( $a->load_order === $b->load_order ) {
					if ( $a->ID > $b->ID ) {
						return 1;
					} else {
						return -1;
					}
				}

				if ( $a->load_order < $b->load_order ) {
					return -1;
				} else {
					return 1;
				}
			} else if ( $a->active < $b->active ) {
				return 1;
			} else {
				return -1;
			}
		}

		public static function trim_values( &$value ) {
			$value = trim( $value );
		}

		/**
		 * Enqueue external stylesheet(s)
		 */
		public static function enqueue_external_styles() {
			global $wp_styles;

			$options = get_option( 'tissheets_settings' );

			if ( ! empty( $options['stylesheets'] ) ) {
				if ( count( $options['stylesheets'] ) ) {
					usort( $options['stylesheets'], 'self::sort_stylesheets' );
				}

				foreach ( $options['stylesheets'] as $stylesheet ) {
					if ( $stylesheet['active'] ) {
						$attachment_url     = wp_get_attachment_url( $stylesheet['ID'] );
						$attachment_version = null;

						if ( $stylesheet['version']['active'] ) {
							$attachment_version = $stylesheet['version']['number'];
						}

						if ( is_array( $stylesheet['media_types'] ) ) {
							$stylesheet['media_types'] = implode( ', ', $stylesheet['media_types'] );
						}

						if ( $stylesheet['location'] === 'external' ) {
							wp_enqueue_style( $stylesheet['handle'], esc_url( $attachment_url ), array(), $attachment_version, $stylesheet['media_types'] );

							if ( $stylesheet['ie_conditional_comment']['is_IE'] ) {
								$wp_styles->add_data( $stylesheet['handle'], 'conditional', implode( ' ', $stylesheet['ie_conditional_comment'] ) );
							}
						}
					}
				}
			}
		}

		/**
		 * Print inline stylesheet(s)
		 */
		public static function print_inline_styles() {
			$options = get_option( 'tissheets_settings' );

			if ( ! empty( $options['stylesheets'] ) ) {
				if ( count( $options['stylesheets'] ) > 1 ) {
					usort( $options['stylesheets'], 'self::sort_stylesheets' );
				}

				foreach ( $options['stylesheets'] as $stylesheet ) {
					if ( $stylesheet['active'] ) {
						$attachment_url = wp_get_attachment_url( $stylesheet['ID'] );

						if ( is_array( $stylesheet['media_types'] ) ) {
							$stylesheet['media_types'] = implode( ', ', $stylesheet['media_types'] );
						}

						if ( $stylesheet['location'] === 'inline' ) {
							// Look into putting this whole thing into WordPress hooks
							$upload_dir = wp_upload_dir();
							$output     = array();

							$attachment_path = $upload_dir['basedir'] . str_replace( $upload_dir['baseurl'], '', $attachment_url );

							$stylesheet_content = file_get_contents( $attachment_path );

							if ( $stylesheet['ie_conditional_comment']['is_IE'] ) {
								$output[] = '<!--[if ' . implode( ' ', $stylesheet['ie_conditional_comment'] ) . ']>';
							}

							$output[] = '<style id="' . esc_attr( $stylesheet['handle'] ) . '-inline-css" media="' . esc_attr( $stylesheet['media_types'] ) . '" type="text/css">';
							$output[] = $stylesheet_content;
							$output[] = '</style>';

							if ( $stylesheet['ie_conditional_comment'] ) {
								$output[] = '<![endif]-->';
							}

							echo implode( "\n", $output );
						}
					}
				}
			}
		}
	}
}
