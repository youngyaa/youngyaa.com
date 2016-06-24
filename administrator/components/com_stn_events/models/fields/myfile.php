<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldMyfile extends JFormField {
    protected $type = 'myfile';
    public function getInput() {
            return '<input type="file" name="'.$this->name.'[]"/>';
    }
}