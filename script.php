<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Installer\Adapter\ModuleAdapter;
use Joomla\CMS\Installer\InstallerScript;

class Mod_Dwd_wettermodulInstallerScript extends InstallerScript
{
	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $minimumPhp = '8.3.0';

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $minimumJoomla = '6.0.0';

	/**
	 * A list of files to be deleted
	 *
	 * @var    array
	 * @since  7.0.0
	 */
	protected $deleteFiles = array(
		'/language/en-GB/en-GB.mod_dwd_wettermodul.ini',
		'/language/en-GB/en-GB.mod_dwd_wettermodul.sys.ini',
		'/language/de-DE/de-DE.mod_dwd_wettermodul.ini',
		'/language/de-DE/de-DE.mod_dwd_wettermodul.sys.ini',
		'/modules/mod_dwd_wettermodul/mod_dwd_wettermodul.php',
		'/modules/mod_dwd_wettermodul/helper.php',
	);

	/**
	 * Function to perform changes during postflight
	 *
	 * @param string         $type   The action being performed
	 * @param ModuleAdapter  $parent The class calling this method
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	public function postflight(string $type, ModuleAdapter $parent): void
	{
		$this->removeFiles();
	}
}
