<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
 **/

namespace Bakual\Module\Wetter\Site\Helper;

// DWD Wettervorhersage Modul
// filedata:(c) DWD - Deutscher Wetterdienst, Offenbach
// Grafiken: (c) J. Correa - www.jcorrea.es
// Modul: (c) M. Bollmann - www.stranddorf.de
// **************************************************************************
// Das Modul lädt aktuelle filedata und Vorhersagen vom FTP Server des DWD.
// Die Daten werden lokal zwischengespeichert und grafisch aufgearbeitet.
// Die filedata der Grundversorgung dürfen frei verwendet werden, sind jedoch urheberrechtlich geschützt.
// **************************************************************************

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;
use ZipArchive;

defined('_JEXEC') or die('Restricted access');

/**
 * Helper class for DWD Wettermodul
 *
 * @since  1.0
 */
class DwdWettermodulHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * @param $params \Joomla\Registry\Registry Module Params
	 * @param $app
	 *
	 * @return \stdClass|null
	 * @since  1.0
	 */
	public function getList(Registry $params, $app): ?\stdClass
	{
		$url     = 'https://opendata.dwd.de/weather/local_forecasts/mos/MOSMIX_L/single_stations/';
		$station = $params->get('station');

		if (is_numeric($station))
		{
			$station = str_pad($station, 5, '0', STR_PAD_LEFT);
		}

		$url .= $station . '/kml/MOSMIX_L_LATEST_' . $station . '.kmz';

		// Read file
		try
		{
			$httpFactory = new HttpFactory();
			$response    = $httpFactory->getHttp()->get($url);
			$tmpFolder   = $app->get('tmp_path');
			$tmpFile     = $tmpFolder . '/mod_dwd_wettermodul.kmz';

			if (File::write($tmpFile, $response->getBody()))
			{
				$zip = new ZipArchive;

				if (is_dir($tmpFolder . '/mod_dwd_wettermodul_kmz'))
				{
					Folder::delete($tmpFolder . '/mod_dwd_wettermodul_kmz');
				}

				if ($zip->open($tmpFile) === true)
				{
					$zip->extractTo($tmpFolder . '/mod_dwd_wettermodul_kmz');
					$zip->close();
				}
			}
			else
			{
				Log::add('Writing file "' . $tmpFile . '" failed!', Log::WARNING, 'dwd_wetter');
			}

			$kmlFile     = Folder::files($tmpFolder . '/mod_dwd_wettermodul_kmz')[0];
			$xml         = simplexml_load_file($tmpFolder . '/mod_dwd_wettermodul_kmz/' . $kmlFile);
			$xmlDocument = $xml->children('kml', true)->Document;
			$timeSteps   = $xmlDocument->ExtendedData->children('dwd', true)->ProductDefinition->ForecastTimeSteps->children('dwd', true);
			$timeSteps   = array_flip((array) $timeSteps->TimeStep);

			// $location = (string) $xmlDocument->Placemark->description;
			$dwd = $xmlDocument->Placemark->ExtendedData->children('dwd', true);
		}
		catch (\RuntimeException $exception)
		{
			Log::add(Text::sprintf('JLIB_INSTALLER_ERROR_DOWNLOAD_SERVER_CONNECT', $exception->getMessage()), Log::WARNING, 'dwd_wetter');

			return null;
		}

		$forecast = new \stdClass;

		foreach ($dwd as $i)
		{
			$name            = (string) $i['elementName'];
			$value           = preg_split('/\s+/', $i->value, -1, PREG_SPLIT_NO_EMPTY);
			$forecast->$name = $value;
		}

		$forecast->timeSteps = $timeSteps;

		return $forecast;
	}

	/**
	 * Returns the weather icon for a condition
	 *
	 * @param object $list
	 * @param int    $index
	 * @param int    $hour
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public function getIcon(object $list, int $index, int $hour = 12): string
	{
		$day  = ($hour > 4 && $hour < 19);
		$code = $list->ww[$index];

		switch ($code)
		{
			case 00:
				$icon = ($day) ? 'sonne.png' : 'nsonne.png';
				break;
			case 01:
				$icon = ($day) ? 'heiter.png' : 'nheiter.png';
				break;
			case 02:
				$icon = ($day) ? 'bewolkt.png' : 'nbewolkt.png';
				break;
			case 03:
				$icon = 'bedeckt.png';
				break;
			case 45:
			case 49:
				$icon = 'nebel.png';
				break;
			case 51:
			case 53:
			case 55:
			case 56:
			case 57:
			case 61:
			case 66:
			case 80:
			case 81:
				$icon = 'leichtregen.png';
				break;
			case 63:
			case 65:
			case 67:
			case 82:
				$icon = 'starkregen.png';
				break;
			case 68:
			case 69:
			case 71:
			case 73:
			case 75:
			case 83:
			case 84:
			case 85:
			case 86:
				$icon = 'schnee.png';
				break;
			case 95:
				$icon = 'gewitter.png';
				break;
		}

		return $icon;
	}

	/**
	 * Returns an array with units
	 *
	 * @return array
	 *
	 * @see   https://opendata.dwd.de/weather/lib/MetElementDefinition.xml
	 *
	 * @since 5.0.0
	 */
	public function getUnits(): array
	{
		$units = array(
			'TTT'   => 'K',
			'Td'    => 'K',
			'TX'    => 'K',
			'TN'    => 'K',
			'DD'    => 'Â°',
			'FF'    => 'm/s',
			'FX1'   => 'm/s',
			'FX3'   => 'm/s',
			'FXh'   => 'm/s',
			'RR1c'  => 'kg/m2',
			'RR3c'  => 'kg/m2',
			'RRS1c' => 'kg/m2',
			'RRS3c' => 'kg/m2',
			'ww'    => '',
			'W1W2'  => '',
			'N'     => '%',
			'Neff'  => '%',
			'N05'   => '%',
			'Nl'    => '%',
			'Nm'    => '%',
			'Nh'    => '%',
			'PPPP'  => 'Pa',
			'T5cm'  => 'K',
			'RadS3' => 'kJ/m2',
			'Rad1h' => 'kJ/m2',
			'RadL3' => 'kJ/m2',
			'VV'    => 'm',
			'SunD1' => 's',
			'FXh25' => '%',
			'FXh40' => '%',
			'FXh55' => '%',
			'wwM'   => '%',
			'wwM6'  => '%',
			'wwMh'  => '%',
			'Rh00'  => '%',
			'R602'  => '%',
			'Rh02'  => '%',
			'Rd02'  => '%',
			'Rh10'  => '%',
			'R650'  => '%',
			'Rh50'  => '%',
			'Rd50'  => '%',
			'TG'    => 'K',
			'TM'    => 'K',
			'DRR1'  => 's',
			'wwZ'   => '%',
			'wwD'   => '%',
			'wwC'   => '%',
			'wwT'   => '%',
			'wwL'   => '%',
			'wwS'   => '%',
			'wwF'   => '%',
			'wwP'   => '%',
			'VV10'  => '%',
			'E_TTT' => 'K',
			'E_FF'  => 'm/s',
			'E_DD'  => 'Â°',
			'E_Td'  => 'K',
			'RR6c'  => 'kg/m2',
			'R600'  => '%',
			'R101'  => '%',
			'R102'  => '%',
			'R103'  => '%',
			'R105'  => '%',
			'R107'  => '%',
			'R110'  => '%',
			'R120'  => '%',
			'SunD'  => 's',
			'RSunD' => '%',
			'PSd00' => '%',
			'PSd30' => '%',
			'PSd60' => '%',
			'RRad1' => '%',
			'PEvap' => 'kg/m2',
			'R130'  => '%',
			'R150'  => '%',
			'RR1o1' => '%',
			'RR1w1' => '%',
			'RR1u1' => '%',
			'wwD6'  => '%',
			'wwC6'  => '%',
			'wwT6'  => '%',
			'wwP6'  => '%',
			'wwL6'  => '%',
			'wwF6'  => '%',
			'wwS6'  => '%',
			'wwZ6'  => '%',
			'wwMd'  => '%',
			'FX625' => '%',
			'FX640' => '%',
			'FX655' => '%',
			'wwDh'  => '%',
			'wwCh'  => '%',
			'wwTh'  => '%',
			'wwPh'  => '%',
			'wwLh'  => '%',
			'wwFh'  => '%',
			'wwSh'  => '%',
			'wwZh'  => '%',
			'R610'  => '%',
			'RRhc'  => 'kg / m2',
			'ww3'   => '',
			'RRL1c' => 'kg / m2',
			'Rd00'  => '%',
			'Rd10'  => '%',
			'RRdc'  => 'kg / m2',
			'Nlm'   => '%',
			'wwPd'  => '%',
			'H_BsC' => 'm',
			'wwTd'  => '%',
			'E_PPP' => 'Pa',
			'SunD3' => 's',
			'WPc11' => '',
			'WPc31' => '',
			'WPc61' => '',
			'WPch1' => '',
			'WPcd1' => '',
		);

		return $units;
	}

	/**
	 * Returns selected Station.
	 *
	 * @param $id
	 *
	 * @return \stdClass
	 * @since 5.0.0
	 */
	public function getStation($id): \stdClass
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__dwd_wetter_sites');
		$query->where('id = ' . (int) $id);

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Returns direction in N/E/S/W instead of Grad.
	 *
	 * @param $grad integer
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public function getDirection(int $grad): string
	{
		$index = (int) round($grad / 22.5);
		$index = ($index === 16) ? 0 : $index;

		return Text::_('MOD_DWD_WETTERMODUL_DIRECTION_' . $index);
	}

	/**
	 * Logs an error into logs/dwd_wetter.php
	 *
	 * @param $forecastIndex
	 * @param $day0
	 * @param $time
	 * @param $timeSteps
	 * @param $list
	 * @param $layout
	 *
	 * @return void
	 *
	 * @since 5.1.2
	 */
	public function logError($forecastIndex, $day0, $time, $timeSteps, $list, $layout): void
	{
		$errorstring = 'Temperature not found at "' . $day0 . ' ' . $time . ':00".';
		$errorstring .= "\nSelected ForecastIndex was " . $forecastIndex . '", Layout in use was "' . $layout . '".';
		$errorstring .= "\n'" . $day0 . 'T18:00:00.000Z\' was ';
		$errorstring .= isset($timeSteps[$day0 . 'T18:00:00.000Z']) ? 'true' : 'false';
		$errorstring .= "\n'" . $day0 . 'T' . $time . ':00:00.000Z\' was ';
		$errorstring .= isset($timeSteps[$day0 . 'T' . $time . ':00:00.000Z']) ? 'true' : 'false';

		$i           = 0;
		$errorstring .= "\ntimeSteps:";
		foreach ($timeSteps as $key => $value)
		{
			$errorstring .= "\n" . $key . ' => ' . $value;
			if ($i > 24)
			{
				break;
			}
			$i++;
		}

		$i           = 0;
		$errorstring .= "\nTTT:";
		foreach ($list->TTT as $key => $value)
		{
			$errorstring .= "\n" . $key . ' => ' . $value;
			if ($i > 24)
			{
				break;
			}
			$i++;
		}

		$i           = 0;
		$errorstring .= "\nTX:";
		foreach ($list->TX as $key => $value)
		{
			$errorstring .= "\n" . $key . ' => ' . $value;
			if ($i > 24)
			{
				break;
			}
			$i++;
		}

		Log::add($errorstring, Log::WARNING, 'dwd_wetter');
	}
}
