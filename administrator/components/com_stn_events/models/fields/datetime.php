<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Add CSS and JS
JHtml::stylesheet(JURI::BASE().'components/com_stn_events/assets/css/bootstrap-combined.min.css');
JHtml::stylesheet(JURI::BASE().'components/com_stn_events/assets/css/bootstrap-datetimepicker.min.css');
JHtml::script(JURI::BASE().'components/com_stn_events/assets/css/bootstrap-datetimepicker.min.js');

jimport('joomla.form.formfield');

class JFormFieldDateTime extends JFormField {

    protected $type = 'DateTime';

    public function getInput() {
            return '<div class="well">'.
                    '<div id="'.$this->id.'" class="input-append">'.
                        '<input data-format="MM/dd/yyyy HH:mm:ss PP" type="text" name="'.$this->name.'" value="'.$this->value.'"></input>'.
                        '<span class="add-on">'.
                          '<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>'.
                        '</span>'.
                      '</div>'.
                    '</div>'.
                    '<script type="text/javascript">'.
                      'jQuery(function() {'.
                        'var date = new Date();'.
                        'date.setDate(date.getDate());'.
                        'jQuery("#'.$this->id.'").datetimepicker({'.
                          'language: "en",'.
                          'format: "yyyy-MM-dd",'.
                          'startDate: date,'.
                          'pick12HourFormat: false,'.
                          'pickTime: false'.
                        '});'.
                      '});'.
                    '</script>';
    }
}