<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");

class lastaccess_form extends moodleform {

    public function definition() {
        global $DB;
        global $CFG;
        $mform = & $this->_form;
        $options = array();
        $options[0] = 'Odaberi kolegij:';
        $options += $this->_customdata['courses'];
        $mform->addElement('select', 'course', get_string('course'), $options, 'align="center"');
        $mform->setType('course', PARAM_ALPHANUMEXT);
        
        $options2 = array();
        $options2[0] = 'Odaberi modul:';
        $options2 += $this->_customdata['moduls'];
        $mform->addElement('select', 'modul',$options2, $options2, 'align="center"');
        $mform->setType('modul', PARAM_ALPHANUMEXT);
        
        
        $mform->addElement('submit', 'save', get_string('display', 'report_lastaccess'), 'align="right"');
    }

}

