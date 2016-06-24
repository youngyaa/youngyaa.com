<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Add CSS and JS
JHtml::stylesheet(JURI::BASE().'components/com_stn_events/assets/css/bootstrap-combined.min.css');
JHtml::stylesheet(JURI::BASE().'components/com_stn_events/assets/css/bootstrap-datetimepicker.min.css');
JHtml::script(JURI::BASE().'components/com_stn_events/assets/css/bootstrap-datetimepicker.min.js');

jimport('joomla.form.formfield');

class JFormFieldTimeType extends JFormField {

    protected $type = 'timeType';

    public function getInput() {
            return '<div class="input-append timetypefield" style="display:block!important;">'.
                        '<input type="text" name="'.$this->name.'[]" value="'.$this->value.'"></input>'.
                        '<span class="add-on">'.
                          '<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>'.
                        '</span>'.
                      '</div>'.
                    '<script type="text/javascript">'.
                      'jQuery(function() {'.
                        'var date = new Date();'.
                        'date.setDate(date.getDate());'.
                        'jQuery(".timetypefield").datetimepicker({'.
                          'language: "en",'.
                          'format: "hh:mm:ss",'.
                          'startDate: date,'.
                          'pick12HourFormat: false,'.
                          'pickDate: false'.
                        '});'.
                      '});'.
                    '</script>';
    }
}