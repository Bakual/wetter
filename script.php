<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - Thomas Hunziker
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;

class Mod_Dwd_wettermodulInstallerScript extends InstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $extension = 'mod_dwd_wettermodul';
	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $minimumPhp = '5.6.0';
	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $minimumJoomla = '3.8.0';

	/**
	 * method to update the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since 5.0.0
	 */
	public function update($parent)
	{
		$manifest = $this->getItemArray('manifest_cache', '#__extensions', 'element', Factory::getDbo()->quote($this->extension));

		if (version_compare($manifest['version'], '5.0.0', '<'))
		{
			$this->deleteFiles = array(
				'/language/en-GB/en-GB.mod_dwd_wettermodul.ini',
				'/language/de-DE/de-DE.mod_dwd_wettermodul.ini',
			);
			$this->removeFiles();
		}
	}
}