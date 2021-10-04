var widgetSequenceNr = 0;
var dashboardWidgets = {};
var dashboardWidgetPosition = {};
var dashboardWidgetDeleted = [];

function increaseWidgetSequenceNr() {
    widgetSequenceNr++;
}

function addDashboardWidget(widgetId, widgetName, widgetType, obj = {}, save = true) {
    // Create widget with default properties
	dashboardWidgets[widgetId] = {};
	dashboardWidgets[widgetId].widgetName = widgetName;
    dashboardWidgets[widgetId].widgetType = widgetType;

    for (var prop in obj) {
        // Add custom properties
        dashboardWidgets[widgetId][prop] = obj[prop];
    }

    // Update dashboard
    if (save) {
        saveDashBoard();
    } else {
        saveWidgetPositions();
    }
}

function delDashboardWidget(widgetId) {
	delete dashboardWidgets[widgetId];
}

function saveWidgetPositions() {
    dashboardWidgetPosition = {};
    for (i=1; i<=jQuery("div.wpda-dashboard-content div.wpda-dashboard-column").length; i++) {
        if (jQuery("div.wpda-dashboard-content #wpda-dashboard-column-" + i + " .wpda-widget").length>0) {
            dashboardWidgetPosition[i] = jQuery("div.wpda-dashboard-content #wpda-dashboard-column-" + i + " .wpda-widget").map(function() {
                return jQuery(this).data("name");
            }).get();
        } else {
            dashboardWidgetPosition[i] = [];
        }
    }
}

function saveDashBoard() {
    saveWidgetPositions();
    jQuery.ajax({
        type: "POST",
        url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_save_dashboard",
        data: {
            wp_nonce: wpda_wpnonce_save,
            wpda_widgets: dashboardWidgets,
            wpda_positions: dashboardWidgetPosition,
            wpda_deleted: dashboardWidgetDeleted,
        }
    }).done(
        function(data) {
            dashboardWidgetDeleted = [];
        }
    ).fail(
		function (msg) {
			console.log("WP Data Access error (saveDashBoard):", msg);
		}
	);
}

function toggleDashboard() {
    if (jQuery("#screen-meta").css("display")==="block") {
        jQuery("#wpda-dashboard").hide();
        jQuery("#wpda-dashboard-mobile").hide();
    } else {
        showMenu();
    }
}

function toggleMenu() {
    if (jQuery("#wpda-dashboard-mobile ul").is(":visible")) {
        jQuery("#wpda-dashboard-mobile ul").hide();
    } else {
        jQuery("#wpda-dashboard-mobile ul").show();
    }
}

function showMenu() {
    if (jQuery("#wpcontent").width()<780) {
        jQuery("#wpda-dashboard").hide();
        jQuery("#wpda-dashboard-mobile").fadeIn(400);
    } else {
        if (jQuery("#wpcontent").width()<840) {
            wd = 38;
            fs = 17;
            tx = 7;
        } else if (jQuery("#wpcontent").width()<960) {
            wd = 46;
            fs = 22;
            tx = 7;
        } else if (jQuery("#wpcontent").width()<1080) {
            wd = 54;
            fs = 24;
            tx = 8;
        } else {
            wd = 62;
            fs = 28;
            tx = 9;
        }

        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-dashboard").css("width",
            (wd*jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-dashboard .wpda-dashboard-item").length) + "px");
        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-administration").css("width",
            (wd*jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-administration .wpda-dashboard-item").length) + "px");
        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-publisher").css("width",
            (wd*jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-publisher .wpda-dashboard-item").length) + "px");
        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-projects").css("width",
            (wd*jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-projects .wpda-dashboard-item").length) + "px");
        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-settings").css("width",
            (wd*jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-settings .wpda-dashboard-item").length) + "px");
        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-support").css("width",
            (wd*jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group-support .wpda-dashboard-item").length) + "px");

        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group .wpda-dashboard-item").css("width", wd + "px");
        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group .wpda-dashboard-item .label").css("font-size", tx + "px");
        jQuery("#wpda-dashboard .wpda-dashboard .wpda-dashboard-group .wpda-dashboard-item .fas").css("font-size", fs + "px");

        jQuery("#wpda-dashboard-mobile").hide();
        jQuery("#wpda-dashboard").fadeIn(400);

        jQuery(".wpda_tooltip").tooltip({
            tooltipClass: "wpda_tooltip_css",
        });
        jQuery(".wpda_tooltip_icons").tooltip({
            tooltipClass: "wpda_tooltip_icons_css",
            position: {
                my: "center bottom-18",
                at: "center top",
                using: function (position, feedback) {
                    jQuery(this).css(position);
                    jQuery("<div>")
                    .addClass("arrow")
                    .addClass(feedback.vertical)
                    .addClass(feedback.horizontal)
                    .appendTo(this);
                }
            }
        });
    }
}

function setDashboardWidth() {
    jQuery(".wpda-dashboard-content").width(jQuery("#wpcontent").width() - jQuery("#wpcontent").css("padding-left").replace("px", "") + 20);
    showMenu();
}

function resizeFont(fontSize) {
    jQuery(".wpda-widget").css("font-size", fontSize + "px");
}

function increaseFont() {
    fontSize = parseInt(jQuery(".wpda-widget").css("font-size").replace("px", ""));
    resizeFont(fontSize+1);
}

function decreaseFont() {
    fontSize = parseInt(jQuery(".wpda-widget").css("font-size").replace("px", ""));
    resizeFont(fontSize-1);
}

function toggleIcon(elem) {
    if (elem.html().indexOf("fa-caret-right")>-1) {
        elem.html(elem.html().replace("fa-caret-right","fa-caret-down"));
    } else {
        elem.html(elem.html().replace("fa-caret-down","fa-caret-right"));
    }
}

function getDbmsInfo(targetElement, wp_nonce, wpda_schemaname) {
    jQuery.ajax({
        type: "POST",
        url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_widget_dbms_refresh",
        data: {
            wp_nonce: wp_nonce,
            wpda_action: "info",
            wpda_schemaname: wpda_schemaname
        }
    }).done(
        function(data) {
            targetElement.closest(".wpda-widget").find(".hostname").html(data['hostname']);
            targetElement.closest(".wpda-widget").find(".post").html(data['post']);
            targetElement.closest(".wpda-widget").find(".ssl").html(data['ssl']);
            targetElement.closest(".wpda-widget").find(".version").html(data['version'] + " " + data['version_comment']);
            targetElement.closest(".wpda-widget").find(".compiled").html(data['version_compile_os'] + " (" + data['version_compile_machine'] + ")");
            targetElement.closest(".wpda-widget").find(".uptime").html(data['uptime']);

            targetElement.closest(".wpda-widget").find(".basedir").html(data['basedir']);
            targetElement.closest(".wpda-widget").find(".datadir").html(data['datadir']);
            targetElement.closest(".wpda-widget").find(".plugin_dir").html(data['plugin_dir']);
            targetElement.closest(".wpda-widget").find(".tmpdir").html(data['tmpdir']);

            targetElement.closest(".wpda-widget").find(".log_error").html(data['log_error']);
            targetElement.closest(".wpda-widget").find(".general_log").html(data['general_log_file'] + "[" + data['general_log'] + "]");
            targetElement.closest(".wpda-widget").find(".slow_query").html(data['slow_query_log_file'] + "[" + data['slow_query_log'] + "]");
        }
    );
}

function getDbmsVars(targetElement, wp_nonce, wpda_action, wpda_schemaname, force = false) {
    if (force || targetElement.html()==="") {
        // Load variables
        targetElement.html("Loading...");
        jQuery.ajax({
            type: "POST",
            url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_widget_dbms_refresh",
            data: {
                wp_nonce: wp_nonce,
                wpda_action: wpda_action,
                wpda_schemaname: wpda_schemaname
            }
        }).done(
            function(data) {
                if (data.status==="ERROR") {
                    targetElement.html('ERROR: ' + data.msg);
                } else {
                    targetElement.html('');
                    vars = jQuery('<table class="wpda-widget-dbms"></table>');
                    targetElement.append(vars);
                    varsHead = jQuery('<thead><tr><th>Variable</th><th>Value</th></tr></thead>');
                    vars.append(varsHead);
                    varsBody = jQuery('<tbody></tbody>');
                    vars.append(varsBody);
                    for (var prop in data) {
                        varsBody.append('<tr><td>' + prop + "</td><td>" + data[prop] + "</td></tr>");
                    }
                    // Adjust header column width
                    jQuery(targetElement).find("thead th:first-child").css("width",
                        jQuery(targetElement).find("tbody td:first-child").css("width"));
                }
            }
        );
    }
}

function addPanelDbmsToDashboard(wp_nonce, panel_name, panel_dbms, panel_column, column_position) {
    increaseWidgetSequenceNr();
    jQuery.ajax({
        type: "POST",
        url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_widget_dbms_add",
        data: {
            wp_nonce: wp_nonce,
            wpda_panel_name: panel_name,
            wpda_panel_dbms: panel_dbms,
            wpda_panel_column: panel_column,
            wpda_column_position: column_position,
            wpda_widget_sequence_nr: widgetSequenceNr,
        }
    }).done(
        function(data) {
            jQuery("#wpbody-content").append(data);
            closePanel();

            setTimeout(function() {
                obj = {};
                obj.dbsDbms = panel_dbms;
                addDashboardWidget(
                    widgetSequenceNr,
                    panel_name,
                    'dbs',
                    obj
                );
            }, 500);
        }
    );
}

function addPanelChartToDashboard(wp_nonce, panel_name, panel_dbs, panel_query, panel_column, column_position) {
    increaseWidgetSequenceNr();
    jQuery.ajax({
        type: "POST",
        url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_widget_chart_add",
        data: {
            wp_nonce: wp_nonce,
            wpda_panel_name: panel_name,
            wpda_panel_dbs: panel_dbs,
            wpda_panel_query: panel_query,
            wpda_panel_column: panel_column,
            wpda_column_position: column_position,
            wpda_widget_sequence_nr: widgetSequenceNr,
        }
    }).done(
        function(data) {
            jQuery("#wpbody-content").append(data);
            closePanel();
        }
    );
}

function removePanelFromDashboard(e) {
    var dialogHtml = " \
        <p><strong>Remove panel from dashboard?</strong></p> \
        <div><span style='display:inline-block;width:55px'><strong>Delete</strong> </span>= Remove panel from dashboard and database </div> \
        <div><span style='display:inline-block;width:55px'><strong>Keep</strong> </span>= Remove panel from dashboard and keep in database </div> \
        <div><span style='display:inline-block;width:55px'><strong>Cancel</strong> </span>= Cancel action </div> \
    ";
    var dialog = jQuery("<div/>").html(dialogHtml).dialog({
        title: "Test",
        width: "max-content",
        buttons: {
            "Delete": function() {
                delDashboardWidget(jQuery(e).data('id'));
                dashboardWidgetDeleted.push(jQuery(e).data("name"));
                removePanelFromDashboardAction(e);
                dialog.dialog("close");
            },
            "Keep":  function() {
                delDashboardWidget(jQuery(e).data('id'));
                removePanelFromDashboardAction(e);
                dialog.dialog("close");
            },
            "Cancel":  function() {
                dialog.dialog("close");
            }
        }
    });
}

function loadPanel() {
    increaseWidgetSequenceNr();
    jQuery.ajax({
        type: "POST",
        url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_widget_load_panel",
        data: {
            wpda_wpnonce: wpda_wpnonce_refresh,
            wpda_panel_name: jQuery("#wpda-open-panel-name").val(),
            wpda_panel_column: jQuery("#wpda-open-panel-column").val(),
            wpda_panel_position: jQuery("#wpda-open-panel-position").val(),
            wpda_widget_id: widgetSequenceNr,
        }
    }).done(
        function(msg) {
            if (typeof msg === 'string') {
                jQuery("#wpbody-content").append(msg);
                setTimeout( function() {
                    saveDashBoard();
                }, 500);
            } else {
                if (msg.status==="ERROR" && msg.msg!==undefined) {
                    alert(msg.msg);
                }
            }

            closePanel();
        }
    ).fail(
        function (msg) {
            console.log("WP Data Access error (loadPanel):", msg);
        }
    );
}

function removePanelFromDashboardAction(e) {
    id = jQuery(e).attr('id');
    jQuery(e).remove(); // Remove panel
    jQuery("." + id).remove(); // Remove panel script blocks
    saveDashBoard();
}

function addPanel() {
    closePanel();
    jQuery("#wpda-add-panel-name").val("");
    jQuery("#wpda-add-panel-type").change();
    jQuery("#wpda-add-panel").show();
}

function openPanel() {
    closePanel();

    var exclude = [];
    for (var position in dashboardWidgetPosition) {
        for (var i=0; i<dashboardWidgetPosition[position].length; i++) {
            exclude.push(dashboardWidgetPosition[position][i]);
        }
    }

    // Update listbox
    jQuery.ajax({
        method: 'POST',
        url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_dashboard_list",
        data: {
            wpda_wpnonce: wpda_wpnonce_refresh,
            wpda_exclude: exclude
        }
    }).done(
        function(msg) {
            if (typeof msg === 'string') {
                if (msg.startsWith("<option")) {
                    jQuery("#wpda-open-panel-name option").remove();
                    jQuery("#wpda-open-panel-name").append(msg);

                    jQuery("#wpda-open-panel").show();
                } else {
                    alert("No panels found");
                }
            } else {
                if (msg.status==="ERROR" && msg.msg!==undefined) {
                    alert(msg.msg);
                }
            }
        }
    ).fail(
        function (msg) {
            console.log("WP Data Access error (openPanel):", msg);
        }
    );
}

function closePanel() {
    jQuery("#wpda-add-panel").hide();
    jQuery("#wpda-open-panel").hide();
}

function deletePanel() {
    panelName = jQuery("#wpda-open-panel-name").val();
    if (confirm('Delete panel "' + panelName + '"? This cannot be undone!')) {
        jQuery.ajax({
            method: 'POST',
            url: wpda_pluginvars.wpda_ajaxurl + "?action=wpda_widget_delete",
            data: {
                wpda_wpnonce: wpda_wpnonce_save,
                wpda_widget_name: panelName
            }
        }).done(
            function(msg) {
                if (msg.status===undefined) {
                    alert("Error deleting panel. Please refresh page and try again...");
                } else {
                    alert(msg.msg);
                    if (msg.status==="SUCCESS") {
                        jQuery("#wpda-open-panel-name option[value='" + panelName + "']").remove();
                        if (jQuery("#wpda-open-panel-name option").length===0) {
                            closePanel();
                        }
                    }
                }
            }
        ).fail(
            function (msg) {
                console.log("WP Data Access error (deletePanel):", msg);
            }
        );
    }
}

function getSQLFromQueryBuilder(wpnonce, widget_id) {
	url = location.pathname + '?action=wpda_query_builder_open_sql';
	jQuery.ajax({
		method: 'POST',
		url: url,
		data: {
			wpda_wpnonce: wpnonce,
			wpda_exclude: ""
		}
	}).done(
		function (msg) {
			if (!Array.isArray(msg.data)) {
				// Show queries
				list = jQuery("<ul/>");
				for (var queryName in msg.data) {
					dbs = msg.data[queryName].schema_name;
					qry = msg.data[queryName].query;

					query = jQuery(`
					<div class="wpda-query-select">
						<div class="wpda-query-select-title ui-widget-header">
							${queryName}
							<span class="fas fa-copy wpda-query-select-title-copy"></span>
						</div>
						<div class="wpda-query-select-content">
							${qry}
						</div>
					</div>
					`);
					listitem = jQuery("<li/>").attr("data-dbs", dbs).attr("data-sql", qry);
					listitem.append(query);
					
					list.append(listitem);
				}
				dialog = jQuery("<div class='wpda-query'/>").attr("title", "Select from Query Builder");
				dialog.append(list);
				dialog.dialog();

				jQuery(".wpda-query-select-title-copy").on("click", function() {
					selectedQuery = jQuery(this).closest("li").data("sql");
					selectedDbs = jQuery(this).closest("li").data("dbs");

					jQuery("#wpda_chart_dbs_" + widget_id).val(selectedDbs);
					jQuery("#wpda_chart_sql_" + widget_id).val(selectedQuery);
					
					jQuery(this).closest('.ui-dialog-content').dialog('close'); 
				});
			} else {
				// No queries found
			}
		}
	).fail(
		function (msg) {
			console.log("WP Data Access error (getSQLFromQueryBuilder):", msg);
		}
	);
}

jQuery(function() {
    jQuery("#show-settings-link").on("click", function() {
        setTimeout(function() { toggleDashboard(); }, 500);
    });

    jQuery(window).on("resize", function() { setDashboardWidth() });
    setDashboardWidth();

    jQuery('.wpda-dashboard-column').sortable({
        connectWith: '.wpda-dashboard-column',
        cursor: 'move',
        opacity: 0.4,
        change: function(event, ui) {
            ui.placeholder.css({visibility: 'visible', background : '#ccc'});
        },
        update: function(event, ui) {
            saveDashBoard();
        }
    });
});
