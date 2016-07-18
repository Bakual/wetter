<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2014 - Thomas Hunziker
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
// Der Copyright-Vermerk und der Link dürfen nicht entfernt werden! Anpassungen an das eigene Webdesign sind natürlich gestattet.
// Der Quelltext ist zwar nicht schön, dafür in liebevoller Handarbeit gebastelt!
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
	 */
	static $params;

	/**
	 * @var string  Region
	 */
	static $region;

	/**
	 * @var string  Search Pattern for location
	 */
	static $pattern;

	/**
	 * @var JClientFtp Holds the FTP connection
	 */
	static $ftp;

	/**
	 * @param $params \Joomla\Registry\Registry Module Params
	 *
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

		foreach ($days as $day)
		{
			// Get filedata
			$filedata = self::getFile($day);

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

	private static function getFile($day)
	{
		if (!self::$ftp)
		{
			$host      = 'ftp-outgoing2.dwd.de';
			self::$ftp = JClientFtp::getInstance($host, 21, array(), self::$params->get('user'), self::$params->get('passwort'));
		}

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
			// Set region and pattern
			if (!self::$region)
			{
				self::setRegion();
			}

			$folder = '/gds/gds/specials/forecasts/tables/germany/';
			$file   = 'Daten_' . self::$region;

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
		}

		// Read file
		self::$ftp->read($folder . $file, $filedata);

		return utf8_encode($filedata);
	}

	private static function parseFiledataCurrent($filedata)
	{
		$filedata = html_entity_decode($filedata);

		preg_match_all('/<th (.*)/', $filedata, $glieder);
		$glieder = $glieder[0];
		$glieder = array_map('strip_tags', $glieder);
		$glieder = array_map('trim', $glieder);

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

		$needle = trim('<td>' . self::getPattern(), ' .\t\n\r\0\x0B');

		$treffer      = strstr($filedata, $needle);
		$treffer      = strstr($treffer, '</tr>', true);
		$treffer      = trim(strip_tags($treffer));
		$teile        = explode("\r\n", $treffer);
		$teile        = array_map('trim', $teile);
		$teile[20]    = '-';
		$data['hohe'] = $teile[$position[0]] . ' m';
		$data['luft'] = $teile[$position[1]] . ' hPa';
		$data['temp'] = $teile[$position[2]] . ' &deg;C';

		if ($teile[$position[2]] !== null)
		{
			$data['temp'] = $teile[$position[2]] . ' &deg;C';
		}
		else
		{
			$data['temp'] = '-- &deg;C';
		}

		if ($teile[$position[3]] == '----')
		{
			$teile[$position[3]] = '0.0';
		}

		$data['regen']    = $teile[$position[3]] . ' mm';
		$data['richtung'] = $teile[$position[4]] . ' ';
		$data['wind']     = $teile[$position[5]] . ' km/h';
		$data['spitze']   = $teile[$position[6]] . ' km/h';

		if ($teile[$position[7]] == 'kein')
		{
			$teile[$position[7]] = 'heiter';
		}

		$check = array('gering', 'leichter', 'starker', 'stark', 'kräftiger', 'vereinzelt', 'gefrierender', 'in', 'schweres', 'starkes');
		if (in_array($teile[$position[7]], $check))
		{
			$teile[$position[7]] = $teile[$position[7]] . ' ' . $teile[$position[8]];
		}

		$data['himmel'] = self::getIcon($teile[$position[7]], date('G'));

		$data['beschreibung'] = $teile[$position[7]];

		return $data;
	}

	private static function parseFiledataFuture($filedata)
	{
		preg_match(self::$pattern, $filedata, $treffer);
		$teile = str_word_count(trim($treffer[1]), 1, '-öäü1234567890.,');

		if ($teile[0] !== null)
		{
			$data['temp'] = $teile[0] . ' &deg;C';
		}
		else
		{
			$data['temp'] = '-- &deg;C';
		}

		$check = array('leichter', 'starker', 'kräftiger', 'vereinzelt', 'in', 'schweres', 'starkes');
		if (in_array($teile[1], $check))
		{
			$teile[1] = $teile[1] . ' ' . $teile[2];
		}

		if ($teile[1] == 'kein')
		{
			$teile[1] = 'heiter';
		}

		$data['himmel']       = self::getIcon($teile[1]);
		$data['beschreibung'] = $teile[1];

		return $data;
	}

	private static function setRegion()
	{
		switch (self::$params->get('datenvorhersage', 8))
		{
			case '1':
				$pattern = '/List\/Sylt (.*)/';
				$region  = 'Nordwest';
				break;
			case '2':
				$pattern = '/Helgoland (.*)/';
				$region  = 'Nordwest';
				break;
			case '3':
				$pattern = '/Schleswig (.*)/';
				$region  = 'Nordwest';
				break;
			case '4':
				$pattern = '/Kiel (.*)/';
				$region  = 'Nordwest';
				break;
			case '5':
				$pattern = '/Fehmarn (.*)/';
				$region  = 'Nordwest';
				break;
			case '6':
				$pattern = '/Norderney (.*)/';
				$region  = 'Nordwest';
				break;
			case '7':
				$pattern = '/Cuxhaven (.*)/';
				$region  = 'Nordwest';
				break;
			case '8':
				$pattern = '/Hamburg (.*)/';
				$region  = 'Nordwest';
				break;
			case '9':
				$pattern = '/Schwerin (.*)/';
				$region  = 'Nordwest';
				break;
			case '10':
				$pattern = '/Emden (.*)/';
				$region  = 'Nordwest';
				break;
			case '11':
				$pattern = '/Bremen (.*)/';
				$region  = 'Nordwest';
				break;
			case '12':
				$pattern = '/Münster (.*)/';
				$region  = 'Nordwest';
				break;
			case '13':
				$pattern = '/Hannover (.*)/';
				$region  = 'Nordwest';
				break;
			case '14':
				$pattern = '/Bad Lippspringe (.*)/';
				$region  = 'Nordwest';
				break;
			case '15':
				$pattern = '/Brocken (.*)/';
				$region  = 'Nordwest';
				break;
			case '16':
				$pattern = '/Magdeburg (.*)/';
				$region  = 'Ost';
				break;
			case '17':
				$pattern = '/Cottbus (.*)/';
				$region  = 'Ost';
				break;
			case '18':
				$pattern = '/Leipzig (.*)/';
				$region  = 'Ost';
				break;
			case '19':
				$pattern = '/Dresden (.*)/';
				$region  = 'Ost';
				break;
			case '20':
				$pattern = '/Görlitz (.*)/';
				$region  = 'Ost';
				break;
			case '21':
				$pattern = '/Meiningen (.*)/';
				$region  = 'Ost';
				break;
			case '22':
				$pattern = '/Erfurt (.*)/';
				$region  = 'Ost';
				break;
			case '23':
				$pattern = '/Gera (.*)/';
				$region  = 'Ost';
				break;
			case '24':
				$pattern = '/Fichtelberg (.*)/';
				$region  = 'Ost';
				break;
			case '25':
				$pattern = '/Berlin (.*)/';
				$region  = 'Ost';
				break;
			case '26':
				$pattern = '/Lindenberg (.*)/';
				$region  = 'Ost';
				break;
			case '27':
				$pattern = '/Arkona (.*)/';
				$region  = 'Nordost';
				break;
			case '28':
				$pattern = '/Rostock (.*)/';
				$region  = 'Nordost';
				break;
			case '29':
				$pattern = '/Greifswald (.*)/';
				$region  = 'Nordost';
				break;
			case '30':
				$pattern = '/Schwerin (.*)/';
				$region  = 'Nordost';
				break;
			case '31':
				$pattern = '/Marnitz (.*)/';
				$region  = 'Nordost';
				break;
			case '32':
				$pattern = '/Neuruppin (.*)/';
				$region  = 'Nordost';
				break;
			case '33':
				$pattern = '/Angermünde (.*)/';
				$region  = 'Nordost';
				break;
			case '34':
				$pattern = '/Potsdam (.*)/';
				$region  = 'Nordost';
				break;
			case '35':
				$pattern = '/Düsseldorf (.*)/';
				$region  = 'Mitte';
				break;
			case '36':
				$pattern = '/Kahler Asten (.*)/';
				$region  = 'Mitte';
				break;
			case '37':
				$pattern = '/Fritzlar (.*)/';
				$region  = 'Mitte';
				break;
			case '38':
				$pattern = '/Köln (.*)/';
				$region  = 'Mitte';
				break;
			case '39':
				$pattern = '/Gießen (.*)/';
				$region  = 'Mitte';
				break;
			case '40':
				$pattern = '/Nürburg (.*)/';
				$region  = 'Mitte';
				break;
			case '41':
				$pattern = '/Wasserkuppe (.*)/';
				$region  = 'Mitte';
				break;
			case '42':
				$pattern = '/Trier (.*)/';
				$region  = 'Mitte';
				break;
			case '43':
				$pattern = '/Hahn (.*)/';
				$region  = 'Mitte';
				break;
			case '44':
				$pattern = '/Frankfurt (.*)/';
				$region  = 'Mitte';
				break;
			case '45':
				$pattern = '/Würzburg (.*)/';
				$region  = 'Mitte';
				break;
			case '46':
				$pattern = '/Mannheim (.*)/';
				$region  = 'Mitte';
				break;
			case '47':
				$pattern = '/Weinbiet (.*)/';
				$region  = 'Mitte';
				break;
			case '48':
				$pattern = '/Saarbrücken (.*)/';
				$region  = 'Mitte';
				break;
			case '49':
				$pattern = '/Karlsruhe (.*)/';
				$region  = 'Mitte';
				break;
			case '50':
				$pattern = '/Aachen (.*)/';
				$region  = 'West';
				break;
			case '51':
				$pattern = '/Öhringen (.*)/';
				$region  = 'Suedwest';
				break;
			case '52':
				$pattern = '/Stuttgart (.*)/';
				$region  = 'Suedwest';
				break;
			case '53':
				$pattern = '/Stötten (.*)/';
				$region  = 'Suedwest';
				break;
			case '54':
				$pattern = '/Lahr (.*)/';
				$region  = 'Suedwest';
				break;
			case '55':
				$pattern = '/Freudenstadt (.*)/';
				$region  = 'Suedwest';
				break;
			case '56':
				$pattern = '/Feldberg (.*)/';
				$region  = 'Suedwest';
				break;
			case '57':
				$pattern = '/Konstanz (.*)/';
				$region  = 'Suedwest';
				break;
			case '58':
				$pattern = '/Bamberg (.*)/';
				$region  = 'Suedost';
				break;
			case '59':
				$pattern = '/Hof (.*)/';
				$region  = 'Suedost';
				break;
			case '60':
				$pattern = '/Weiden (.*)/';
				$region  = 'Suedost';
				break;
			case '61':
				$pattern = '/Nürnberg (.*)/';
				$region  = 'Suedost';
				break;
			case '62':
				$pattern = '/Regensburg (.*)/';
				$region  = 'Suedost';
				break;
			case '63':
				$pattern = '/Straubing (.*)/';
				$region  = 'Suedost';
				break;
			case '64':
				$pattern = '/Grosser Arber (.*)/';
				$region  = 'Suedost';
				break;
			case '65':
				$pattern = '/Fürstenzell (.*)/';
				$region  = 'Suedost';
				break;
			case '66':
				$pattern = '/Augsburg (.*)/';
				$region  = 'Suedost';
				break;
			case '67':
				$pattern = '/München (.*)/';
				$region  = 'Suedost';
				break;
			case '68':
				$pattern = '/Kempten (.*)/';
				$region  = 'Suedost';
				break;
			case '69':
				$pattern = '/Oberstdorf (.*)/';
				$region  = 'Suedost';
				break;
			case '70':
				$pattern = '/Hohenpeissenberg (.*)/';
				$region  = 'Suedost';
				break;
			case '71':
				$pattern = '/Zugspitze (.*)/';
				$region  = 'Suedost';
				break;
			case '72':
				$pattern = '/Wendelstein (.*)/';
				$region  = 'Suedost';
				break;

			default:
				$pattern = '/Hamburg (.*)/';
				$region  = 'Nordwest';
				break;
		}

		self::$region  = $region;
		self::$pattern = $pattern;

		return;
	}

	private static function getPattern()
	{
		switch (self::$params->get('daten', 13))
		{
			case '1':
				$pattern = 'Nordseeboje II';
				break;
			case '2':
				$pattern = 'Helgoland';
				break;
			case '3':
				$pattern = 'List/Sylt';
				break;
			case '4':
				$pattern = 'Schleswig';
				break;
			case '5':
				$pattern = 'Grosst. Fehmarnbelt';
				break;
			case '6':
				$pattern = 'Leuchtturm Kiel';
				break;
			case '7':
				$pattern = 'Kiel';
				break;
			case '8':
				$pattern = 'Fehmarn';
				break;
			case '9':
				$pattern = 'Arkona';
				break;
			case '10':
				$pattern = 'Norderney';
				break;
			case '11':
				$pattern = 'Leuchtt. Alte Weser';
				break;
			case '12':
				$pattern = 'Cuxhaven';
				break;
			case '13':
				$pattern = 'Hamburg-Flh.';
				break;
			case '14':
				$pattern = 'Schwerin';
				break;
			case '15':
				$pattern = 'Rostock';
				break;
			case '16':
				$pattern = 'Greifswald';
				break;
			case '17':
				$pattern = 'Emden';
				break;
			case '18':
				$pattern = 'Bremen-Flh.';
				break;
			case '19':
				$pattern = 'Marnitz';
				break;
			case '20':
				$pattern = 'Neuruppin';
				break;
			case '21':
				$pattern = 'Angermünde';
				break;
			case '22':
				$pattern = 'Münster/Osnabr.-Flh.';
				break;
			case '23':
				$pattern = 'Hannover-Flh.';
				break;
			case '24':
				$pattern = 'Magdeburg';
				break;
			case '25':
				$pattern = 'Potsdam';
				break;
			case '26':
				$pattern = 'Berlin-Tegel';
				break;
			case '27':
				$pattern = 'Berlin-Tempelhof';
				break;
			case '28':
				$pattern = 'Lindenberg';
				break;
			case '29':
				$pattern = 'Düsseldorf-Flh.';
				break;
			case '30':
				$pattern = 'Kahler Asten';
				break;
			case '31':
				$pattern = 'Bad Lippspringe';
				break;
			case '32':
				$pattern = 'Kassel';
				break;
			case '33':
				$pattern = 'Fritzlar';
				break;
			case '34':
				$pattern = 'Brocken';
				break;
			case '35':
				$pattern = 'Leipzig-Flh.';
				break;
			case '36':
				$pattern = 'Dresden-Flh.';
				break;
			case '37':
				$pattern = 'Cottbus';
				break;
			case '38':
				$pattern = 'Görlitz ';
				break;
			case '39':
				$pattern = 'Aachen-Orsbach';
				break;
			case '40':
				$pattern = 'Nürburg';
				break;
			case '41':
				$pattern = 'Köln/Bonn-Flh.';
				break;
			case '42':
				$pattern = 'Gießen/Wettenberg';
				break;
			case '43':
				$pattern = 'Wasserkuppe';
				break;
			case '44':
				$pattern = 'Meiningen';
				break;
			case '45':
				$pattern = 'Erfurt';
				break;
			case '46':
				$pattern = 'Gera';
				break;
			case '47':
				$pattern = 'Fichtelberg';
				break;
			case '48':
				$pattern = 'Trier ';
				break;
			case '49':
				$pattern = 'Hahn-Flh.';
				break;
			case '50':
				$pattern = 'Frankfurt/M-Flh.';
				break;
			case '51':
				$pattern = 'OF-Wetterpark ';
				break;
			case '52':
				$pattern = 'Würzburg';
				break;
			case '53':
				$pattern = 'Bamberg';
				break;
			case '54':
				$pattern = 'Hof';
				break;
			case '55':
				$pattern = 'Weiden';
				break;
			case '56':
				$pattern = 'Saarbrücken-Flh.';
				break;
			case '57':
				$pattern = 'Karlsruhe-Rheinst.';
				break;
			case '58':
				$pattern = 'Mannheim';
				break;
			case '59':
				$pattern = 'Stuttgart-Flh.';
				break;
			case '60':
				$pattern = 'Öhringen';
				break;
			case '61':
				$pattern = 'Nürnberg-Flh.';
				break;
			case '62':
				$pattern = 'Regensburg';
				break;
			case '63':
				$pattern = 'Straubing';
				break;
			case '64':
				$pattern = 'Grosser Arber';
				break;
			case '65':
				$pattern = 'Lahr';
				break;
			case '66':
				$pattern = 'Freudenstadt';
				break;
			case '67':
				$pattern = 'Stötten';
				break;
			case '68':
				$pattern = 'Augsburg';
				break;
			case '69':
				$pattern = 'München-Flh.';
				break;
			case '70':
				$pattern = 'Fürstenzell';
				break;
			case '71':
				$pattern = 'Feldberg/Schw.';
				break;
			case '72':
				$pattern = 'Konstanz';
				break;
			case '73':
				$pattern = 'Kempten';
				break;
			case '74':
				$pattern = 'Oberstdorf';
				break;
			case '75':
				$pattern = 'Zugspitze';
				break;
			case '76':
				$pattern = 'Hohenpeissenberg';
				break;
			case '77':
				$pattern = 'Wendelstein';
				break;
			default:
				$pattern = 'Fehmarn';
				break;
		}

		return $pattern;
	}

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
			case 'kräftiger Regen':
			case 'kräftiger Regenschauer':
				$icon = 'starkregen.png';
				break;
			case 'Schnee':
			case 'Schneefall':
			case 'leichter Schneefall':
			case 'starker Schneefall':
			case 'kräftiger Schneefall':
			case 'leichter Schneeregen':
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
}
