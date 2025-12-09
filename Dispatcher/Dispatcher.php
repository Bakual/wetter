<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
 **/

namespace Bakual\Module\Wetter\Site\Dispatcher;

// DWD Wettervorhersage Modul
// Wetterdaten:(c) DWD - Deutscher Wetterdienst, Offenbach
// Grafiken: (c) J. Correa - www.jcorrea.es
// Modul: (c) M. Bollmann - www.stranddorf.de
// **************************************************************************
// Das Modul lädt aktuelle Wetterdaten und Vorhersagen vom FTP Server des DWD.
// Die Daten werden lokal zwischengespeichert und grafisch aufgearbeitet. 
// Die Wetterdaten der Grundversorgung dürfen frei verwendet werden, sind jedoch urheberrechtlich geschützt.
// **************************************************************************

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die();

/**
 * Dispatcher class for mod_latestsermons
 *
 * @since  7.0.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

	/**
	 * Returns the layout data.
	 *
	 * @return  array|false
	 *
	 * @since   7.0.0
	 */
	protected function getLayoutData(): array|false
	{
		$data = parent::getLayoutData();

		// Setting up custom logger
		Log::addLogger(
			array('text_file' => 'dwd_wetter.php'),
			Log::ALL,
			array('dwd_wetter')
		);

		$data['helper'] = $this->getHelperFactory()->getHelper('DwdWettermodulHelper');

		$data['list'] = $data['helper']->getList($data['params'], $this->getApplication());

		if (!$data['list'])
		{
			return false;
		}

		$data['units']     = $data['helper']->getUnits();
		$data['timeSteps'] = $data['list']->timeSteps;

		$timestamp    = time();
		$data['day0'] = date('Y-m-d', $timestamp);
		$data['day1'] = date('Y-m-d', $timestamp + (24 * 60 * 60));
		$data['time'] = str_pad(date('H', $timestamp), '2', '0', STR_PAD_LEFT);

		$data['days'] = array();

		if ($data['params']->get('tag0'))
		{
			$data['days'][0] = $data['day0'];
		}
		if ($data['params']->get('tag1'))
		{
			$data['days'][1] = $data['day1'];
		}
		if ($data['params']->get('tag2'))
		{
			$data['days'][2] = date('Y-m-d', $timestamp + (2 * 24 * 60 * 60));
		}
		if ($data['params']->get('tag3'))
		{
			$data['days'][3] = date('Y-m-d', $timestamp + (3 * 24 * 60 * 60));
			$data['days'][4] = date('Y-m-d', $timestamp + (4 * 24 * 60 * 60));
			$data['days'][5] = date('Y-m-d', $timestamp + (5 * 24 * 60 * 60));
		}

		$data['titel']             = $data['params']->get('titel');
		$data['farbe']             = $data['params']->get('farbe', '#3366cc');
		$data['zweitfarbe']        = $data['params']->get('zweitfarbe', '#666666');
		$data['heutehohe']         = $data['params']->get('heutehohe');
		$data['heuteluft']         = $data['params']->get('heuteluft', 1);
		$data['heuteregen']        = $data['params']->get('heuteregen', 1);
		$data['heutewindrichtung'] = $data['params']->get('heutewindrichtung', 1);
		$data['heutewind']         = $data['params']->get('heutewind', 1);
		$data['heutewindspitze']   = $data['params']->get('heutewindspitze');
		$data['datumtitel']        = $data['params']->get('datumtitel', 1);

		return $data;
	}
}
