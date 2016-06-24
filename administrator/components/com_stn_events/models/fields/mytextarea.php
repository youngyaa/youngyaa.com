<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldMytextarea extends JFormField {
    protected $type = 'mytextarea';
    public function getInput() {
            return '<textarea name="'.$this->name.'[]">'.$this->value.'</textarea>';
    }
}