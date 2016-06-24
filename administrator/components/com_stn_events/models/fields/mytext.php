<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldMytext extends JFormField {
    protected $type = 'mytext';
    public function getInput() {
            return '<input type="text" name="'.$this->name.'[]" value="'.$this->value.'"/>';
    }
}