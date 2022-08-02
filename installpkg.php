<?php

/**
 * copyright (C) 2012-2015 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html

 * */
// no direct access
defined('_JEXEC' ) or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class pkg_frontendusermanagerInstallerScript
{

	public function preflight($type, $parent) {

		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;

		if(!$this->phpCheck())
		{
			return false;
		}

		if(!$this->joomlaCheck())
		{
			return false;
		}
	}
	// TODO enable plugins
	public function update()
	{

		return true;
	}

	public function install($adapter)
	{
		return true;
	}


	public function uninstall($adapter)
	{
		return true;
	}

	protected function joomlaCheck()
	{
		$jversion = new JVersion();

		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) )
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf("PKG_FUM_INSTALLER_LOW_JOOMLA_WARNING", $this->minimum_joomla_release), 'error');
			return false;
		}

		return true;
	}

	function phpCheck()
	{
		// Only allow to install on PHP 5.6.0 or later
		if (defined('PHP_VERSION'))
		{
			$version = PHP_VERSION;
		}
		elseif (function_exists('phpversion'))
		{
			$version = phpversion();
		}
		else
		{
			$version = '5.0.0'; // We set this version as reference
		}

		if (!version_compare($version, '5.6.0', 'ge'))
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf("PKG_FUM_INSTALLER_LOW_PHP_WARNING",JText::_("PKG_FUM_INSTALLER_PACKAGE_NAME")), 'error');

			return false;
		}
		else
		{
			return true;
		}
	}

	/*
	 * enable the plugins
	 */
	function postflight($type, $parent)
	{
		jimport('joomla.filesystem.file');
		
		$filesToRemove = array( JPATH_SITE . '/components/com_frontendusermanager/models/forms/filter_wheeltime.xml',
							JPATH_SITE . '/components/com_frontendusermanager/models/forms/filter_wheeltime.xml.bak');
		foreach($filesToRemove as $extraFilter)
		{
			if(JFile::exists($extraFilter))
			{
				JFile::delete($extraFilter);
			}
		}
		
		return true;
	}
	// Manifest validation
	function getValidManifestFile($manifest)
	{
		$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
		if (!$manifestdata)
			return false;
		return $manifestdata;

	}

}
