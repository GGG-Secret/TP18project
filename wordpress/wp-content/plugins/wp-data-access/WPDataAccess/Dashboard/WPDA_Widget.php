<?php

namespace WPDataAccess\Dashboard {

	use WPDataAccess\WPDA;

    abstract class WPDA_Widget  {

		const WIDGET_ADD     = 'WPDA_WIDGET_ADD';
    	const WIDGET_REFRESH = 'WPDA_WIDGET_REFRESH';

    	protected static $widget_sequence_nr = 0;

        protected $column      = 1;

		protected $can_share   = false;
        protected $can_refresh = false;
		protected $has_setting = false;
        
		protected $name        = 'No name';
		protected $title       = 'No title';
        protected $content     = 'No content';
        
		protected $wp_nonce    = null;
        protected $widget_id   = 0;
        
		protected $position    = 'append';
		protected $state       = null;

        public function __construct( $args = [] ) {
            wp_enqueue_script( 'jquery-ui-widget' );

            if ( isset( $args['name'] ) ) {
                $this->name = $args['name'];
            }

            if ( isset( $args['column'] ) ) {
                $this->column = $args['column'];
            }

			if ( isset( $args['title'] ) ) {
                $this->title = $args['title'];
            }

            if ( isset( $args['content'] ) ) {
                $this->content = $args['content'];
            }

			if ( isset( $args['position'] ) && 'prepend' === $args['position'] ) {
				$this->position = 'prepend';
			}

			if ( isset( $args['widget_id'] ) ) {
				$this->widget_id = $args['widget_id']; // Used to add widgets via ajax
			} else {
				$this->widget_id = ++self::$widget_sequence_nr; // Used to add widgets on page load
			}

			$this->state = isset( $args['state'] ) ? $args['state'] : 'new';

			$this->wp_nonce  = wp_create_nonce( static::WIDGET_REFRESH . WPDA::get_current_user_login() );
        }

        protected function container() {
			ob_start();

			$widget = $this->html();
			?>
			<script type="application/javascript" class="wpda-widget-<?php echo $this->widget_id; ?>">
				jQuery(function() {
					var widget = jQuery("<?php echo str_replace( "\\", "\\\\", str_replace( ["\n", '"'], '', $widget ) ); ?>");
					jQuery("#wpda-dashboard-column-<?php echo $this->column; ?>").<?php echo $this->position; ?>(widget);
					jQuery("#wpda-widget-<?php echo $this->widget_id; ?>").data("name", "<?php echo $this->name; ?>" );

					widget.find(".wpda-widget-close").on("click", function() {
						removePanelFromDashboard(jQuery(this).closest('.wpda-widget'));
					});
				});
			</script>
			<?php
			$this->js();

			return ob_get_clean();
		}

		protected function html() {
			$share   = $this->can_share ? "<i class='fas fa-share-alt wpda-widget-share' title='Share'></i> &nbsp;" : '';
			$setting = $this->has_setting ? "<i class='fas fa-cog wpda-widget-setting' title='Settings'></i> &nbsp;" : '';
			$refresh = $this->can_refresh ? "<i class='fas fa-sync-alt wpda-widget-refresh' title='Refresh panel'></i> &nbsp;" : '';
			$widget  = <<<EOF
                <div id="wpda-widget-{$this->widget_id}" data-id="{$this->widget_id}" class="wpda-widget ui-widget">
                    <div class="wpda-widget-content">
                        <div class="ui-widget-header">
                            <span>{$this->name}</span>
                            <span class="icons">
								{$share}
								{$setting}
								{$refresh}
								<i class='fas fa-window-close wpda-widget-close' title='Close panel'></i>
							</span>
                        </div>
                        <div class="ui-widget-content">
                            {$this->content}
                        </div>
                    </div>
                </div>
EOF;

			return $widget;
		}

		abstract protected function js(); // Method to add custom JavaScript code

		// Add widget to dashboard
		public function add() {
			echo $this->container();
			?>
			<script type="application/javascript">
				jQuery(function() {
					increaseWidgetSequenceNr();
				});
			</script>
			<?php
		}

		abstract public static function widget();

		public static function ajax_widget() {
			static::ajax_verify_nonce(static::WIDGET_ADD);
			static::widget();
		}

		abstract public static function refresh();

		public static function ajax_refresh() {
			static::ajax_verify_nonce(static::WIDGET_REFRESH);
			static::refresh();
		}

		protected static function ajax_verify_nonce($action) {
			$wp_nonce = isset( $_REQUEST['wp_nonce'] ) ? $_REQUEST['wp_nonce'] : '';
			if ( ! wp_verify_nonce( $wp_nonce, $action . WPDA::get_current_user_login() ) ) {
				static::sent_header();
				echo static::msg('ERROR', 'Token expired');
				wp_die();
			}
		}

		protected static function msg( $status, $msg ) {
			$error = [
				'status' => $status,
				'msg'    => $msg,
			];

			return json_encode( $error );
		}

		protected static function sent_header( $content_type = 'application/json' ) {
			header( "Content-type: $content_type" );
			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
		}

	}

}
