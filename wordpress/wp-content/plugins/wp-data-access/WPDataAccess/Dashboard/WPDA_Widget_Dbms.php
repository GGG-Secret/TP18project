<?php

namespace WPDataAccess\Dashboard {

	use WPDataAccess\WPDA;

	class WPDA_Widget_Dbms extends WPDA_Widget {

		protected $id;
		protected $schema_name;

		public function __construct( $args = [] ) {
			parent::__construct( $args );

			$this->can_refresh = true;

			global $wpdb;
			if ( isset( $args['schema_name'] ) ) {
				$schema_name = $args['schema_name'];

				if ( 'wpdb' === $schema_name ) {
					$schema_name = $wpdb->dbname;
					$this->title = "WordPress database ($schema_name)";
				} else {
					$this->title = "Remote database $schema_name";
				}
			} else {
				$schema_name = $wpdb->dbname;
				$this->title = "WordPress database ($schema_name)";
			}

			$this->id          = $this->widget_id . str_replace( ':', '_', $schema_name );
			$this->schema_name = $schema_name;

			$info = static::get_data( $schema_name );

			$dbms_status = "
				<div id='{$this->id}' class='wpda-dbms-container'>
					<ul>
						<li><a href='#{$this->id}-1'>Instance</a></li>
						<li><a href='#{$this->id}-2'>Variables</a></li>
						<li><a href='#{$this->id}-3'>Status</a></li>
					</ul>
					<div id='{$this->id}-1'>
						<table class='wpda_dbms_table'>
							<tr>
								<th>Host</th>
								<td class='hostname'>{$info['hostname']}</td>
							</tr>
							<tr>
								<th>Port</th>
								<td class='post'>{$info['port']}</td>
							</tr>
							<tr>
								<th>SSL</th>
								<td class='ssl'>{$info['ssl']}</td>
							</tr>
							<tr>
								<th>Version</th>
								<td class='version'>{$info['version']} {$info['version_comment']}</td>
							</tr>
							<tr>
								<th>Compiled For</th>
								<td class='compiled'>{$info['version_compile_os']} ({$info['version_compile_machine']})</td>
							</tr>
							<tr>
								<th>Uptime</th>
								<td class='uptime'>{$info['uptime']}</td>
							</tr>
							<tr>
								<th colspan='2'>&nbsp;</th>
							</tr>
							<tr>
								<th colspan='2' id='{$this->id}-dir' class='wpda-dbms-link'><i class='fas fa-caret-right'></i> Server Directories</th>
							</tr>
							<tr class='dir' style='display: none'>
								<th colspan='2'><hr/></th>
							</tr>
							<tr class='dir' style='display: none'>
								<th>Base Directory</th>
								<td class='basedir'>{$info['basedir']}</td>
							</tr>
							<tr class='dir' style='display: none'>
								<th>Data Directory</th>
								<td class='datadir'>{$info['datadir']}</td>
							</tr>
							<tr class='dir' style='display: none'>
								<th>Plugins Directory</th>
								<td class='plugin_dir'>{$info['plugin_dir']}</td>
							</tr>
							<tr class='dir' style='display: none'>
								<th>Tmp Directory</th>
								<td class='tmpdir'>{$info['tmpdir']}</td>
							</tr>
							<tr class='dir' style='display: none'>
								<th colspan='2'>&nbsp;</th>
							</tr>
							<tr>
								<th colspan='2' id='{$this->id}-log' class='wpda-dbms-link'><i class='fas fa-caret-right'></i> Log Files</th>
							</tr>
							<tr class='log' style='display: none'>
								<th colspan='2'><hr/></th>
							</tr>
							<tr class='log' style='display: none'>
								<th>Error Log</th>
								<td class='log_error'>{$info['log_error']} [ON]</td>
							</tr>
							<tr class='log' style='display: none'>
								<th>General Log</th>
								<td class='general_log'>{$info['general_log_file']} [{$info['general_log']}]</td>
							</tr>
							<tr class='log' style='display: none'>
								<th>Slow Query Log</th>
								<td class='slow_query'>{$info['slow_query_log_file']} [{$info['slow_query_log']}]</td>
							</tr>
						</table>
					</div>
					<div id='{$this->id}-2' style='display: none'></div>
					<div id='{$this->id}-3' style='display: none'></div>
				</div>
			";

			$this->content = $dbms_status;
		}

		protected function js() {
			?>
			<script type='application/javascript' class="wpda-widget-<?php echo $this->widget_id; ?>">
				jQuery(function() {
					jQuery("#<?php echo esc_attr( $this->id ); ?>").tabs();

					jQuery("#<?php echo esc_attr( $this->id ); ?> :nth-child(2)").on("click", function() {
						getDbmsVars(
							jQuery("#<?php echo esc_attr( $this->id ); ?>-2"),
							"<?php echo esc_attr( $this->wp_nonce ); ?>",
							"vars",
							"<?php echo esc_attr( $this->schema_name ); ?>"
						);
					});

					jQuery("#<?php echo esc_attr( $this->id ); ?> :nth-child(3)").on("click", function() {
						getDbmsVars(
							jQuery("#<?php echo esc_attr( $this->id ); ?>-3"),
							"<?php echo esc_attr( $this->wp_nonce ); ?>",
							"status",
							"<?php echo esc_attr( $this->schema_name ); ?>"
						);
					});

					jQuery("#<?php echo esc_attr( $this->id ); ?>-dir").on("click", function(e) {
						toggleIcon(jQuery(this));
						jQuery(this).closest("tbody").find(".dir").toggle();
					});

					jQuery("#<?php echo esc_attr( $this->id ); ?>-log").on("click", function() {
						toggleIcon(jQuery(this));
						jQuery(this).closest("tbody").find(".log").toggle();
					});

					jQuery("#<?php echo esc_attr( $this->id ); ?>").closest(".wpda-widget").find(".wpda-widget-refresh").on("click", function() {
						getDbmsInfo(
							jQuery("#<?php echo esc_attr( $this->id ); ?>"),
							"<?php echo esc_attr( $this->wp_nonce ); ?>",
							"<?php echo esc_attr( $this->schema_name ); ?>",
						);

						getDbmsVars(
							jQuery("#<?php echo esc_attr( $this->id ); ?>-2"),
							"<?php echo esc_attr( $this->wp_nonce ); ?>",
							"vars",
							"<?php echo esc_attr( $this->schema_name ); ?>",
							true
						);

						getDbmsVars(
							jQuery("#<?php echo esc_attr( $this->id ); ?>-3"),
							"<?php echo esc_attr( $this->wp_nonce ); ?>",
							"status",
							"<?php echo esc_attr( $this->schema_name ); ?>",
							true
						);
					});

					<?php
					if ( wpda_freemius()->is_premium() ) {
						$obj = new \stdClass();
						$obj->dbsDbms = esc_attr( $this->schema_name );
					?>
						var obj = <?php echo json_encode( $obj ); ?>;
						setTimeout(function() {
							addDashboardWidget(
								<?php echo esc_attr( $this->widget_id ); ?>,
								"<?php echo esc_attr( $this->name ); ?>",
								'dbs',
								obj,
								false
							);
						}, 500);
					<?php
					}
					?>
				});
			</script>
			<?php
		}

		public static function widget() {
			$panel_name         = isset( $_REQUEST['wpda_panel_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_panel_name'] ) ) : ''; // input var okay.;
			$panel_dbms         = isset( $_REQUEST['wpda_panel_dbms'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_panel_dbms'] ) ) : ''; // input var okay.;
			$panel_column       = isset( $_REQUEST['wpda_panel_column'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_panel_column'] ) ) : '1'; // input var okay.;
			$column_position    = isset( $_REQUEST['wpda_column_position'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_column_position'] ) ) : 'prepend'; // input var okay.;
			$widget_sequence_nr = isset( $_REQUEST['wpda_widget_sequence_nr'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_widget_sequence_nr'] ) ) : '1'; // input var okay.;

			$wdg = new WPDA_Widget_Dbms([
				'name'		  => $panel_name,
				'schema_name' => $panel_dbms,
				'column'	  => $panel_column,
				'position'	  => $column_position,
				'widget_id'	  => $widget_sequence_nr,
			]);

			static::sent_header('text/html; charset=UTF-8');
			echo $wdg->container();
			wp_die();
		}

		public static function refresh() {
			if (
				! isset(
					$_REQUEST['wpda_action'], 
					$_REQUEST['wpda_schemaname'] 
				)
			) {
				static::sent_header();
				echo static::msg('ERROR', 'Invalid arguments');
				wp_die();
			}

			switch( $_REQUEST['wpda_action' ] ) {
				case 'vars':
					static::get_vars();
					break;
				case 'status':
					static::get_status();
					break;
				case 'info':
					static::get_info();
					break;
				default:
					static::sent_header();
					echo static::msg('ERROR', 'Invalid arguments');
					wp_die();
			}
		}

		protected static function get_data( $schema_name ) {
			return [
				'hostname'                => WPDA::get_dbms_var( 'hostname', $schema_name ),
				'ssl'                     => WPDA::get_dbms_var( 'have_ssl', $schema_name ),
				'port'                    => WPDA::get_dbms_var( 'port', $schema_name ),
				'version'                 => WPDA::get_dbms_var( 'version', $schema_name ),
				'version_comment'         => WPDA::get_dbms_var( 'version_comment', $schema_name ),
				'version_compile_os'	  => WPDA::get_dbms_var( 'version_compile_os', $schema_name ),
				'version_compile_machine' => WPDA::get_dbms_var( 'version_compile_machine', $schema_name ),
				'uptime'                  => WPDA::secondsToTime( WPDA::get_dbms_global( 'uptime', $schema_name ) ),

				'basedir'                 => WPDA::get_dbms_var( 'basedir', $schema_name ),
				'datadir'                 => WPDA::get_dbms_var( 'datadir', $schema_name ),
				'plugin_dir'              => WPDA::get_dbms_var( 'plugin_dir', $schema_name ),
				'tmpdir'                  => WPDA::get_dbms_var( 'tmpdir', $schema_name ),

				'log_error'               => WPDA::get_dbms_var( 'log_error', $schema_name ),
				'general_log'             => WPDA::get_dbms_var( 'general_log', $schema_name),
				'general_log_file'        => WPDA::get_dbms_var( 'general_log_file', $schema_name),
				'slow_query_log'          => WPDA::get_dbms_var( 'slow_query_log', $schema_name),
				'slow_query_log_file'     => WPDA::get_dbms_var( 'slow_query_log_file', $schema_name),
			];
		}

		protected static function get_vars() {
			static::sent_header();

			$schema_name = sanitize_text_field( wp_unslash( $_REQUEST['wpda_schemaname'] ) );
			$vars = WPDA::get_dbms_var( null, $schema_name );
			$json = [];
			foreach ( $vars as $var ) {
				$json[ $var[0] ] = $var[1];
			}

			echo json_encode( $json );
			wp_die();
		}

		protected static function get_status() {
			static::sent_header();

			$schema_name = sanitize_text_field( wp_unslash( $_REQUEST['wpda_schemaname'] ) );
			$vars = WPDA::get_dbms_global( null, $schema_name );
			$json = [];
			foreach ( $vars as $var ) {
				$json[ $var[0] ] = $var[1];
			}

			echo json_encode( $json );
			wp_die();
		}

		protected static function get_info() {
			static::sent_header();

			$schema_name = sanitize_text_field( wp_unslash( $_REQUEST['wpda_schemaname'] ) );

			echo json_encode( static::get_data( $schema_name ) );
			wp_die();
		}

	}

}