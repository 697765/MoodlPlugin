<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('reportlastaccess', get_string('pluginname', 
        'report_lastaccess'), "$CFG->wwwroot/report/lastaccess/index.php",'report/lastaccess:view'));

// no report settings
$settings = null;
