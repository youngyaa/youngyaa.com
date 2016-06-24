<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldMyhidden extends JFormField {
    protected $type = 'myhidden';
    public function getInput() {
            return '<input type="hidden" name="'.$this->name.'[]" value="'.$this->value.'"/>';
    }
}