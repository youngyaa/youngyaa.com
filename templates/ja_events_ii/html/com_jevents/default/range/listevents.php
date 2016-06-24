<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();

echo $this->loadTemplate("body");

$this->_viewNavAdminPanel();

$this->_footer();