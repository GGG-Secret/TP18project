const CHART_TYPES = [
	"BarChart",
	"ColumnChart",
	"ComboChart",
	// "DonutChart",
	// "Gauge",
	"Histogram",
	"LineChart",
	"PieChart",
	"Table"
];
const CHART_DEFAULT_WIDTH = 500;
const CHART_DEFAULT_HEIGHT = 300;

var googleChartsLoaded = false;
var googleChartsObjects = {};
var cachedChartData = {};

function getChartData(widgetId) {
	wpda_dbs = dashboardWidgets[widgetId].chartDbs;
	wpda_query = dashboardWidgets[widgetId].chartSql;
	jQuery.ajax({
        type: "POST",
        url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_widget_chart_refresh",
        data: {
            wp_nonce: wpda_wpnonce_refresh,
			wpda_action: 'get_data',
			wpda_dbs: wpda_dbs,
			wpda_query: wpda_query,
        }
    }).done(
        function(data) {
			if (data.error!=='') {
				alert("ERROR: " + data.error);
			} else {
				jQuery("#wpda_widget_container_" + widgetId).show();
				setUserChartSelection(widgetId);

				jQuery("#wpda_widget_chart_selection_" + widgetId + " option").remove();
				jQuery.each(dashboardWidgets[widgetId].chartType, function (i, item) {
					jQuery("#wpda_widget_chart_selection_" + widgetId).append(jQuery("<option/>", {
						value: item,
						text : item
					}));
				});

				if (dashboardWidgets[widgetId].chartType.length>1) {
					chartType = dashboardWidgets[widgetId].chartType[0];
				} else {
					chartType = dashboardWidgets[widgetId].chartType[0];
				}

				createChart(
					chartType,
					widgetId,
					data.cols,
					data.rows,
					chartArguments(widgetId)
				);

				jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda-settings").remove();
			}
        }
    );
}

function chartArguments(widgetId) {
	var obj = {};

	if (dashboardWidgets[widgetId]!==undefined && dashboardWidgets[widgetId].title!==undefined) {
		obj.title = dashboardWidgets[widgetId].title;
	}

	if (dashboardWidgets[widgetId]!==undefined && dashboardWidgets[widgetId].chartWidth!==undefined) {
		obj.width = dashboardWidgets[widgetId].chartWidth;
	} else {
		obj.width = "*";
	}

	if (dashboardWidgets[widgetId]!==undefined && dashboardWidgets[widgetId].chartHeight!==undefined) {
		obj.height = dashboardWidgets[widgetId].chartHeight;
	} else {
		obj.height = CHART_DEFAULT_HEIGHT ;
	}

	return obj;
}

function createChart(outputType, widgetId, columns, rows, options) {
	cachedChartData[widgetId] = new google.visualization.DataTable({
		cols: columns,
		rows: rows
	});
	var element = document.getElementById("wpda_widget_container_" + widgetId);
	googleChartsObjects[widgetId] = new google.visualization[outputType](element);
	googleChartsObjects[widgetId].draw(cachedChartData[widgetId], options);
}

function switchChartType(outputType, widgetId) {
	var element = document.getElementById("wpda_widget_container_" + widgetId);
	googleChartsObjects[widgetId] = new google.visualization[outputType](element);
	googleChartsObjects[widgetId].draw(
		cachedChartData[widgetId],
		chartArguments(widgetId)
	);
}

function setUserChartSelection(widgetId, action = null) {
	if (action==="show") {
		jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda_widget_chart_selection").show();
	} else if (action==="hide") {
		jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda_widget_chart_selection").hide();
	} else {
		if (dashboardWidgets[widgetId].chartType.length>1) {
			// Allow user to switch between multiple chart types
			jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda_widget_chart_selection").show();
		} else {
			// Hide selection if only one chart type is selected
			jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda_widget_chart_selection").hide();
		}
	}
}

function chartTypeOption(id) {
	return `
		<li>
			<input type="checkbox" id="${id}"/>
			<label for="${id}">
				<img src="${wpda_google_chart_types}${id}.png"/>
			</label>
		</li>`;
}

function chartTypeOptions(chartTypes) {
	list = "";
	for (var i=0; i<chartTypes.length; i++) {
		list += chartTypeOption(chartTypes[i]);
	}
	return list;
}

function chartSettings(widgetId) {
	var query = "";
	var chartTypes = CHART_TYPES;
	var widgetName = "";
	if (dashboardWidgets[widgetId]!==undefined) {
		if (dashboardWidgets[widgetId].chartSql!==null) {
			query = dashboardWidgets[widgetId].chartSql;
		}

		if (dashboardWidgets[widgetId].userChartTypeList!==undefined) {
			chartTypes = dashboardWidgets[widgetId].userChartTypeList;
		}

		if (dashboardWidgets[widgetId].widgetName!==undefined) {
			widgetName = dashboardWidgets[widgetId].widgetName;
		}
	} else {
		// Widget name not yet available on insert: grap title from widget header
		widgetName = jQuery("#wpda-widget-" + widgetId).data("name");
	}

	if (jQuery("#wpda_widget_container_" + widgetId).is(":visible")) {
		jQuery("#wpda_widget_container_" + widgetId).hide();
		setUserChartSelection(widgetId, "hide");

		jQuery("#wpda_widget_container_" + widgetId).parent().append(`
			<div class="wpda-settings">
				<div class="wpda-dashboard-chart-settings">
					<fieldset class="wpda_fieldset">
						<legend>
							SQL Query
						</legend>
						<div>
							<select id="wpda_chart_dbs_${widgetId}" class="wpda_chart_dbs">
								${wpda_databases}
							</select>
							<button class="button wpda_insert_query_builder" title="Insert SQL from Query Builder"><i class="fas fa-tools"></i> Query Builder</button>
						</div>
						<div>
							<textarea id="wpda_chart_sql_${widgetId}" class="wpda_chart_sql">${query}</textarea>
						</div>
					</fieldset>
					<fieldset class="wpda_fieldset">
						<legend>
							Table or chart type
						</legend>
						<ul class="wpda_google_chart_types">
							${chartTypeOptions(chartTypes)}
						</ul>
					</fieldset>
					<fieldset class="wpda_fieldset wpda_fieldset_chart_layout">
						<legend>
							Layout
						</legend>
						<div>
							<label for="wpda_chart_panel_name_${widgetId}">
								Title
							</label>
							<input type="text" 
								   id="wpda_chart_panel_name_${widgetId}" 
								   value="${widgetName}" 
								   title="Cannot be changed" 
								   readonly
							/>
						</div>
						<div>
							<label for="wpda_chart_title_${widgetId}">
								Title
							</label>
							<input type="text"
								   id="wpda_chart_title_${widgetId}" 
								   value="" 
								   placeholder="A title is optional..."
							/>
						</div>
						<div>
							<label for="wpda_chart_width_${widgetId}">
								Width
							</label>
							<input type="radio" 
								   value="*" 
								   id="wpda_chart_width_select_${widgetId}" 
								   name="wpda_chart_width_select_${widgetId}" 
								   checked
							/>
							<input type="text" 
								   value="Fit to container width (100%)" 
								   title="Cannot be changed" 
								   readonly
							/>
						</div>
						<div>
							<label></label>
							<input type="radio" value="val" name="wpda_chart_width_select_${widgetId}" />
							<input type="number" id="wpda_chart_width_${widgetId}" value="${CHART_DEFAULT_WIDTH}" /> px
						</div>
						<div>
							<label for="wpda_chart_height_${widgetId}">
								Height
							</label>
							<input type="number" id="wpda_chart_height_${widgetId}" value="${CHART_DEFAULT_HEIGHT}" /> px
						</div>
					</fieldset>
					<div class="wpda-dashboard-chart-settings-buttons">
						<button class="button button-primary wpda-button-ok">OK</button>
						<button class="button wpda-button-cancel">Cancel</button>
					</div>
				</div>
			</div>
		`);

		if (dashboardWidgets[widgetId]!==undefined && dashboardWidgets[widgetId].chartDbs!==null) {
			jQuery("#wpda_chart_dbs_" + widgetId).val(dashboardWidgets[widgetId].chartDbs);
		}
		if (dashboardWidgets[widgetId]!==undefined) {
			for (var i=0; i<dashboardWidgets[widgetId].chartType.length; i++) {
				jQuery("#" + dashboardWidgets[widgetId].chartType[i]).prop("checked", true);
			}
		}
	
		jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda_insert_query_builder").on("click", function() {
			getSQLFromQueryBuilder(wpda_wpnonce_qb, widgetId);
		})

		jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda-button-ok").on("click", function() {
			if (jQuery("#wpda_chart_sql_" + widgetId).val()!=='') {
				obj = {};
				obj.chartType = jQuery("#wpda-widget-" + widgetId + " ul.wpda_google_chart_types input[type='checkbox']:checked").map(function() {
					return jQuery(this).attr("id");
				}).get();
				obj.userChartTypeList = jQuery("#wpda-widget-" + widgetId + " ul.wpda_google_chart_types input[type='checkbox']").map(function() {
					return jQuery(this).attr("id");
				}).get();
				obj.chartDbs = jQuery("#wpda_chart_dbs_" + widgetId).val();
				obj.chartSql = jQuery("#wpda_chart_sql_" + widgetId).val();
				obj.chartTitle = jQuery("#wpda_chart_title_" + widgetId).val();
				if (jQuery("#wpda_chart_width_select_" + widgetId).is(":checked")) {
					obj.chartWidth = '*';
				} else {
					obj.chartWidth = jQuery("#wpda_chart_width_" + widgetId).val();
				}
				obj.chartHeight = jQuery("#wpda_chart_height_" + widgetId).val();

				addDashboardWidget(
					widgetId,
					jQuery("#wpda-widget-" + widgetId).data("name"),
					"chart",
					obj
				);

				getChartData(widgetId);
			} else {
				alert("Please enter a valid query");
			}
		});

		jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda-button-cancel").on("click", function() {
			if (dashboardWidgets[widgetId] == undefined) {
				removePanelFromDashboardAction(jQuery(this).closest('.wpda-widget'));
			} else {
				jQuery("#wpda_widget_container_" + widgetId).parent().find(".wpda-settings").remove();
				jQuery("#wpda_widget_container_" + widgetId).show();
				setUserChartSelection(widgetId);
			}
		});

		jQuery(".wpda_google_chart_types").sortable({
			connectWith: ".wpda_google_chart_types",
			cursor: "move",
			opacity: 0.4,
			change: function(event, ui) {
				ui.placeholder.css({visibility: "visible", background : "#ccc"});
			}
		});
	}
}

jQuery(function() {
	google.charts.load(
		"current", {
			"packages": [
				"table",
				"corechart",
				"gauge"
			]
		}
	);

	google.charts.setOnLoadCallback(function() {
		googleChartsLoaded = true;
	});
});
