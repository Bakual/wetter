<?php
/**
 * @package        Wettermodul
 * @author         Thomas Hunziker <admin@bakual.net>
 * @copyright  (C) 2019 - Thomas Hunziker
 * @license        http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

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
	protected $minimumJoomla = '4.0.0';
	/**
	 * Old version before updating
	 *
	 * @var    string
	 * @since  5.0.2
	 */
	private $oldRelease;

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string                      $type    'install', 'update' or 'discover_install'
	 * @param   JInstallerAdapterComponent  $parent  Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since ?
	 */
	public function preflight($type, $parent)
	{
		// Storing old release number for process in postflight
		if (strtolower($type) == 'update')
		{
			$manifest         = $this->getItemArray('manifest_cache', '#__extensions', 'element', JFactory::getDbo()->quote($this->extension));
			$this->oldRelease = $manifest['version'];
		}

		return parent::preflight($type, $parent);
	}

	/**
	 * method to update the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since 5.0.0
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '5.0.2', '<'))
		{
			$this->deleteFiles = array(
				'/language/en-GB/en-GB.mod_dwd_wettermodul.ini',
				'/language/en-GB/en-GB.mod_dwd_wettermodul.sys.ini',
				'/language/de-DE/de-DE.mod_dwd_wettermodul.ini',
				'/language/de-DE/de-DE.mod_dwd_wettermodul.sys.ini',
			);
			$this->removeFiles();
		}
	}
}
