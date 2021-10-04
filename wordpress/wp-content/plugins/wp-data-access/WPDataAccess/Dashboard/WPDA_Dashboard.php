<?php

namespace WPDataAccess\Dashboard {

	use WP_Data_Access_Admin;
	use WPDataAccess\Data_Dictionary\WPDA_Dictionary_Lists;
	use WPDataAccess\WPDA;
	use WPDataAccess\Connection\WPDADB;

    class WPDA_Dashboard {

		const DASHBOARD_SAVE = 'WPDA_DASHBOARD_SAVE';

    	protected $number_of_columns = 2;
		protected $wp_nonce_add      = null;
		protected $databases         = null;
		protected $default_database  = null;

		public function __construct( $args = [] ) {
			$this->wp_nonce_add = wp_create_nonce( WPDA_Widget::WIDGET_ADD . WPDA::get_current_user_login() );

			$dbs = WPDA_Dictionary_Lists::get_db_schemas();
			foreach ( $dbs as $db ) {
				$this->databases[] = $db['schema_name'];
			}
			$this->default_database = WPDA::get_user_default_scheme();
		}

        protected static function navigation_enabled( $navigation_type ) {
            return in_array( WPDA::get_option( WPDA::OPTION_PLUGIN_NAVIGATION ), [ 'both', $navigation_type ] );
        }

        public static function dashboard_enabled() {
            return self::navigation_enabled( 'dashboard' );
        }

        public static function menu_enabled() {
            return self::navigation_enabled( 'menu' );
        }

        public static function add_dashboard() {
            if ( self::dashboard_enabled() ) {
                $dashboard = new WPDA_Dashboard();
                $dashboard->dashboard();
            } else {
            	global $plugin_page;
            	if ( $plugin_page && WP_Data_Access_Admin::PAGE_DASHBOARD === $plugin_page ) {
            	?>
					<div class="wrap">
						<h1>WP Data Access dashboard is disabled!</h1>
						<br/>
						<a href="<?php echo admin_url('options-general.php?page=wpdataaccess'); ?>">
						> Enable dashboard now...
						</a>
					</div>
				<?php
				}
			}
        }

        public function dashboard() {
            wp_enqueue_style('wpdataaccess_dashboard');
            wp_enqueue_script('wpdataaccess_dashboard');
            
            $this->dashboard_default();
            $this->dashboard_mobile();
            
            if ( isset( $_REQUEST['page'] ) && 'wpda_dashboard' === $_REQUEST['page'] ) {
            	$this->toolbar();
				$this->add_forms();
                $this->columns();
				$this->add_panels();
				$this->dashboard_js();
			}
        }

        protected function dashboard_default() {
            ?>
            <div id="wpda-dashboard" style="display:none">
                <div class="wpda-dashboard">
                    <div class="wpda-dashboard-group wpda-dashboard-group-dashboard">
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('admin.php'); ?>?page=wpda_dashboard" title="Data Analysis">
                            <div class="fas fa-tachometer-alt"></div>
                            <div class="label">Dashboard</div>
                        </a>
						<div class="subject" style="flex:1 1 100%">Analysis</div>
                    </div>
                    <div class="wpda-dashboard-group wpda-dashboard-group-administration">
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('admin.php'); ?>?page=wpda" title="Data Explorer">
                            <div class="fas fa-database"></div>
                            <div class="label">Explorer</div>
                        </a>
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('admin.php'); ?>?page=wpda_query_builder" title="Query Builder">
                            <div class="fas fa-tools"></div>
                            <div class="label">SQL</div>
                        </a>
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('admin.php'); ?>?page=wpda_designer" title="Data Designer">
                            <div class="fas fa-drafting-compass"></div>
                            <div class="label">Designer</div>
                        </a>
						<div class="subject" style="flex:1 1 33%">Database</div>
                    </div>
                    <div class="wpda-dashboard-group wpda-dashboard-group-publisher">
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('admin.php'); ?>?page=wpda_publisher" title="Data Publisher">
                            <div class="fas fa-address-card"></div>
                            <div class="label">Publisher</div>
                        </a>
						<div class="subject" style="flex:1 1 100%">Publish</div>
                    </div>
                    <div class="wpda-dashboard-group wpda-dashboard-group-projects">
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('admin.php'); ?>?page=wpda_wpdp" title="Data Projects">
                            <div class="fas fa-magic"></div>
                            <div class="label">Projects</div>
                        </a>
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('admin.php'); ?>?page=wpda_templates" title="Project Templates">
                            <div class="fas fa-desktop"></div>
                            <div class="label">Templates</div>
                        </a>
						<div class="subject" style="flex:1 1 50%">Projects</div>
                    </div>
                    <div class="wpda-dashboard-group wpda-dashboard-group-settings">
                        <a class="wpda-dashboard-item wpda_tooltip_icons" href="<?php echo admin_url('options-general.php?page=wpdataaccess'); ?>" title="Plugin Settings">
                            <div class="fas fa-cog"></div>
                            <div class="label">Settings</div>
                        </a>
						<a class="wpda-dashboard-item wpda_tooltip_icons" target="_blank" href="<?php echo admin_url('admin.php'); ?>?page=wpda-account" title="Manage Account">
							<div class="fas fa-user"></div>
							<div class="label">Account</div>
						</a>
						<div class="subject" style="flex:1 1 20%">Manage</div>
                    </div>
					<div class="wpda-dashboard-group wpda-dashboard-group-support">
						<a class="wpda-dashboard-item wpda_tooltip_icons" target="_blank" href="https://wpdataaccess.com/docs/documentation/getting-started/overview/" title="Online Documentation">
							<div class="fas fa-question"></div>
							<div class="label">Docs</div>
						</a>
						<a class="wpda-dashboard-item wpda_tooltip_icons" target="_blank" href="https://wordpress.org/support/plugin/wp-data-access/" title="Public Forum">
							<div class="fas fa-life-ring"></div>
							<div class="label">Forum</div>
						</a>
						<?php
						if ( wpda_freemius()->is_premium() ) {
							?>
							<a class="wpda-dashboard-item wpda_tooltip_icons" target="_blank" href="<?php echo admin_url('admin.php'); ?>?page=wpda-wp-support-forum" title="Premium Support">
								<div class="fas fa-ambulance"></div>
								<div class="label">Support</div>
							</a>
							<?php
						}
						?>
						<div class="subject" style="flex:1 1 20%">Support</div>
					</div>
                </div>
            </div>
            <?php
        }

        protected function dashboard_mobile() {
            ?>
            <div id="wpda-dashboard-mobile" style="display:none">
                <div id="wpda-dashboard-drop-down">
                    <div class="wpda_nav_toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
                    <div class="wpda_nav_title">WP Data Access</div>
                </div>
                <ul>
                    <li class="menu-item wpda-separator"><a href="<?php echo admin_url('admin.php'); ?>?page=wpda_dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="menu-item"><a href="<?php echo admin_url('admin.php'); ?>?page=wpda"><i class="fas fa-database"></i> Data Explorer</a></li>
                    <li class="menu-item"><a href="<?php echo admin_url('admin.php'); ?>?page=wpda_query_builder"><i class="fas fa-tools"></i> Query Builder</a></li>
                    <li class="menu-item wpda-separator"><a href="<?php echo admin_url('admin.php'); ?>?page=wpda_designer"><i class="fas fa-drafting-compass"></i> Data Designer</a></li>
                    <li class="menu-item wpda-separator"><a href="<?php echo admin_url('admin.php'); ?>?page=wpda_publisher"><i class="fas fa-address-card"></i> Data Publisher</a></li>
                    <li class="menu-item"><a href="<?php echo admin_url('admin.php'); ?>?page=wpda_wpdp"><i class="fas fa-magic"></i> Data Projects</a></li>
                    <li class="menu-item wpda-separator"><a href="<?php echo admin_url('admin.php'); ?>?page=wpda_templates"><i class="fas fa-desktop"></i> Project Templates</a></li>
                    <li class="menu-item"><a href="<?php echo admin_url('options-general.php?page=wpdataaccess'); ?>"><i class="fas fa-cog"></i> Settings</a></li>
                    <li class="menu-item wpda-separator"><a target="_blank" href="<?php echo admin_url('admin.php'); ?>?page=wpda-account"><i class="fas fa-user"></i> Account</a></li>
					<li class="menu-item"><a target="_blank" href="https://wpdataaccess.com/docs/documentation/getting-started/overview/"><i class="fas fa-question"></i> Online Documentation</a></li>
					<li class="menu-item"><a target="_blank" href="https://wordpress.org/support/plugin/wp-data-access/"><i class="fas fa-life-ring"></i> Support Forum</a></li>
					<?php
					if ( wpda_freemius()->is_premium() ) {
					?>
                    <li class="menu-item"><a target="_blank" href="<?php echo admin_url('admin.php'); ?>?page=wpda-wp-support-forum"><i class="fas fa-ambulance"></i> Premium Support</a></li>
					<?php
					}
					?>
                </ul>
            </div>
        <?php
        }

		protected function toolbar() {
			?>
			<div class="wpda-dashboard-toolbar">
				<div>
					<i class="fas fa-plus-circle wpda_tooltip" title="Add panel" onclick="addPanel()"></i>
					<?php 
					if ( wpda_freemius()->is_premium() ) {
					?>
						<i class="fas fa-folder-open wpda_tooltip" title="Open existing panel" onclick="openPanel()"></i>
					<?php
					}
					?>
					<span class="wpda-ison-separator"></span>
					<i class="fas fa-arrow-circle-up wpda_tooltip" title="Increase font size" onclick="increaseFont()"></i>
					<i class="fas fa-arrow-circle-down wpda_tooltip" title="Decrease font size" onclick="decreaseFont()"></i>
				</div>
				<div style="display: none">
					<i class="fas fa-dice-one wpda_tooltip" title="One column"></i>
					<i class="fas fa-dice-two wpda_tooltip" title="Two columns"></i>
					<i class="fas fa-dice-three wpda_tooltip" title="Three columns"></i>
					<i class="fas fa-dice-four wpda_tooltip" title="Four columns"></i>
				</div>
				<div>
					Data Analysis dashboard is currently beta
				</div>
			</div>
			<?php
		}

		protected function add_forms() {
			?>
			<div id="wpda-add-panel" class="wpda-add-panel" style="display: none">
				<form>
					<fieldset class="wpda_fieldset wpda_fieldset_dashboard">
						<legend>
							Add panel
						</legend>
						<div>
							<label>
								Panel name
							</label>
							<input type="text" id="wpda-add-panel-name" required/>
						</div>
						<div>
							<label>
								Panel type
							</label>
							<select id="wpda-add-panel-type">
									<option value="dbs">Database</option>
									<option value="chart" selected>Tables and charts</option>
							</select>
						</div>
						<div id="wpda-panel-dbms">
							<label>
								Select database
							</label>
							<select id="wpda-add-panel-dbms">
								<option value="wpdb" selected>WordPress database (<?php global $wpdb; echo $wpdb->dbname ?>)</option>
								<?php
								$rdbs = WPDADB::get_remote_databases();
								ksort( $rdbs );
								foreach ( $rdbs as $key => $rdb ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $key ); ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div>
							<label>
								Add to column
							</label>
							<select id="wpda-add-panel-column">
								<option value="1" selected>1</option>
								<option value="2">2</option>
							</select>
							<select id="wpda-add-panel-position">
								<option value="prepend" selected>Before</option>
								<option value="append">After</option>
							</select>
						</div>
					</fieldset>
					<div class="wpda-panel-buttons">
						<button id="wpda-add-panel-button" class="button button-primary">
							<span class="material-icons wpda_icon_on_button">check</span>
							Add
						</button>
						<button id="wpda-add-panel-button-cancel" class="button">
							<span class="material-icons wpda_icon_on_button">cancel</span>
							Cancel
						</button>
					</div>
				</div>
			</form>
			<script type="application/javascript">
				jQuery(function() {
					jQuery("#wpda-add-panel-type").on("change", function() {
						if (jQuery(this).val()==="dbs") {
							jQuery("#wpda-panel-dbms").show();
						} else {
							jQuery("#wpda-panel-dbms").hide();
						}
					});

					jQuery("#wpda-add-panel-button").on("click", function () {
						panelName = jQuery("#wpda-add-panel-name").val();
						if (panelName=="") {
							alert("Panel name is required");
						} else {
							if (jQuery("#wpda-add-panel-type").val()=="dbs") {
								addPanelDbmsToDashboard(
									"<?php echo $this->wp_nonce_add; ?>",
									panelName,
									jQuery("#wpda-add-panel-dbms").val(),
									jQuery("#wpda-add-panel-column").val(),
									jQuery("#wpda-add-panel-position").val(),
								);
							} else {
								addPanelChartToDashboard(
									"<?php echo $this->wp_nonce_add; ?>",
									panelName,
									null,
									null,
									jQuery("#wpda-add-panel-column").val(),
									jQuery("#wpda-add-panel-position").val(),
								);
							}
						}
						return false;
					});

					jQuery("#wpda-add-panel-button-cancel").on("click", function () {
						closePanel();
						return false;
					});
				});
			</script>
			<?php
			if ( wpda_freemius()->is_premium() ) {
				?>
				<div id="wpda-open-panel" class="wpda-add-panel" style="display: none">
					<form>
						<fieldset class="wpda_fieldset wpda_fieldset_dashboard">
							<legend>
								Open panel
							</legend>
							<div>
								<label>
									Panel name
								</label>
								<select id="wpda-open-panel-name"></select>
							</div>
							<div>
								<label>
									Add to column
								</label>
								<select id="wpda-open-panel-column">
									<option value="1" selected>1</option>
									<option value="2">2</option>
								</select>
								<select id="wpda-open-panel-position">
									<option value="prepend" selected>Before</option>
									<option value="append">After</option>
								</select>
							</div>
						</fieldset>
						<div class="wpda-panel-buttons">
							<button id="wpda-open-panel-button" class="button button-primary">
								<span class="material-icons wpda_icon_on_button">check</span>
								Open selected panel
							</button>
							<button id="wpda-open-delete-panel-button" class="button">
								<span class="material-icons wpda_icon_on_button">delete</span>
								Delete selected panel
							</button>
							<button id="wpda-open-panel-button-cancel" class="button">
								<span class="material-icons wpda_icon_on_button">cancel</span>
								Cancel
							</button>
						</div>
					</div>
				</form>
				<script type="application/javascript">
					jQuery(function() {
						jQuery("#wpda-open-panel-button").on("click", function () {
							loadPanel();
							return false;
						});

						jQuery("#wpda-open-delete-panel-button").on("click", function () {
							deletePanel();
							return false;
						});

						jQuery("#wpda-open-panel-button-cancel").on("click", function () {
							closePanel();
							return false;
						});
					});
				</script>
				<?php
			}
		}

		protected function columns() {
			if ( wpda_freemius()->is_free_plan() ) {
				?>
				<style>
					div.wpda_dashboard_free_message {
                        padding: 20px;
                        display: flex;
                        flex-direction: row;
                        border-bottom: 1px solid #c3c4c7;
                        background-color: #fff;
                        margin-left: -20px;
                        justify-content: space-between;
                    }
                    div.wpda_dashboard_free_message div.wpda_dashboard_free_content {
                        padding: 0 20px 0 0;
                    }
                    div.wpda_dashboard_free_message div.wpda_dashboard_free_buttons {
                        white-space: nowrap;
                        align-self: center;
                    }
					#wpda_dashboard_free_message_more {
						display: none;
					}
					@media screen and (max-width: 782px) {
                        div.wpda_dashboard_free_message {
                            padding: 10px;
                            margin-left: -10px;
                        }
                        div.wpda_dashboard_free_message div.wpda_dashboard_free_content {
                            padding: 0 10px 0 0;
                        }
                        div.wpda_dashboard_free_message div.wpda_dashboard_free_buttons {
                            white-space: normal;
                        }
                        div.wpda_dashboard_free_message div.wpda_dashboard_free_buttons a {
                            width: 100%;
							text-align: center;
                        }
					}
				</style>
				<script type="application/javascript">
					jQuery(function() {
						jQuery("#wpda_dashboard_free_message_more").on("click", function() {
							alert("The Data Analysis dashboard is currently in beta!");
							return false;
						});
					});
				</script>
				<div class="wpda_dashboard_free_message">
					<div class="wpda_dashboard_free_content">
						You are currently using the free version of WP Data Access. This version has limited support
						for Data Analysis. You can add panels to your dashboard, but you cannot save or share panels.
						Update to premium to use all dashboard features for Data Analysis.
					</div>
					<div class="wpda_dashboard_free_buttons">
						<a href='https://wpdataaccess.com/pricing/' target='_blank' class='button button-primary'>UPGRADE TO PREMIUM</a>
						<a id="wpda_dashboard_free_message_more" href='javascript:void(0)' class='button'>LEARN MORE</a>
					</div>
				</div>
				<?php
			}
            ?>
            <div class="wpda-dashboard-content">
            <?php
			for ( $i = 1; $i <= $this->number_of_columns; $i++ ) {
				?>
				<div id="wpda-dashboard-column-<?php echo $i; ?>" class="wpda-dashboard-column wpda-dashboard-column-<?php echo $this->number_of_columns; ?>">
				</div>
				<?php
			}
            ?>
            </div>
            <?php
        }

        protected function add_panels() {
			if ( wpda_freemius()->is_premium() ) {
				$dashboard           = new \WPDataAccess\Premium\WPDAPRO_Dashboard\WPDAPRO_Dashboard();
				$dashboard_widgets   = $dashboard->get_widget_list();
				$dashboard_positions = $dashboard->get_widget_positions();

				if ( sizeof( $dashboard_positions ) > 0 ) {
					foreach ( $dashboard_positions[0] as $column => $widgets ) {
						foreach ( $widgets as $widget ) {
							if ( isset( $dashboard_widgets[ $widget ] ) ) {
								$dashboard_widget = $dashboard_widgets[ $widget ];
								self::add_panel( $dashboard_widget, $column, 'append' );
							}
						}
					}
				}
			}
		}

		protected static function add_panel( $dashboard_widget, $column, $position, $widget_id = null ) {
			if ( isset ( $dashboard_widget['widgetType'], $dashboard_widget['widgetName'] ) ) {
				switch( $dashboard_widget['widgetType'] ) {
					case 'dbs':
						if ( isset( $dashboard_widget['dbsDbms'] ) ) {
							$args = [
								'name'		  => $dashboard_widget['widgetName'],
								'schema_name' => $dashboard_widget['dbsDbms'],
								'column'	  => $column,
								'position'    => $position,
								'state'		  => 'existing',
							];
							if ( null !== $widget_id ) {
								$args['widget_id'] = $widget_id;
							}
							$dbms = new WPDA_Widget_Dbms( $args );
							$dbms->add();
						}
						break;
					case 'chart':
						if ( isset(
							$dashboard_widget['chartType'],
							$dashboard_widget['userChartTypeList'],
							$dashboard_widget['chartDbs'],
							$dashboard_widget['chartSql']
						)
						) {
							$args = [
								'name'              => $dashboard_widget['widgetName'],
								'outputType'        => $dashboard_widget['chartType'],
								'userChartTypeList' => $dashboard_widget['userChartTypeList'],
								'dbs'               => $dashboard_widget['chartDbs'],
								'query'             => $dashboard_widget['chartSql'],
								'column'            => $column,
								'position'          => $position,
								'state'		  => 'existing',
							];
							if ( null !== $widget_id ) {
								$args['widget_id'] = $widget_id;
							}
							$chart = new WPDA_Widget_Google_Chart( $args );
							$chart->add();
						}
						break;
				}
			}
		}

		/**
		 * Make databases available as an options set
		 */
		protected function dashboard_js() {
			?>
			<script type="application/javascript">
				var wpda_google_chart_types = "<?php echo plugin_dir_url( __DIR__ ); ?>../assets/images/google_chart_types/";
				var wpda_wpnonce_save = "<?php echo wp_create_nonce( static::DASHBOARD_SAVE . WPDA::get_current_user_login() ); ?>";
				var wpda_wpnonce_add = "<?php echo $this->wp_nonce_add; ?>";
				var wpda_wpnonce_qb = "<?php echo wp_create_nonce( 'wpda-query-builder-' . WPDA::get_current_user_id() ); ?>";
				var wpda_wpnonce_refresh = "<?php echo wp_create_nonce( WPDA_Widget::WIDGET_REFRESH . WPDA::get_current_user_login() ); ?>";
				<?php
				$database_options = '';
				foreach ( $this->databases as $database ) {
					$selected = $this->default_database === $database ? 'selected' : '';
					$database_options .= '<option value="' . $database . '" ' . $selected . '>' . $database . '</option>';
				}
				echo "var wpda_databases = '{$database_options}';";
				?>
			</script>
			<?php
		}

		public static function save() {
			$wp_nonce = isset( $_POST['wp_nonce'] ) ? $_POST['wp_nonce'] : '';
			if ( ! wp_verify_nonce( $wp_nonce, static::DASHBOARD_SAVE . WPDA::get_current_user_login() ) ) {
				static::sent_header();
				echo static::msg('ERROR', 'Token expired');
				wp_die();
			}

			// Send empty message
			if ( wpda_freemius()->is_premium() ) {
				// Save dashboard widget positions
				$dashboard = new \WPDataAccess\Premium\WPDAPRO_Dashboard\WPDAPRO_Dashboard();
				$dashboard->save();
				wp_die();
			}
			// Placeholder: code only available in premium version
			echo '';
			wp_die();
		}

		public static function get_list() {
			$wp_nonce = isset( $_POST['wpda_wpnonce'] ) ? $_POST['wpda_wpnonce'] : '';
			if ( ! wp_verify_nonce( $wp_nonce, WPDA_Widget::WIDGET_REFRESH . WPDA::get_current_user_login() ) ) {
				static::sent_header();
				echo static::msg('ERROR', 'Token expired');
				wp_die();
			}

			if ( wpda_freemius()->is_premium() ) {
				$dashboard = new \WPDataAccess\Premium\WPDAPRO_Dashboard\WPDAPRO_Dashboard();
				$widgets   = $dashboard->get_widget_list();
				$exclude   = isset( $_POST['wpda_exclude'] ) ? $_POST['wpda_exclude'] : []; // No sanitization needed: just check value
				$options   = '';

				foreach ( $widgets as $key => $widget ) {
					if ( ! in_array( $key,  $exclude ) ) {
						$options .= "<option value='{$key}'>{$key}</option>";
					}
				}

				echo $options;
				wp_die();
			}
			// Placeholder: code only available in premium version
			echo '';
			wp_die();
		}

		public static function delete_widget() {
			$wp_nonce = isset( $_POST['wpda_wpnonce'] ) ? $_POST['wpda_wpnonce'] : '';
			if ( ! wp_verify_nonce( $wp_nonce, self::DASHBOARD_SAVE . WPDA::get_current_user_login() ) ) {
				static::sent_header();
				echo static::msg('ERROR', 'Token expired');
				wp_die();
			}

			if ( wpda_freemius()->is_premium() ) {
				if ( ! isset( $_POST['wpda_widget_name'] ) ) {
					static::sent_header();
					echo static::msg('ERROR', 'Invalid arguments');
					wp_die();
				}

				$widget_name = sanitize_text_field( $_POST['wpda_widget_name'] ); // No sanitization needed: just check value

				$dashboard   = new \WPDataAccess\Premium\WPDAPRO_Dashboard\WPDAPRO_Dashboard();
				$dashboard->del_widget( $widget_name );
				$dashboard->save_dashboard();

				static::sent_header();
				echo static::msg('SUCCESS', 'Panel deleted');
				wp_die();
			}
			// Placeholder: code only available in premium version
			echo '';
			wp_die();
		}

		public static function load_widget() {
			$wp_nonce = isset( $_POST['wpda_wpnonce'] ) ? $_POST['wpda_wpnonce'] : '';
			if ( ! wp_verify_nonce( $wp_nonce, WPDA_Widget::WIDGET_REFRESH . WPDA::get_current_user_login() ) ) {
				static::sent_header();
				echo static::msg('ERROR', 'Token expired');
				wp_die();
			}

			if ( wpda_freemius()->is_premium() ) {
				if ( ! isset(
						$_POST['wpda_panel_name'] ,
						$_POST['wpda_panel_column'],
						$_POST['wpda_panel_position'],
						$_POST['wpda_widget_id']
					)
				) {
					static::sent_header();
					echo static::msg('ERROR', 'Invalid arguments');
					wp_die();
				}

				$wpda_panel_name     = sanitize_text_field( $_POST['wpda_panel_name'] );
				$wpda_panel_column   = sanitize_text_field( $_POST['wpda_panel_column'] );
				$wpda_panel_position = sanitize_text_field( $_POST['wpda_panel_position'] );
				$wpda_widget_id      = sanitize_text_field( $_POST['wpda_widget_id'] );

				$dashboard = new \WPDataAccess\Premium\WPDAPRO_Dashboard\WPDAPRO_Dashboard();
				$widget    = $dashboard->get_widget( $wpda_panel_name );

				self::add_panel( $widget, $wpda_panel_column, $wpda_panel_position, $wpda_widget_id );
				wp_die();
			}

			// Placeholder: code only available in premium version
			echo "";
			wp_die();
		}

		public static function msg( $status, $msg ) {
			$error = [
				'status' => $status,
				'msg'    => $msg,
			];

			return json_encode( $error );
		}

		public static function sent_header( $content_type = 'application/json' ) {
			header( "Content-type: $content_type" );
			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
		}

    }

}