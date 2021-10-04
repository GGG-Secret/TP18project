<?php

namespace WPDataAccess\Settings {

	use WPDataAccess\Utilities\WPDA_Message_Box;
	use WPDataAccess\WPDA;

	class WPDA_Settings_DataPublisher extends WPDA_Settings {

		/**
		 * Add data publisher tab content
		 *
		 * See class documentation for flow explanation.
		 *
		 * @since   2.0.15
		 */
		protected function add_content() {
			if ( isset( $_REQUEST['action'] ) ) {
				$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ); // input var okay.

				// Security check.
				$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : ''; // input var okay.
				if ( ! wp_verify_nonce( $wp_nonce, 'wpda-publication-settings-' . WPDA::get_current_user_login() ) ) {
					wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) );
				}

				if ( 'save' === $action ) {
					// Save options.
					if ( isset( $_REQUEST['publication_roles'] ) ) {
						$publication_roles_request = isset( $_REQUEST['publication_roles'] ) ? $_REQUEST['publication_roles'] : null;
						if ( is_array( $publication_roles_request ) ) {
							$publication_roles = implode( ',', $publication_roles_request );
						} else {
							$publication_roles = '';
						}
					} else {
						$publication_roles = '';
					}
					WPDA::set_option( WPDA::OPTION_DP_PUBLICATION_ROLES, $publication_roles );

					if ( isset( $_REQUEST['json_editing'] ) ) {
						WPDA::set_option(
							WPDA::OPTION_DP_JSON_EDITING,
							sanitize_text_field( wp_unslash( $_REQUEST['json_editing'] ) )
						);
					}
					if ( isset( $_REQUEST['publication_style'] ) ) {
						WPDA::set_option(
							WPDA::OPTION_DP_STYLE,
							sanitize_text_field( wp_unslash( $_REQUEST['publication_style'] ) )
						);
					}
					WPDA::set_option(
						WPDA::OPTION_DP_STYLE_COMPACT,
						isset( $_REQUEST['publication_style_compact'] ) ? 'on' : 'off' // input var okay.
					);
				} elseif ( 'setdefaults' === $action ) {
					// Set all publication settings back to default.
					WPDA::set_option( WPDA::OPTION_DP_PUBLICATION_ROLES );
					WPDA::set_option( WPDA::OPTION_DP_STYLE );
					WPDA::set_option( WPDA::OPTION_DP_STYLE_COMPACT );
					WPDA::set_option( WPDA::OPTION_DP_JSON_EDITING );
				}

				$msg = new WPDA_Message_Box(
					[
						'message_text' => __( 'Settings saved', 'wp-data-access' ),
					]
				);
				$msg->box();

			}

			global $wp_roles;
			$lov_roles = [];
			foreach ( $wp_roles->roles as $role => $val ) {
				array_push( $lov_roles, $role );
			}
			$publication_roles         = WPDA::get_option( WPDA::OPTION_DP_PUBLICATION_ROLES );
			$publication_style         = WPDA::get_option( WPDA::OPTION_DP_STYLE );
			$publication_style_compact = WPDA::get_option( WPDA::OPTION_DP_STYLE_COMPACT );
			$json_editing              = WPDA::get_option( WPDA::OPTION_DP_JSON_EDITING );
			?>
			<form id="wpda_settings_publication" method="post"
				  action="?page=<?php echo esc_attr( $this->page ); ?>&tab=datapublisher">
				<table class="wpda-table-settings">
					<tr>
						<th><?php echo __( 'Publication style', 'wp-data-access' ); ?></th>
						<td>
							<select name="publication_style">
								<option value="default" <?php echo 'default'===$publication_style ? 'selected' : ''; ?>>Default</option>
								<option value="jqueryui" <?php echo 'jqueryui'===$publication_style ? 'selected' : ''; ?>>jQuery UI</option>
								<option value="semantic" <?php echo 'semantic'===$publication_style ? 'selected' : ''; ?>>Semantic</option>
								<option value="foundation" <?php echo 'foundation'===$publication_style ? 'selected' : ''; ?>>Foundation</option>
								<option value="bootstrap" <?php echo 'bootstrap'===$publication_style ? 'selected' : ''; ?>>Bootstrap 3</option>
								<option value="bootstrap4" <?php echo 'bootstrap4'===$publication_style ? 'selected' : ''; ?>>Bootstrap 4</option>
							</select>
							<div style="padding-top:5px">
								<label>
									<input type="checkbox" name="publication_style_compact"
										<?php echo 'on' === $publication_style_compact ? 'checked' : ''; ?>
									/>Compact
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'Default jQuery UI theme', 'wp-data-access' ); ?></th>
						<td>
							<a href="<?php echo admin_url('options-general.php?page=wpdataaccess'); ?>&tab=frontend">
								Change default jQuery UI theme
							</a> (used when jQuery UI style is selected)
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'JSON Editing', 'wp-data-access' ); ?></th>
						<td>
							<label>
								<input type="radio" name="json_editing" value="validate"
									<?php echo 'validate' === $json_editing ? 'checked' : ''; ?>
								><?php echo __( 'Use code editor with JSON validation', 'wp-data-access' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="json_editing" value="text"
									<?php echo 'text' === $json_editing ? 'checked' : ''; ?>
								><?php echo __( 'Use textarea without JSON validation', 'wp-data-access' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'Data Publisher Tool Access', 'wp-data-access' ); ?></th>
						<td><div style="padding-bottom: 20px">
								<?php echo __( 'Select WordPress roles allowed to access Data Publisher', 'wp-data-access' ); ?>
							</div>
							<select name="publication_roles[]" multiple size="6">
								<?php
								foreach ( $lov_roles as $lov_role ) {
									if ( false !== stripos( $publication_roles, $lov_role ) ) {
										$granted = 'selected';
									} else {
										$granted = '';
									}
									?>
									<option value="<?php echo $lov_role; ?>" <?php echo $granted; ?>><?php echo $lov_role; ?></option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th><span class="dashicons dashicons-info" style="float:right;font-size:300%;"></span></th>
						<td>
							<span class="dashicons dashicons-yes"></span>
							<?php echo __( 'Users have readonly access to tables to which you have granted access in Front-end Settings only', 'wp-data-access' ); ?>
							<br/>
							<span class="dashicons dashicons-yes"></span>
							<?php echo __( 'Table access is automatically granted to tables used in the Data Publisher', 'wp-data-access' ); ?>
						</td>
					</tr>
				</table>
				<div class="wpda-table-settings-button">
					<input type="hidden" name="action" value="save"/>
					<input type="submit"
						   value="<?php echo __( 'Save Publication Settings', 'wp-data-access' ); ?>"
						   class="button button-primary"/>
					<a href="javascript:void(0)"
					   onclick="if (confirm('<?php echo __( 'Reset to defaults?', 'wp-data-access' ); ?>')) {
						   jQuery('input[name=&quot;action&quot;]').val('setdefaults');
						   jQuery('#wpda_settings_publication').trigger('submit')
						   }"
					   class="button">
						<?php echo __( 'Reset Publication Settings To Defaults', 'wp-data-access' ); ?>
					</a>
				</div>
				<?php wp_nonce_field( 'wpda-publication-settings-' . WPDA::get_current_user_login(), '_wpnonce', false ); ?>
			</form>
			<?php
		}

	}

}