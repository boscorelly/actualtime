<?php

$USEDBREPLICATE= 1;
$DBCONNECTION_REQUIRED= 0;

include ("../../../../inc/includes.php");

$report= new PluginReportsAutoReport(__('ActualTimeUser'));
//Filtro fecha
new PluginReportsDateIntervalCriteria($report, 'glpi_tickets.closedate', __("Close date"));
//Filtro user
$choices = [0=>__('Technician'), 1 => __('Requester')];
$filter_active=new PluginReportsArrayCriteria($report, 'glpi_tickets_users.type', _('Group by'), $choices);

$report->displayCriteriasForm();
$report->setColumns([new PluginReportsColumnLink('user_id', __('User'), 'User', ['with_navigate' => true]),
							new PluginReportsColumnTimestamp('duration', __("Total duration")),
                     new PluginReportsColumnTimestamp('totalduration', "ActualTime - ".__("Total duration")),
                     new PluginReportsColumnTimestamp('diff', __("Duration Diff", "actiontime")),
                     new PluginReportsColumn('diffpercent', __("Duration Diff", "actiontime")." (%)")
                  ]
               );
if ($filter_active->getParameterValue()==1) {
	$query = "SELECT glpi_tickets_users.users_id as user_id,";
	$group =" AND glpi_tickets_users.type=1  GROUP BY glpi_tickets_users.users_id";
}else{
	$query = "SELECT glpi_tickettasks.users_id_tech as user_id,";
	$group =" GROUP BY glpi_tickettasks.users_id_tech";
}
$report->delCriteria('glpi_tickets_users.type');
$query.="SUM(glpi_tickettasks.actiontime)as duration,
			SUM(actual_actiontime)as totalduration,
			(SUM(glpi_tickettasks.actiontime)-SUM(actual_actiontime))as diff,
					concat(round(( (SUM(glpi_tickettasks.actiontime)-SUM(actual_actiontime))/SUM(actual_actiontime) * 100 ),2),'%')as diffpercent
			FROM glpi_plugin_actualtime_tasks
			RIGHT JOIN glpi_tickettasks ON glpi_tickettasks.id=glpi_plugin_actualtime_tasks.tasks_id
			INNER JOIN glpi_tickets ON glpi_tickets.id=glpi_tickettasks.tickets_id
			INNER JOIN glpi_tickets_users ON glpi_tickets_users.tickets_id=glpi_tickets.id
			WHERE status=6 ";
$query .= $report->addSqlCriteriasRestriction();
$query .=$group;

$report->setSqlRequest($query);
$report->execute();