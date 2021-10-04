<?php

namespace WPDataAccess\Dashboard {

	use WPDataAccess\Connection\WPDADB;

	class WPDA_Widget_Google_Chart extends WPDA_Widget {

		protected $outputType        = ['table'];
		protected $userChartTypeList = [];
		protected $dbs               = null;
		protected $query             = null;
		protected $columns           = [];
		protected $rows              = [];

		public function __construct( $args = [] ) {
			parent::__construct( $args );

			// TODO
			// $this->can_share   = true;
			// $this->can_refresh = true;
			$this->has_setting = true;

			if ( wpda_freemius()->is_premium() ) {
				if (
					isset(
						$args['outputType'],
						$args['userChartTypeList'],
						$args['dbs'],
						$args['query']
					)
				) {
					$this->outputType        = $args['outputType'];
					$this->userChartTypeList = $args['userChartTypeList'];
					$this->dbs               = $args['dbs'];
					$this->query             = $args['query'];
				}
			}

			// Create container
			$this->content  = "
				<div class='wpda-chart-container'>
					<div class='wpda_widget_chart_selection' style='display: none'>
						<select id='wpda_widget_chart_selection_{$this->widget_id}'></select>
					</div>
					<div id='wpda_widget_container_{$this->widget_id}'>
					</div>
				</div>
			";
		}

		protected function js() {
			?>
			<script type='application/javascript' class="wpda-widget-<?php echo esc_attr( $this->widget_id ); ?>">
				jQuery(function() {
					var widget = jQuery("#wpda-widget-<?php echo esc_attr( $this->widget_id ); ?>");
					widget.find(".wpda-widget-setting").on("click", function() {
						chartSettings("<?php echo esc_attr( $this->widget_id ); ?>");
					});

					jQuery("#wpda_widget_chart_selection_<?php echo esc_attr( $this->widget_id ); ?>").on("change", function() {
						switchChartType(jQuery(this).val(), "<?php echo esc_attr( $this->widget_id ); ?>");
					});

					<?php
					if ( 'new' === $this->state ) {
					?>
						chartSettings("<?php echo esc_attr( $this->widget_id ); ?>");
					<?php
					}
					if ( wpda_freemius()->is_premium() ) {
						if ( 'new' !== $this->state ) {
							$obj = new \stdClass();
							$obj->chartType         = $this->outputType;
							$obj->userChartTypeList = $this->userChartTypeList;
							$obj->chartDbs          = esc_attr( $this->dbs );
							$obj->chartSql          = wp_unslash( $this->query );
					?>
							var waitForGoogleCharts = setInterval(function() {
								if (googleChartsLoaded) {
									clearInterval(waitForGoogleCharts);

									var obj = <?php echo json_encode( $obj ); ?>;
									addDashboardWidget(
										<?php echo esc_attr( $this->widget_id ); ?>,
										"<?php echo esc_attr( $this->name ); ?>",
										"chart",
										obj,
										false
									);
									getChartData(<?php echo esc_attr( $this->widget_id ); ?>);
								}
							}, 100);
					<?php
						}
					}
					?>
				});
			</script>
			<?php
		}

		protected static function get_data( $dbs, $query ) {
			$return_value = [
				'cols'  => [],
				'rows'  => [],
				'error'	=> '',
			];

			$wpdadb = WPDADB::get_db_connection( $dbs );
			if ( null === $wpdadb ) {
				$return_value['error'] = 'Database connection failed';
				return $return_value;
			}

			$suppress = $wpdadb->suppress_errors( true );

			// Get column info
			$wpdadb->query("
				create temporary table widget as select * from (
					{$query}
				) resultset limit 0
			");
			if ( '' !== $wpdadb->last_error ) {
				$return_value['error'] = $wpdadb->last_error;
				return $return_value;
			}

			$cols        = $wpdadb->get_results("desc widget", 'ARRAY_A');
			$cols_return = [];
			foreach ($cols as $col) {
				$cols_return[] = [
					'id'    => $col['Field'],
					'label' => $col['Field'],
					'type'  => self::google_charts_type( $col['Type'] ),
				];
			}

			// Perform query
			$rows        = $wpdadb->get_results($query, 'ARRAY_A');
			$rows_return = [];
			foreach ($rows as $row) {
				$val = [];
				$index = 0;
				foreach ( $row as $col) {
					if ( 'number' === $cols_return[ $index ]['type'] ) {
						if ( is_int( $col ) ) {
							$col =  intval( $col );
						} else {
							$col = floatval( $col );
						}
					} elseif (
						'date' === $cols_return[ $index ]['type'] ||
						'datetime' === $cols_return[ $index ]['type']
					) {
						$year  = substr( $col, 0, 4);
						$month = substr( $col, 5, 2);
						$day   = substr( $col, 8, 2);
						if ('datetime' === $cols_return[ $index ]['type']) {
							$hrs   = substr( $col, 11, 2);
							$min   = substr( $col, 14, 2);
							$sec   = substr( $col, 17, 2);
							$col   = "Date($year,$month,$day,$hrs,$min,$sec)";
						} else {
							$col   = "Date($year,$month,$day)";
						}
					} elseif (
						'timeofday' === $cols_return[ $index ]['type']
					) {
						$hrs   = substr( $col, 0, 2);
						$min   = substr( $col, 3, 2);
						$sec   = substr( $col, 6, 2);
						$col   = "[$hrs,$min,$sec,0]";
					}
					$val[] = [
						'v' => $col
					];
					$index++;
				}
				$rows_return[] = [
					'c' => $val
				];
			}

			$wpdadb->suppress_errors( $suppress );

			$return_value['cols'] = $cols_return;
			$return_value['rows'] = $rows_return;

			return $return_value;
		}

		public static function widget() {
			$panel_name         = isset( $_REQUEST['wpda_panel_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_panel_name'] ) ) : ''; // input var okay.;
			$panel_dbs          = isset( $_REQUEST['wpda_panel_dbs'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_panel_dbs'] ) ) : ''; // input var okay.;
			$panel_query        = isset( $_REQUEST['wpda_panel_query'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_panel_query'] ) ) : ''; // input var okay.;
			$panel_column       = isset( $_REQUEST['wpda_panel_column'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_panel_column'] ) ) : '1'; // input var okay.;
			$column_position    = isset( $_REQUEST['wpda_column_position'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_column_position'] ) ) : 'prepend'; // input var okay.;
			$widget_sequence_nr = isset( $_REQUEST['wpda_widget_sequence_nr'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpda_widget_sequence_nr'] ) ) : '1'; // input var okay.;

			$wdg = new WPDA_Widget_Google_Chart([
				'outputtype' => ['Table'],
				'name'		 => $panel_name,
				'dbs'		 => $panel_dbs,
				'query'		 => $panel_query,
				'column'	 => $panel_column,
				'position'	 => $column_position,
				'widget_id'	 => $widget_sequence_nr,
			]);

			static::sent_header('text/html; charset=UTF-8');
			echo $wdg->container();
			wp_die();
		}

		public static function refresh() {
			if (
				! isset( 
					$_REQUEST['wpda_action'], 
					$_REQUEST['wpda_dbs'], 
					$_REQUEST['wpda_query']
				)
			) {
				static::sent_header();
				echo static::msg('ERROR', 'Invalid arguments');
				wp_die();
			}

			switch( $_REQUEST['wpda_action' ] ) {
				case 'get_data':
					$dbs   = sanitize_text_field( wp_unslash( $_REQUEST['wpda_dbs'] ) ); // input var okay.
					$query = wp_unslash( $_REQUEST['wpda_query'] ); // input var okay.

					static::sent_header();
					echo json_encode( static::get_data( $dbs, $query ) );
					wp_die();
					break;
				default:
					static::sent_header();
					echo static::msg('ERROR', 'Invalid arguments');
					wp_die();
			}

		}

		public static function google_charts_type( $data_type ) {
			$type = explode( '(', $data_type );
			switch( $type[0] ) {
				case 'tinyint':
				case 'smallint':
				case 'mediumint':
				case 'int':
				case 'bigint':
				case 'float':
				case 'double':
				case 'decimal':
				case 'year':
					return 'number';

				case 'date':
					return 'date';

				case 'datetime':
				case 'timestamp':
					return 'datetime';

				case 'time':
					// TODO Timeofday returns an error in Google Charts
					// Workaround = return time as string
					// return 'timeofday';

				default:
					return 'string';
			}
		}

	}

}