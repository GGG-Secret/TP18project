<?php

namespace WPDataAccess\Promo {

	class WPDA_Promo {

		const PROMO_ENABLED = false;
		const PROMOS = [
			'data-publisher' => [
				[
					'<strong>Save as buttons</strong> (to <strong>PDF</strong>, <strong>Excel</strong>, <strong>CSV</strong>, <strong>SQL</strong>, <strong>printer</strong> and <strong>clipboard</strong>)',
					''
				],
				[
					'<strong>Custom buttons</strong> (requires javascript knowledge)',
					''
				],
				[
					'<strong>Advanced search</strong> (fulltext, column specific, start with empty table)',
					''
				],
				[
					'Interactive filtering on column level with the <strong>Search Builder</strong>',
					''
				],
				[
					'<strong>Search panes</strong> to activate predefined filters on mouse click',
					''
				],
				[
					'<strong>Row grouping</strong> for statistical analysis',
					''
				],
				[
					'<strong>Google geolocation integration</strong> (requires Google Maps API key)',
					''
				],
				[
					'<strong>Show/hide published columns</strong>',
					''
				],
			]
		];
		protected $promo_type = null;

		public function __construct( $promo_type ) {
			$this->promo_type = $promo_type;
		}

		public function get_link() {
			if ( ! self::PROMO_ENABLED ) {
				return;
			}
			?>
			<form
				method="post"
				action="javascript:void(0)"
				style="display: inline-block; vertical-align: baseline;"
			>
				<div>
					<button type="submit"
							onclick="jQuery('#wpda_data_publisher_promo').show()"
							class="page-title-action wpda_tooltip"
							title="See all premium features for the Data Publisher to make your publications even more professional"
					>
						<span class="material-icons wpda_icon_on_button">favorite</span>
						<?php echo __( 'Get more features', 'wp-data-access' ); ?>
					</button>
				</div>
			</form>
			<?php
		}

		public function get_container() {
			if ( ! self::PROMO_ENABLED ) {
				return;
			}
			?>
			<div id="wpda_data_publisher_promo" style="display:none">
				<fieldset class="wpda_fieldset" style="position:relative">
					<legend>
						Premium Data Publisher features
					</legend>
					<div style="display: inline-grid;grid-template-columns: auto auto;justify-content: space-evenly;width: 100%;">
						<ul style="padding:20px">
							<?php
							foreach ( self::PROMOS[ $this->promo_type ] as $promo ) {
								?>
								<li><span class="dashicons dashicons-yes"></span> <?php echo $promo[0]; ?></li>
								<?php
							}
							?>
						</ul>
						<div style="padding:20px;text-align:center;margin-top: auto;margin-bottom: auto;">
							<a href="https://wpdataaccess.com/pricing/" target="_blank">
								<span class="button button-primary" style="font-weight:bold;width:180px">
									<span class="material-icons wpda_icon_on_button">favorite</span>
									&nbsp;&nbsp;GET PREMIUM
								</span>
							</a>
							<p>&nbsp;</p>
							<p>
								and <strong>many more features</strong> for other plugin
							</p>
							<p>
								tools for just <strong>one small price</strong>
							</p>
						</div>
					</div>
					<a class="wpda-icon-close" onclick="jQuery('#wpda_data_publisher_promo').hide()">
						<span class="material-icons wpda_icon_on_button">closed</span></a>
					</a>
				</fieldset>
			</div>
			<?php
		}

	}

}