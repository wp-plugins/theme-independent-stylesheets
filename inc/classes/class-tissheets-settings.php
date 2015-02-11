<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Block direct access to this file

if ( ! class_exists( 'TISSheets_Settings' ) ) {
	class TISSheets_Settings {
		private $options;
		private $menu_slug = 'tissheets_options';
		private $media_types;
		private $conditonal_segments;
		private $errors;

		public function __construct() {
			$this->options = get_option( 'tissheets_settings' );

			$this->media_types = array(
				'braille',
				'embossed',
				'handheld',
				'print',
				'projection',
				'screen',
				'speech',
				'tty',
				'tv',
			);

			$this->conditional_segments = array(
				'comparison' => array(
					'lt',
					'lte',
					'gt',
					'gte',
				),
				'is_IE'         => array(
					'IE',
					'!IE',
				),
				'version'    => array(
					5,
					6,
					7,
					8,
					9,
				),
			);

			$this->errors = array();

			add_action( 'admin_init', array( $this, 'options_init' ) );
			add_action( 'admin_menu', array( $this, 'add_options_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'settings_css' ) );
		}

		// Register settings with WordPress Settings API
		public function options_init() {
			register_setting(
				'tissheets_settings',
				'tissheets_settings',
				array( $this, 'sanitize_settings' )
			);

			// Plugin Settings
			add_settings_section(
				'tissheets_plugin_settings_section',
				__( 'Plugin Settings', TISSHEETS_TEXT_DOMAIN ),
				array( $this, 'tissheets_plugin_settings_section_callback' ),
				$this->menu_slug
			);

			add_settings_field(
				'tissheets_plugin_full_uninstall',
				__( 'Full Wipe on Uninstall', TISSHEETS_TEXT_DOMAIN ),
				array( $this, 'tissheets_plugin_full_uninstall_callback' ),
				$this->menu_slug,
				'tissheets_plugin_settings_section'
			);
		}

		// Register "Plugin Settings" section
		public function tissheets_plugin_settings_section_callback() {
			echo '<p>' . sprintf( __( 'Settings for the <i>%s</i> plugin.', TISSHEETS_TEXT_DOMAIN ), TISSHEETS_PLUGIN_NAME ) . '</p>';
		}

		// Register "Full Uninstall" setting
		public function tissheets_plugin_full_uninstall_callback() {
			echo '<label><input name="tissheets_settings[full_uninstall]" id="tissheets_full_uninstall_cb" type="checkbox" value="1" class="code" ' . checked( 1, $this->options['full_uninstall'], false ) . ' /> Delete all settings for <i>' . TISSHEETS_PLUGIN_NAME . '</i> upon deleting the plugin.</label>';
		}

		// Add menu item under "Settings" menu
		public function add_options_page() {
			if ( current_user_can( 'edit_theme_options' ) ) {
				add_options_page( __( 'Theme-Independent Stylesheets', TISSHEETS_TEXT_DOMAIN ), __( 'Theme-Independent Stylesheets', TISSHEETS_TEXT_DOMAIN ), 'edit_theme_options', $this->menu_slug, array( $this, 'create_options_page' ) );
			}
		}

		// Enqueue settings page stylesheet
		public function settings_css( $hook ) {
			if ( 'settings_page_tissheets_options' === $hook ) {
				wp_enqueue_style( 'tissheets-settings-css', TISSHEETS_CSS . 'style.css' );
			}
		}

		/**
		 * Settings page output
		 */
		public function create_options_page() {
			?>
			<div class="wrap">
				<h2><?php _e( 'Theme-Independent Stylesheets', TISSHEETS_TEXT_DOMAIN ); ?></h2>

				<form method="post" action="options.php">

					<hr />

					<h3><?php _e( 'Stylesheet Settings <a href="#help" title="View Help Section"><span class="dashicons dashicons-editor-help"></span></a>', TISSHEETS_TEXT_DOMAIN ); ?></h3>
					<?php
					$stylesheets = get_posts( array(
						'posts_per_page' => -'1',
						'post_type'      => 'attachment',
						'post_mime_type' => 'text/css',
						'order'          => 'ASC',
						'orderby'        => 'ID',
					) );

					settings_fields( 'tissheets_settings' );

					$this->stylesheets_submit_button( $stylesheets );
					?>
					<table id="tissheets-settings-table" class="widefat fixed">
						<colgroup>
							<col id="tissheets-active-col" />

							<col id="tissheets-load-order-col" />

							<col id="tissheets-id-col" />

							<col id="tissheets-handle-col" />

							<col id="tissheets-name-col" />

							<col id="tissheets-version-col" />

							<col id="tissheets-media-types-col" />

							<col id="tissheets-location-col" />

							<col id="tissheets-ie-conditional-comment-col" />
						</colgroup>

						<?php
						$this->table_header( 'head' );
						?>
						<tbody>
							<?php
							if ( $stylesheets ) {
								if ( count ( $stylesheets > 1 ) && ! empty( $this->options['stylesheets'] ) ) {
									usort( $stylesheets, 'TISSheets_Plugin_Functions::sort_stylesheets' );
								}

								$sheet_row = 0;

								foreach( $stylesheets as $stylesheet ) {
									if ( ! isset( $this->options['stylesheets'][ 'id-' . $stylesheet->ID ] ) ) {
										$this->options['stylesheets'][ 'id-' . $stylesheet->ID ] = array(
											'active'                 => 0,
											'load_order'             => 0,
											'handle'                 => sanitize_title( $stylesheet->post_title ),
											'version'                => array(
												'active' => 0,
												'number' => '',
											),
											'media_types'            => 'all',
											'location'               => 'external',
											'ie_conditional_comment' => array(
												'comparison' => 0,
												'is_IE'      => 0,
												'version'    => 0,
											),
										);
									}

									$row_class = 'tissheets-stylesheet';

									// If row is odd, add "alternate" class
									if ( $sheet_row++ % 2 === 0 ) {
										$row_class .= ' alternate';
									}

									echo '<tr id="stylesheet-' . $stylesheet->ID . '" class="' . $row_class . '">';

									// Active
									echo '<td class="tissheets-active">' . $this->setting_field( 'checkbox', $stylesheet->ID, 'active', '1' ) . '</td>';

									// Load Order (same concept as WordPress "menu order")
									echo '<td class="tissheets-load-order">' . $this->setting_field( 'number', $stylesheet->ID, 'load_order', $this->options['stylesheets'][ 'id-' . $stylesheet->ID ]['load_order'] ) . '</td>';

									// Stylesheet ID
									echo '<td class="tissheets-id">' . $stylesheet->ID . '</td>';

									// Stylesheet handle (for wp_register_style)
									echo '<td class="tissheets-handle">' . $this->setting_field( 'text', $stylesheet->ID, 'handle', $this->options['stylesheets'][ 'id-' . $stylesheet->ID ]['handle'], 'required' ) . '</td>';

									// Stylesheet name (with link to file)
									echo '<td class="tissheets-name"><a href="' . get_admin_url( null, 'post.php?post=' . $stylesheet->ID . '&action=edit' ) . '">' . $stylesheet->post_title . '</a></td>';

									// Stylesheet version
									echo '<td class="tissheets-version">' . $this->setting_field( 'checkbox', $stylesheet->ID, 'version][active', '1' ) . $this->setting_field( 'text', $stylesheet->ID, 'version][number', $this->options['stylesheets'][ 'id-' . $stylesheet->ID ]['version']['number'] ) . '</td>';

									// CSS media types
									echo '<td class="tissheets-media-types">';
									
									echo '<div class="tissheets-media-type-all-contain">';
									echo '<label>' . $this->setting_field( 'radio', $stylesheet->ID, 'media_type_radio', 'all' ) . ' all</label>';
									echo '</div>';

									echo '<div class="tissheets-media-type-specific-contain">';
									echo '<label>'	. $this->setting_field( 'radio', $stylesheet->ID, 'media_type_radio', 'specific' ) . ' ' . $this->media_types_select( $stylesheet->ID ) . '</label>';
									echo '</div>';
									
									echo '</td>';

									// External or inline?
									echo '<td class="tissheets-location">';
									
									echo '<div class="tissheets-location-external-contain">';
									echo '<label>' . $this->setting_field( 'radio', $stylesheet->ID, 'location', 'external' ) . ' ' . __( 'External', TISSHEETS_TEXT_DOMAIN ) . ' <small>(&lt;link /&gt;)</small></label>';
									echo '</div>';
									
									echo '<div class="tissheets-location-inline-contain">';
									echo '<label>' . $this->setting_field( 'radio', $stylesheet->ID, 'location', 'inline' ) . ' ' . __( 'Inline', TISSHEETS_TEXT_DOMAIN ) . ' <small>(&lt;style&gt;)</small></label>';
									echo '</div>';

									echo '</td>';

									// Conditional comment for Internet Explorer 5 - 9
									// @todo Replace text input with dropdowns
									echo '<td class="tissheets-ie-conditional-comment">';

									echo '<span class="tissheets-ie-conditional-comment-contain">' . $this->conditional_comment_select( $stylesheet->ID, 'comparison', $this->conditional_segments['comparison'] ) . $this->conditional_comment_select( $stylesheet->ID, 'is_IE', $this->conditional_segments['is_IE'] ) . $this->conditional_comment_select( $stylesheet->ID, 'version', $this->conditional_segments['version'] ) . '</span>';

									echo '</td>';
								}
							} else {
								echo '<td class="tissheets-none-found" colspan="9">' . __( 'No stylesheets found in WordPress Media Library.', TISSHEETS_TEXT_DOMAIN ) . '</td>';
							}

							echo '</tr>';
							?>
						</tbody>

						<?php
						$this->table_header( 'foot' );
						?>
					</table>
					<?php
					$this->stylesheets_submit_button( $stylesheets );
					?>

					<hr />

					<?php
					// Wipe settings on plugin deactivation?
					do_settings_sections( $this->menu_slug );

					submit_button();
					?>
				</form>

				<?php
				/**
				 * Help section
				 */
				require_once( TISSHEETS_INC . 'help.php' );
				?>
			</div>
			<?php
		}

		/**
		 * Output <thead> or <tfoot> section
		 * @param  string $section 'head' for <thead>, 'foot' for <tfoot>
		 */
		private function table_header( $section ) {
			echo "<t$section>";
				?>
				<tr>
					<th class="column-title" scope="col"><?php _e( 'Active', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( 'Load<br />Order', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( 'ID', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( 'Handle <small>(required)</small>', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( 'Name', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( 'Version', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( 'Media Type(s)', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( 'External or inline?', TISSHEETS_TEXT_DOMAIN ); ?></th>

					<th class="column-title" scope="col"><?php _e( '<abbr title="Internet Explorer">IE</abbr> Conditional Comment', TISSHEETS_TEXT_DOMAIN ); ?></th>
				</tr>
				<?php
			echo '</t$section>';
		}

		/**
		 * Output <input /> element 
		 * @param  string  $type          "type" attribute for <input />
		 * @param  string   $stylesheet_id Media ID of stylesheet
		 * @param  string  $setting       Setting name
		 * @param  string  $value         "value" attribute for <input />
		 * @param  string $required      If set to 'required', place HTML5 "required" attribute
		 * @param  string  $spellcheck    "spellcheck" attribute for <input />
		 * @return string                 Completed <input /> string
		 */
		private function setting_field( $type, $stylesheet_id, $setting, $value, $required = false, $spellcheck = null ) {
			// "checked" attribute
			if ( $type === 'radio' &&
				// "media_types_specific"
				(
					(
						! isset( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'] )
						|| $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'] === 'all'
					)
					&& $value === 'all'
				)
				|| (
					isset( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'] )
					&& $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'] !== 'all'
					&& $value === 'specific'
				)
				// External or inline?
				|| (
					(
						! isset( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['location'] )
						&& $value === 'external'
					)
					|| isset( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'] ) && $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['location'] === $value
				)
			) {
				$checked = true;
			} else {
				$checked = false;
			}

			$output  = '<input ';
			$output .= 'type="' . $type . '" ';
			$output .= 'name="tissheets_settings[stylesheets][id-' . $stylesheet_id . '][' . $setting . ']" ';
			$output .= 'id="stylesheet-' . esc_attr( $stylesheet_id ) . '-' . esc_attr( $setting ) . '-' . esc_attr( $value ) . '" ';
			$output .= 'value="' . esc_attr( $value ) . '"';

			switch ( $type ) {
				case 'checkbox':
					if ( $setting === 'version][active' ) {
						$output .= checked( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['version']['active'], true, false );
					} else {
						$output .= checked( $this->options['stylesheets'][ 'id-' . $stylesheet_id ][ $setting ], true, false );
					}
					break;

				case 'radio':
					$output .= checked( $checked, true, false );
					break;

				case 'number':
					$output .= ' min="0"';
					break;
			}

			if ( $required === 'required' ) {
				$output .= ' required="required"';
			}

			if ( ! is_null( $spellcheck ) ) {
				$output .= ' spellcheck="' . $spellcheck . '"';
			}

			$output .= ' />';

			return $output;
		}

		/**
		 * Outputs <select> menu for media types dropdown
		 * @param  string $stylesheet_id Media ID of stylesheet
		 * @return string                Completed <select> menu string
		 */
		private function media_types_select( $stylesheet_id ) {
			if ( ! isset( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'] ) || $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'] === 'all' ) {
				$specified = array();
			} else {
				$specified = $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['media_types'];
			}

			$output = '<select id="stylesheet-' . esc_attr( $stylesheet_id ) . '-media-types" name="tissheets_settings[stylesheets][id-' . esc_attr( $stylesheet_id ) . '][media_types][]" multiple="multiple" size="5" class="media-types-dropdown">' . "\n";

			foreach( $this->media_types as $media_type ) {
				$output .= '<option value="' . esc_attr( $media_type ) . '"' . selected( in_array( $media_type, $specified ), true, false ) . '>' . esc_html( $media_type ) . '</option>' . "\n";
			}

			$output .= '</select>';

			return $output;
		}

		/**
		 * Outputs specified <select> menu for IE conditional comment dropdowns
		 * @param  string $stylesheet_id Media ID of stylesheet
		 * @param  string $segment       Name of comment segment
		 * @param  array  $options       Array of <option> values
		 * @return string                Completed <select> menu string
		 */
		private function conditional_comment_select( $stylesheet_id, $segment, $options ) {
			$output = '<select id="stylesheet-' . esc_attr( $stylesheet_id ) . '-conditional-comment-' . $segment . '" name="tissheets_settings[stylesheets][id-' . esc_attr( $stylesheet_id ) . '][ie_conditional_comment][' . esc_attr( $segment ) . ']" class="conditional-comment-dropdown conditional-comment-' . sanitize_html_class( $segment, esc_attr( $segment ) ) . '-dropdown">' . "\n";

			if ( ! isset ( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['ie_conditional_comment'][ $segment ] ) || ( isset( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['ie_conditional_comment'][ $segment ] ) && empty( $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['ie_conditional_comment'][ $segment ] ) ) ) {
				$current = 0;
			} else {
				$current = $this->options['stylesheets'][ 'id-' . $stylesheet_id ]['ie_conditional_comment'][ $segment ];
			}

			$output .= '<option value="0"' . selected( $current, 0, false ) . '> </option>' . "\n";

			foreach ( $options as $value ) {
				$output .= '<option value="' . esc_attr( $value ) . '"' . selected( $current, $value, false ) .'>' . esc_html( $value ) . '</option>' . "\n";
			}

			$output .= '</select>';

			return $output;
		}

		// Echo submit button if media library contains stylesheets
		private function stylesheets_submit_button( $stylesheets ) {
			if ( $stylesheets ) {
				submit_button();
			}
		}

		// Save settings to database/
		public function sanitize_settings( $input ) {
			if ( current_user_can( 'edit_theme_options' ) ) {
				$output = array();

				foreach ( $input['stylesheets'] as $id => $stylesheet ) {
					$stylesheet_id                      = intval( str_replace( 'id-', '', $id ) );
					$output['stylesheets'][ $id ]['ID'] = $stylesheet_id;

					// Active
					if ( isset( $stylesheet['active'] ) && $stylesheet['active'] ) {
						$output['stylesheets'][ $id ]['active'] = 1;
					} else {
						$output['stylesheets'][ $id ]['active'] = 0;
					}

					// Load order
					if ( isset( $stylesheet['load_order'] ) && is_numeric( $stylesheet['load_order'] ) ) {
						$load_order = intval( sanitize_text_field( $stylesheet['load_order'] ) );

						if ( $load_order >= 0 ) {
							$output['stylesheets'][ $id ]['load_order'] = round( $load_order );
						} else {
							$output['stylesheets'][ $id ]['load_order'] = 0;
						}
					} else {
						$output['stylesheets'][ $id ]['load_order'] = 0;
					}

					// Handle
					if ( isset( $stylesheet['handle'] ) && ! empty( $stylesheet['handle'] ) ) {
						$output['stylesheets'][ $id ]['handle'] = sanitize_title( $stylesheet['handle'] );
					} else {
						$output['stylesheets'][ $id ]['handle'] = sanitize_title( get_the_title( $stylesheet_id ) );

						if ( ! isset( $this->errors['handle']['msg'] ) ) {
							$this->errors['handle']['msg'] = __( 'A handle is required for all stylesheets.', TISSHEETS_TEXT_DOMAIN );
						}
					}

					// Version
					if ( isset( $stylesheet['version']['active'] ) && $stylesheet['version']['active'] ) {
						$output['stylesheets'][ $id ]['version']['active'] = 1;
					} else {
						$output['stylesheets'][ $id ]['version']['active'] = 0;
					}

					if ( isset( $stylesheet['version']['number'] ) && ! empty( $stylesheet['version']['number'] ) ) {
						$output['stylesheets'][ $id ]['version']['number'] = sanitize_text_field( trim( $stylesheet['version']['number'] ) );
					} else {
						$output['stylesheets'][ $id ]['version']['number'] = false;
					}

					// Media Types
					if ( isset( $stylesheet['media_type_radio'] ) && ! empty( $stylesheet['media_type_radio'] ) && $stylesheet['media_type_radio'] === 'specific' && ! empty( $stylesheet['media_types'] ) ) {
						array_walk( $stylesheet['media_types'], 'TISSheets_Plugin_Functions::trim_values' );

						foreach( $stylesheet['media_types'] as $key => $media_type ) {
							if ( ! in_array( $media_type, $this->media_types ) ) {
								unset( $stylesheet['media_types'][ $key ] );
							}
						}

						$output['stylesheets'][ $id ]['media_types'] = $stylesheet['media_types'];
					} else {
						$output['stylesheets'][ $id ]['media_types'] = 'all';
					}

					// External or inline?
					if ( isset( $stylesheet['location'] ) && ! empty( $stylesheet['location'] ) && $stylesheet['location'] === 'inline' ) {
						$output['stylesheets'][ $id ]['location'] = 'inline';
					} else {
						$output['stylesheets'][ $id ]['location'] = 'external';
					}

					/**
					 * Conditional comment for Internet Explorer 5 - 9
					 */

					// Comparison
					if (
						isset( $stylesheet['ie_conditional_comment']['comparison'] ) && ( ! empty( $stylesheet['ie_conditional_comment']['comparison'] ) || $stylesheet['ie_conditional_comment']['comparison'] === '0' )
						&& ( // Make sure option is a valid choice
							$stylesheet['ie_conditional_comment']['comparison'] === '0' || in_array( $stylesheet['ie_conditional_comment']['comparison'], $this->conditional_segments['comparison'] )
						)
					) {
						$output['stylesheets'][ $id ]['ie_conditional_comment']['comparison'] = $stylesheet['ie_conditional_comment']['comparison'];
					} else {
						$output['stylesheets'][ $id ]['ie_conditional_comment']['comparison'] = '0';
					}

					// Is/is not IE
					if (
						isset( $stylesheet['ie_conditional_comment']['is_IE'] ) && ( ! empty( $stylesheet['ie_conditional_comment']['is_IE'] ) || $stylesheet['ie_conditional_comment']['is_IE'] === '0' )
						&& ( // Make sure option is a valid choice
							$stylesheet['ie_conditional_comment']['is_IE'] === '0' || in_array( $stylesheet['ie_conditional_comment']['is_IE'], $this->conditional_segments['is_IE'] )
						)
					) {
							$output['stylesheets'][ $id ]['ie_conditional_comment']['is_IE'] = $stylesheet['ie_conditional_comment']['is_IE'];
					} else {
						$output['stylesheets'][ $id ]['ie_conditional_comment']['is_IE'] = 0;
					}

					// Version
					if (
						isset( $stylesheet['ie_conditional_comment']['version'] ) && ( ! empty( $stylesheet['ie_conditional_comment']['version'] ) || $stylesheet['ie_conditional_comment']['version'] === '0' )
						&& ( // Make sure option is a valid choice
							$stylesheet['ie_conditional_comment']['version'] === '0' || in_array( $stylesheet['ie_conditional_comment']['version'], $this->conditional_segments['version'] )
						)
					) {
						$output['stylesheets'][ $id ]['ie_conditional_comment']['version'] = intval( $stylesheet['ie_conditional_comment']['version'] );
					} else {
						$output['stylesheets'][ $id ]['ie_conditional_comment']['version'] = 0;
					}
					
					// Check for valid segment pairs
					if (
						( // If "comparison" or "version" are set then "is_IE" is required
							( $stylesheet['ie_conditional_comment']['comparison'] !== '0' || $stylesheet['ie_conditional_comment']['version'] !== '0' ) && $stylesheet['ie_conditional_comment']['is_IE'] === '0'
						)
						|| ( // if "comparison" segment is set then "version" is required and "is_IE" must be set to "IE"
							$stylesheet['ie_conditional_comment']['comparison'] !== '0' && ( $stylesheet['ie_conditional_comment']['version'] === '0' || $stylesheet['ie_conditional_comment']['is_IE'] !== 'IE' )
						)
						|| ( // If "comparison" is set to "lt" or "lte" then "version" must not be "5"
							( substr( $stylesheet['ie_conditional_comment']['comparison'], 0, 2 ) === 'lt' && $stylesheet['ie_conditional_comment']['version'] === '5' )
						)
						|| ( // If "comparison" is set to "gt" or "gte" then "version" must not be "9"
							( substr( $stylesheet['ie_conditional_comment']['comparison'], 0, 2 ) === 'gt' && $stylesheet['ie_conditional_comment']['version'] === '9' )
						)
					) {
						$output['stylesheets'][ $id ]['ie_conditional_comment']['comparison'] = 0;
						$output['stylesheets'][ $id ]['ie_conditional_comment']['is_IE'] = 0;
						$output['stylesheets'][ $id ]['ie_conditional_comment']['version'] = 0;

						$this->errors['ie_conditional_comment'][ $id ]['msg'] = __( 'Invalid <abbr title="Internet Explorer">IE</abbr> Conditional Comment provided for stylesheet ID ' . $stylesheet_id . '.', TISSHEETS_TEXT_DOMAIN );
					}
				}

				// Wipe settings on plugin deactivation?
				if ( isset( $input['full_uninstall'] ) && $input['full_uninstall'] ) {
					$output['full_uninstall'] = 1;
				} else {
					$output['full_uninstall'] = 0;
				}

				$error_code = 'settings_updated';

				if ( ! empty( $this->errors ) ) {
					foreach ( $this->errors as $key => $error ) {
						switch ( $key ) {
							case 'handle':
								add_settings_error(
									'tissheets_error_' . $key,
									$error_code,
									$error['msg']
								);
								break;

							case 'ie_conditional_comment':
								foreach( $error as $error_id => $msg ) {
									add_settings_error(
										'tissheets_error_' . $key . '_id_' . $error_id,
										$error_code,
										$msg['msg']
									);
								}
								break;
						}
					}
				} else {
					add_settings_error(
						'tissheets_settings',
						$error_code,
						__( 'Settings saved.', TISSHEETS_TEXT_DOMAIN ),
						'updated'
					);
				}

				return $output;
			}
		}
	}
}
