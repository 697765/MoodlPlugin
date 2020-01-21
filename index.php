<?php

/* index.php */
require_once('../../config.php');
require($CFG->dirroot . '/report/lastaccess/index_form.php');
// Get the system context.
$systemcontext = context_system::instance();
$url = new moodle_url('/report/lastaccess/index.php');
// Check basic permission.
require_capability('report/lastaccess:view', $systemcontext);
// Get the language strings from language file.

$strgrade = get_string('grade', 'report_lastaccess');
$strcourse = get_string('course', 'report_lastaccess');
$strlastaccess = get_string('lastaccess', 'report_lastaccess');
$strname = get_string('name', 'report_lastaccess');
$strtitle = get_string('title', 'report_lastaccess');
// Set up page object.
$PAGE->set_url($url);
$PAGE->set_context($systemcontext);
$PAGE->set_title($strtitle);
$PAGE->set_pagelayout('report');
$PAGE->set_heading($strtitle);
// Get the courses.
$sql = "SELECT id, fullname
        FROM {course}
        WHERE visible = :visible
        AND id != :siteid
        ORDER BY fullname";

$courses = $DB->get_records_sql_menu($sql, array('visible' => 1, 'siteid' => SITEID));

$sql2 = "SELECT id, name
FROM mdl_modules
ORDER BY name";

$moduls = $DB->get_records_sql_menu($sql2);

$mform = new lastaccess_form('', array('courses' => $courses, 'moduls'=>$moduls));
echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
$mform->display();

$odabrani_modul=$_POST[modul];
$odabrani_predmet=$_POST[course];

$svi_moduli=$DB->get_records_sql('SELECT module FROM mdl_course_modules WHERE course=?',array($odabrani_predmet));

$br_ukupno=$DB->get_records_sql('SELECT COUNT(c.id) 
FROM mdl_course c 
INNER JOIN mdl_context cx ON c.id=cx.instanceid 
AND cx.contextlevel=50
INNER JOIN mdl_role_assignments ra ON cx.id=ra.contextid
INNER JOIN mdl_role r ON ra.roleid=r.id
INNER JOIN mdl_user usr ON ra.userid=usr.id
WHERE c.id=?',array($odabrani_predmet));

$bru=0;
foreach($br_ukupno as $k=>$v)
{

		$bru=$k;
	
}

$nalazi=false;
foreach($svi_moduli as $k=>$v)
{
	if($k==$odabrani_modul)
	{
		$nalazi=true;
	}
}

if($nalazi)
{
	$br_pogledanih=$DB->get_records_sql('SELECT COUNT( DISTINCT ls.userid)
										FROM mdl_user u
										JOIN mdl_logstore_standard_log ls ON u.id = ls.userid
										JOIN mdl_course c ON c.id=ls.courseid
										JOIN mdl_modules s ON s.name=ls. objecttable
										WHERE u.id != 1 AND u.id !=2 AND ls.action="viewed" AND c.id =? AND s.id=?'
										,array($odabrani_predmet,$odabrani_modul));	
	
	$br_nepogledanih=0;
	foreach($br_pogledanih as $kljuc=>$vr)
	{
		$br_nepogledanih=(double)$bru-(double)$kljuc;
	}
	
	$pieData=array(
				array('Moduli','Broj studenata'),
				array('pogledano',(double)$br_pogledanih),
				array('nepogledano',(double)$br_nepogledanih),
			);
			$jsonTable=json_encode($pieData);
}
else
{
	echo('Odabrani modul se ne nalazi u odabranom predmetu');
}	

?>
<!DOCTYPE html>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo $jsonTable ?>);
        var options = {
          title: 'Postotak studenata koji su pristupili aktivnosti',
		  is3D: true
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
		
      }
    </script>
  </head>
  <body>
	<table>
	<tr>
	<td><div id="piechart" style="width: 600px; height: 500px;"></div></td>
	</tr>
	</table>
  </body>
</html>

<?php
echo $OUTPUT->footer();
?>