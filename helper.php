<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - Thomas Hunziker
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

// DWD Wettervorhersage Modul
// filedata:(c) DWD - Deutscher Wetterdienst, Offenbach
// Grafiken: (c) J. Correa - www.jcorrea.es
// Modul: (c) M. Bollmann - www.stranddorf.de
// **************************************************************************
// Das Modul lädt aktuelle filedata und Vorhersagen vom FTP Server des DWD.
// Die Daten werden lokal zwischengespeichert und grafisch aufgearbeitet. 
// Die filedata der Grundversorgung dürfen frei verwendet werden, sind jedoch urheberrechtlich geschützt.
// **************************************************************************

defined('_JEXEC') or die('Restricted access');

/**
 * Helper class for DWD Wettermodul
 *
 * @since  1.0
 */
class ModDwdwetterHelper
{
	/**
	 * @var array
	 * @since 5.0.0
	 */
	public static $units = array();

	/**
	 * @param $params \Joomla\Registry\Registry Module Params
	 *
	 * @since  1.0
	 * @return array
	 */
	public static function getList($params)
	{
		$url     = 'https://opendata.dwd.de/weather/local_forecasts/poi/';
		$station = $params->get('station');

		if (is_numeric($station))
		{
			$station = str_pad($station, 5, '0', STR_PAD_LEFT);
		}

		$url .= $station . '-MOSMIX.csv';

		// Read file
		try
		{
			$response      = Joomla\CMS\Http\HttpFactory::getHttp()->get($url);
			$responseArray = explode("\n", $response->body);

			// Remove headers from array and store it separately where needed.
			$header      = str_getcsv(array_shift($responseArray), ';');
			self::$units = array_combine($header, str_getcsv(array_shift($responseArray), ';'));
			array_shift($responseArray);

			$csv = array();

			foreach ($responseArray as $row)
			{
				if (!$row)
				{
					continue;
				}

				$row       = str_getcsv($row, ';');
				$key       = $row[0] . ' ' . $row[1];
				$row       = array_combine($header, $row);
				$csv[$key] = $row;
			}
		}
		catch (\RuntimeException $exception)
		{
			\JLog::add(\JText::sprintf('JLIB_INSTALLER_ERROR_DOWNLOAD_SERVER_CONNECT', $exception->getMessage()), \JLog::WARNING, 'jerror');

			return array();
		}

		return $csv;
	}

	/**
	 * @param  array  $row
	 * @param  int    $hour
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function getIcon($row, $hour = 12)
	{
		$day = ($hour > 4 && $hour < 19);

		if ($row['ww'] < 30)
		{
			// Cloud Cover is measured in 1/8
			if ($row['N'] == 0)
			{
				$icon = ($day) ? 'sonne.png' : 'nsonne.png';
			}
			elseif ($row['N'] < 4)
			{
				$icon = ($day) ? 'heiter.png' : 'nheiter.png';
			}
			elseif ($row['N'] < 8)
			{
				$icon = ($day) ? 'bewolkt.png' : 'nbewolkt.png';
			}
			else
			{
				$icon = 'bedeckt.png';
			}
		}
		elseif ($row['ww'] < 50)
		{
			if ($row['ww'] >= 36 && $row['ww'] <= 39)
			{
				$icon = 'schnee.png';
			}
			else
			{
				$icon = 'nebel.png';
			}
		}
		elseif ($row['ww'] < 66)
		{
			if ($row['ww'] <= 61 || $row['ww'] == 66 || $row['ww'] == 68)
			{
				$icon = 'leichtregen.png';
			}
			else
			{
				$icon = 'starkregen.png';
			}
		}
		elseif ($row['ww'] < 80)
		{
			$icon = 'schnee.png';
		}
		elseif ($row['ww'] < 90)
		{
			if ($row['ww'] <= 81)
			{
				$icon = 'leichtregen.png';
			}
			elseif ($row['ww'] == 82)
			{
				$icon = 'starkregen.png';
			}
			elseif ($row['ww'] >= 83 && $row['ww'] <= 87)
			{
				$icon = 'schnee.png';
			}
			else
			{
				$icon = 'hagel.png';
			}
		}
		else
		{
			$icon = 'gewitter.png';
		}

		return $icon;
	}

	/**
	 * Returns an array with units
	 *
	 * @return array
	 *
	 * @since 5.0.0
	 */
	public static function getUnits()
	{
		return self::$units;
	}

	/**
	 * Returns selected Station.
	 *
	 * @return array
	 *
	 * @since 5.0.0
	 */
	public static function getStation($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__dwd_wetter_sites');
		$query->where('id = ' . (int) $id);

		$db->setQuery($query);
		return $db->loadObject();
	}
}
