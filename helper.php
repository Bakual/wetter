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
	 * @var \Joomla\Registry\Registry Module Params
	 * @since  4.0
	 */
	static $params;

	/**
	 * @param $params \Joomla\Registry\Registry Module Params
	 *
	 * @since  1.0
	 * @return array
	 */
	public static function getList($params)
	{
		self::$params = $params;

		$days = array();

		if ($params->get('tag0', 1))
		{
			$days[] = 0;
		}

		if ($params->get('tag1'))
		{
			$days[] = 1;
		}

		if ($params->get('tag2'))
		{
			$days[] = 2;
		}

		if ($params->get('tag3'))
		{
			$days[] = 3;
		}

		$data = array();
		
		new JBuffer;

		foreach ($days as $day)
		{
			// Get filedata
			$filedata = self::getFile($day);
			$filedata = html_entity_decode($filedata);

			// Process filedata
			if (!$day)
			{
				$data[$day] = self::parseFiledataCurrent($filedata);
			}
			else
			{
				$data[$day] = self::parseFiledataFuture($filedata);
			}
		}

		return $data;
	}

	/**
	 * Fetchs the file from the DWD FTP server
	 *
	 * @param $day  integer  Day to fetch
	 *
	 * @return string  file content
	 *
	 * @since 1.0
	 */
	private static function getFile($day)
	{
		if (!$day)
		{
			$folder = '/gds/gds/specials/observations/tables/germany/';
			$files  = self::$ftp->listNames($folder);
			sort($files);

			// Take the raw one
			$file = array_pop($files);

			if (strpos($file, '_0645_') !== false)
			{
				$file = array_pop($files);
			}

			unset($files);
		}
		else
		{
			$folder = '/gds/gds/specials/forecasts/tables/germany/';
			$file   = 'Daten_Deutschland';

			switch ($day)
			{
				case 1:
					$file .= '_morgen_spaet';
					break;
				case 2:
					$file .= '_uebermorgen_spaet';
					break;
				case 3:
					$file .= '_Tag4_spaet';
					break;
			}

			$file .= '_HTML';
		}

		// Read file
		self::$ftp->read($folder . $file, $filedata);

		return utf8_encode($filedata);
	}

	/**
	 * @param string $filedata The file content to parse
	 *
	 * @return array  $data  Array of data
	 *
	 * @since 4.0
	 */
	private static function parseFiledataCurrent($filedata)
	{
		$position = array();
		$data     = array();
		$glieder  = self::FetchHeaders($filedata);

		$k = 0;

		for ($count = 1; $count < 21; $count++)
		{
			$position[$count] = 20;
		}

		foreach ($glieder as $j)
		{
			switch (mb_strtolower($j))
			{
				case 'höhe':
					$position[0] = $k;
					break;
				case 'luftd.':
					$position[1] = $k;
					break;
				case 'temp.':
					$position[2] = $k;
					break;
				case 'rr1':
				case 'rr30':
					$position[3] = $k;
					break;
				case 'dd':
					$position[4] = $k;
					break;
				case 'ff':
					$position[5] = $k;
					break;
				case 'fx':
					$position[6] = $k;
					break;
				case 'wetter+wolken':
					$position[7] = $k;
					$position[8] = $k + 1;
					break;
			}

			$k++;
		}

		$parts        = self::FetchRow($filedata, '<td>' . self::getObservationPattern());
		$parts[20]    = '-';
		$data['hohe'] = $parts[$position[0]] . ' m';
		$data['luft'] = $parts[$position[1]] . ' hPa';
		$data['temp'] = $parts[$position[2]] . ' &deg;C';

		if ($parts[$position[2]] !== null)
		{
			$data['temp'] = $parts[$position[2]] . ' &deg;C';
		}
		else
		{
			$data['temp'] = '-- &deg;C';
		}

		if ($parts[$position[3]] == '----')
		{
			$parts[$position[3]] = '0.0';
		}

		$data['regen']    = $parts[$position[3]] . ' mm';
		$data['richtung'] = $parts[$position[4]] . ' ';
		$data['wind']     = $parts[$position[5]] . ' km/h';
		$data['spitze']   = $parts[$position[6]] . ' km/h';

		if ($parts[$position[7]] == 'kein')
		{
			$parts[$position[7]] = 'heiter';
		}

		$check = array('gering', 'leichter', 'starker', 'stark', 'kräftiger', 'vereinzelt', 'gefrierender', 'in', 'schweres', 'starkes');
		if (in_array($parts[$position[7]], $check))
		{
			$parts[$position[7]] = $parts[$position[7]] . ' ' . $parts[$position[8]];
		}

		$data['himmel'] = self::getIcon($parts[$position[7]], date('G'));

		$data['beschreibung'] = $parts[$position[7]];

		return $data;
	}

	/**
	 * @param  string $filedata The file content to parse
	 *
	 * @return array  $data  Array of data
	 *
	 * @since 4.0
	 */
	private static function parseFiledataFuture($filedata)
	{
		$glieder = self::FetchHeaders($filedata);
		$parts   = self::FetchRow($filedata, '<td>' . self::getForecastPattern());
		$dataFtp = array();
		$data    = array();

		foreach ($glieder as $key => $value)
		{
			$dataFtp[$value] = $parts[$key];
		}

		if ($dataFtp['Tmax'] !== null)
		{
			$data['temp'] = $dataFtp['Tmax'] . ' &deg;C';
		}
		else
		{
			$data['temp'] = '-- &deg;C';
		}

		if (!$dataFtp['Wetter/Wolken'])
		{
			$dataFtp['Wetter/Wolken'] = 'heiter';
		}

		$data['himmel']       = self::getIcon($dataFtp['Wetter/Wolken']);
		$data['beschreibung'] = $dataFtp['Wetter/Wolken'];

		return $data;
	}

	/**
	 * Returns the station name of the selected observation station
	 *
	 * @return string
	 *
	 * @since 4.1
	 */
	private static function getObservationPattern()
	{
		$stations = array(
			82 => 'UFS TW Ems',
			83 => 'UFS Deutsche Bucht',
			2  => 'Helgoland',
			3  => 'List/Sylt',
			4  => 'Schleswig',
			6  => 'Leuchtturm Kiel',
			7  => 'Kiel',
			8  => 'Fehmarn',
			9  => 'Arkona',
			10 => 'Norderney',
			11 => 'Leuchtt. Alte Weser',
			12 => 'Cuxhaven',
			13 => 'Hamburg-Flh.',
			14 => 'Schwerin',
			15 => 'Rostock',
			16 => 'Greifswald',
			17 => 'Emden',
			18 => 'Bremen-Flh.',
			78 => 'Lüchow',
			19 => 'Marnitz',
			79 => 'Waren',
			20 => 'Neuruppin',
			21 => 'Angermünde',
			22 => 'Münster/Osnabr.-Flh.',
			23 => 'Hannover-Flh.',
			24 => 'Magdeburg',
			25 => 'Potsdam',
			26 => 'Berlin-Tegel',
			27 => 'Berlin-Tempelhof',
			80 => 'Berlin-Dahlem',
			28 => 'Lindenberg',
			29 => 'Düsseldorf-Flh.',
			81 => 'Essen',
			30 => 'Kahler Asten',
			31 => 'Bad Lippspringe',
			33 => 'Fritzlar',
			34 => 'Brocken',
			35 => 'Leipzig-Flh.',
			36 => 'Dresden-Flh.',
			37 => 'Cottbus',
			38 => 'Görlitz',
			39 => 'Aachen',
			40 => 'Nürburg',
			41 => 'Köln/Bonn-Flh.',
			42 => 'Gießen/Wettenberg',
			43 => 'Wasserkuppe',
			44 => 'Meiningen',
			45 => 'Erfurt',
			46 => 'Gera',
			47 => 'Fichtelberg',
			48 => 'Trier',
			49 => 'Hahn-Flh.',
			50 => 'Frankfurt/M-Flh.',
			51 => 'OF-Wetterpark',
			52 => 'Würzburg',
			53 => 'Bamberg',
			54 => 'Hof',
			55 => 'Weiden',
			56 => 'Saarbrücken-Flh.',
			57 => 'Karlsruhe-Rheinst.',
			58 => 'Mannheim',
			59 => 'Stuttgart-Flh.',
			60 => 'Öhringen',
			61 => 'Nürnberg-Flh.',
			62 => 'Regensburg',
			63 => 'Straubing',
			64 => 'Großer Arber',
			65 => 'Lahr',
			66 => 'Freudenstadt',
			67 => 'Stötten',
			68 => 'Augsburg',
			69 => 'München-Flh.',
			70 => 'Fürstenzell',
			71 => 'Feldberg/Schw.',
			72 => 'Konstanz',
			73 => 'Kempten',
			74 => 'Oberstdorf',
			75 => 'Zugspitze',
			76 => 'Hohenpeißenberg',
		);

		$station = self::$params->get('daten', 13);

		return !empty($stations[$station]) ? $stations[$station] : $stations[13];
	}

	/**
	 * Returns the station name of the selected forecast station
	 *
	 * @return string
	 *
	 * @since 4.1
	 */
	private static function getForecastPattern()
	{
		$stations = array(
			2  => 'Helgoland',
			1  => 'List/Sylt',
			3  => 'Schleswig',
			4  => 'Kiel',
			5  => 'Fehmarn',
			27 => 'Arkona',
			6  => 'Norderney',
			7  => 'Cuxhaven',
			8  => 'Hamburg-Flh.',
			9  => 'Schwerin',
			28 => 'Rostock',
			29 => 'Greifswald',
			10 => 'Emden',
			11 => 'Bremen-Flh.',
			73 => 'Lüchow',
			31 => 'Marnitz',
			74 => 'Waren',
			32 => 'Neuruppin',
			33 => 'Angermünde',
			12 => 'Münster/Osnabr.-Flh.',
			13 => 'Hannover-Flh.',
			16 => 'Magdeburg',
			34 => 'Potsdam',
			25 => 'Berlin-Tegel',
			75 => 'Berlin-Tempelhof',
			76 => 'Berlin-Dahlem',
			26 => 'Lindenberg',
			35 => 'Düsseldorf-Flh.',
			77 => 'Essen',
			36 => 'Kahler Asten',
			14 => 'Bad Lippspringe',
			37 => 'Fritzlar',
			15 => 'Brocken',
			18 => 'Leipzig-Flh.',
			19 => 'Dresden-Flh.',
			17 => 'Cottbus',
			20 => 'Görlitz',
			50 => 'Aachen',
			40 => 'Nürburg',
			38 => 'Köln/Bonn-Flh.',
			39 => 'Gießen/Wettenberg',
			41 => 'Wasserkuppe',
			21 => 'Meiningen',
			22 => 'Erfurt',
			23 => 'Gera',
			24 => 'Fichtelberg',
			42 => 'Trier',
			43 => 'Hahn-Flh.',
			44 => 'Frankfurt/M-Flh.',
			78 => 'OF-Wetterpark',
			45 => 'Würzburg',
			58 => 'Bamberg',
			59 => 'Hof',
			60 => 'Weiden',
			48 => 'Saarbrücken-Flh.',
			49 => 'Karlsruhe-Rheinst.',
			46 => 'Mannheim',
			52 => 'Stuttgart-Flh.',
			51 => 'Öhringen',
			61 => 'Nürnberg-Flh.',
			62 => 'Regensburg',
			63 => 'Straubing',
			64 => 'Großer Arber',
			54 => 'Lahr',
			55 => 'Freudenstadt',
			53 => 'Stötten',
			66 => 'Augsburg',
			67 => 'München-Flh.',
			65 => 'Fürstenzell',
			56 => 'Feldberg/Schw.',
			57 => 'Konstanz',
			68 => 'Kempten',
			69 => 'Oberstdorf',
			71 => 'Zugspitze',
			70 => 'Hohenpeißenberg',
		);

		$station = self::$params->get('datenvorhersage', 8);

		return !empty($stations[$station]) ? $stations[$station] : $stations[8];
	}

	/**
	 * @param string $string
	 * @param int    $hour
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	private static function getIcon($string, $hour = 12)
	{
		$day = ($hour > 4 && $hour < 19);

		switch ($string)
		{
			case 'heiter':
			case 'Heiter':
			case 'gering bewölkt':
			case 'gering Bewölkt':
				$icon = ($day) ? 'heiter.png' : 'nheiter.png';
				break;
			case 'bedeckt':
			case 'Bedeckt':
				$icon = 'bedeckt.png';
				break;
			case 'Sonne':
			case 'wolkenlos':
			case 'Wolkenlos':
				$icon = ($day) ? 'sonne.png' : 'nsonne.png';
				break;
			case 'Regen':
			case 'leichter Regen':
			case 'leichter Regen oder Schneegriesel':
			case 'Schauer':
			case 'Regenschauer':
			case 'Schneeregen':
			case 'leichter Schneeregen':
			case 'Niederschlag':
				$icon = 'leichtregen.png';
				break;
			case 'kräftiger Regen':
			case 'kräftiger Regenschauer':
				$icon = 'starkregen.png';
				break;
			case 'Schnee':
			case 'Schneefall':
			case 'leichter Schneefall':
			case 'starker Schneefall':
			case 'kräftiger Schneefall':
			case 'kräftiger Schneeregen':
			case 'Schneeregenschauer':
			case 'kräftiger Schneeregenschauer':
			case 'Schneeschauer':
			case 'kräftiger Schneeschauer':
			case 'Schneefegen':
			case 'Schneegriesel':
			case 'Schneetreiben':
			case 'Glatteisbildung':
				$icon = 'schnee.png';
				break;
			case 'Graupelschauer':
			case 'kräftiger Graupelschauer':
			case 'Hagelschauer':
			case 'kräftiger Hagelschauer':
				$icon = 'hagel.png';
				break;
			case 'Gewitter':
			case 'schweres Gewitter':
			case 'starkes Gewitter':
				$icon = 'gewitter.png';
				break;
			case 'Nebel':
			case 'gefrierender Nebel':
			case 'Dunst':
			case 'Dunst oder flacher Nebel':
			case 'in Wolken':
			case 'Sandsturm':
			case 'Sandsturm oder Schneefegen':
				$icon = 'nebel.png';
				break;
			case 'bewölkt':
			case 'Bewölkt':
			case 'stark bewölkt':
			case 'stark Bewölkt':
			default:
				$icon = ($day) ? 'bewolkt.png' : 'nbewolkt.png';
				break;
		}

		return $icon;
	}

	/**
	 * Fetchs the header row from the table
	 *
	 * @param string $filedata The raw file string
	 *
	 * @return array
	 *
	 * @since 4.1
	 */
	private static function FetchHeaders($filedata)
	{
		preg_match_all('/<th (.*)/', $filedata, $glieder);
		$glieder = $glieder[0];
		$glieder = array_map('strip_tags', $glieder);
		$glieder = array_map('trim', $glieder);

		return $glieder;
	}

	/**
	 * @param string $filedata The raw file string
	 * @param string $needle   The row to find
	 *
	 * @return array
	 *
	 * @since 4.1
	 */
	private static function FetchRow($filedata, $needle)
	{
		$row = strstr($filedata, $needle);
		$row = strstr($row, '</tr>', true);
		$row = trim(strip_tags($row));

		// Remove non-breaking spaces
		$row = htmlentities($row, null, 'utf-8');
		$row = str_replace('&nbsp;', '', $row);
		$row = html_entity_decode($row);

		$parts = explode("\r\n", $row);
		$parts = array_map('trim', $parts);

		return $parts;
	}
}
