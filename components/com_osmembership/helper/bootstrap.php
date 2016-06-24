<?php

/**
 * @version        1.6.4
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2013 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
class OSMembershipHelperBootstrap
{
	/**
	 * Twitter bootstrap version, default 2
	 * @var string
	 */
	protected $bootstrapVersion;

	/**
	 * The class mapping to map between twitter bootstrap 2 and twitter bootstrap 3
	 * @var string
	 */
	protected static $classMaps;

	/**
	 * Constructor, initialize the classmaps array
	 *
	 * @param int $bootstrapVersion
	 */
	public function __construct($bootstrapVersion)
	{
		if (empty($bootstrapVersion))
		{
			$bootstrapVersion = 2;
		}

		$this->bootstrapVersion = $bootstrapVersion;

		// The static class map
		if ($bootstrapVersion == 2)
		{
			self::$classMaps = array(
				'row-fluid'       => 'row-fluid',
				'span1'           => 'span1',
				'span2'           => 'span2',
				'span3'           => 'span3',
				'span4'           => 'span4',
				'span5'           => 'span5',
				'span6'           => 'span6',
				'span7'           => 'span7',
				'span8'           => 'span8',
				'span9'           => 'span9',
				'span10'          => 'span10',
				'span11'          => 'span11',
				'span12'          => 'span12',
				'btn'             => 'btn',
				'btn-mini'        => 'btn-mini',
				'btn-small'       => 'btn-small',
				'btn-large'       => 'btn-large',
				'visible-phone'   => 'visible-phone',
				'visible-tablet'  => 'visible-tablet',
				'visible-desktop' => 'visible-desktop',
				'hidden-phone'    => 'hidden-phone',
				'hidden-tablet'   => 'hidden-tablet',
				'hidden-desktop'  => 'hidden-desktop',
				'control-group'   => 'control-group',
				'input-prepend'   => 'input-prepend',
				'input-append'   => 'input-append',
				'add-on'          => 'add-on',
				'img-polaroid'    => 'img-polaroid',
				'control-label'   => 'control-label',
				'controls'        => 'controls'
			);
		}
		else
		{
			self::$classMaps = array(
				'row-fluid'       => 'row',
				'span1'           => 'col-md-1',
				'span2'           => 'col-md-2',
				'span3'           => 'col-md-3',
				'span4'           => 'col-md-4',
				'span5'           => 'col-md-5',
				'span6'           => 'col-md-6',
				'span7'           => 'col-md-7',
				'span8'           => 'col-md-8',
				'span9'           => 'col-md-9',
				'span10'          => 'col-md-10',
				'span11'          => 'col-md-11',
				'span12'          => 'col-md-12',
				'btn'             => 'btn btn-default',
				'btn-mini'        => 'btn-xs',
				'btn-small'       => 'btn-sm',
				'btn-large'       => 'btn-lg',
				'visible-phone'   => 'visible-xs',
				'visible-tablet'  => 'visible-sm',
				'visible-desktop' => 'visible-md visible-lg',
				'hidden-phone'    => 'hidden-xs',
				'hidden-tablet'   => 'hidden-sm',
				'hidden-desktop'  => 'hidden-md hidden-lg',
				'control-group'   => 'form-group',
				'input-prepend'   => 'input-group',
				'input-append '   => 'input-group',
				'add-on'          => 'input-group-addon',
				'img-polaroid'    => 'img-thumbnail',
				'control-label'   => 'col-md-3 control-label',
				'controls'        => 'col-md-9'
			);
		}
	}

	/**
	 * Get the mapping of a given class
	 *
	 * @param $class The input class
	 *
	 * @return string The mapped class
	 */
	public function getClassMapping($class)
	{
		if (isset(self::$classMaps[$class]))
		{
			return self::$classMaps[$class];
		}

		// Handle icon class
		if (strpos($class, 'icon-') !== false)
		{
			if ($this->bootstrapVersion == 2)
			{
				return $class;
			}
			else
			{
				$icon = substr($class, 5);

				return 'glyphicon glyphicon-' . $icon;
			}
		}

		// Not found, this class is for twitter bootstrap 3 only

		if ($this->bootstrapVersion == 3)
		{
			return $class;
		}
		else
		{
			return null;
		}

	}
}