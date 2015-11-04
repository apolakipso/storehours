<?php
namespace Craft;

/**
 * Store Hours plugin
 */
class StoreHoursPlugin extends BasePlugin
{
	function getName()
	{
		return 'Store Hours';
	}

	function getVersion()
	{
		return '1.0';
	}

	function getDeveloper()
	{
		return 'Pixel & Tonic';
	}

	function getDeveloperUrl()
	{
		return 'http://pixelandtonic.com';
	}

	public function addTwigExtension()
	{
		Craft::import('plugins.storehours.twigextensions.StoreHoursTwigExtension');
		return new StoreHoursTwigExtension();
	}
}
