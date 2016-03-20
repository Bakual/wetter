<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

// DWD Wettervorhersage Modul
// Wetterdaten:(c) DWD - Deutscher Wetterdienst, Offenbach
// Grafiken: (c) J. Correa - www.jcorrea.es
// Modul: (c) M. Bollmann - www.stranddorf.de
// **************************************************************************
// Das Modul lädt aktuelle Wetterdaten und Vorhersagen vom FTP Server des DWD.
// Die Daten werden lokal zwischengespeichert und grafisch aufgearbeitet. 
// Die Wetterdaten der Grundversorgung dürfen frei verwendet werden, sind jedoch urheberrechtlich geschützt.
// Der Copyright-Vermerk und der Link dürfen nicht entfernt werden! Anpassungen an das eigene Webdesign sind natürlich gestattet.
// Der Quelltext ist zwar nicht schön, dafür in liebevoller Handarbeit gebastelt!
// **************************************************************************

defined('_JEXEC') or die();

require_once __DIR__ . '/helper.php';

if (!$params->get('user') || !$params->get('passwort'))
{
	echo '<span class="text-error">DWD Wettermodul Fehler:<br />Die FTP-Zugangsdaten vom DWD wurden nicht korrekt angegeben!</span>';

	return;
}

$cacheparams = new stdClass;
$cacheparams->cachemode = 'static';
$cacheparams->class = 'ModDwdwetterHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

if (!count($list))
{
	return;
}

$timestamp = time();
$datum2 = date('d.m.y', $timestamp + (2 * 24 * 60 * 60));
$datum3 = date('d.m.y', $timestamp + (3 * 24 * 60 * 60));

$titel = $params->get('titel');
$farbe = $params->get('farbe', '#3366cc');
$zweitfarbe = $params->get('zweitfarbe', '#666666');
$textausgabe = $params->get('textausgabe', 1);
$heutehohe = $params->get('heutehohe');
$heuteluft = $params->get('heuteluft', 1);
$heuteregen = $params->get('heuteregen', 1);
$heutewindrichtung = $params->get('heutewindrichtung', 1);
$heutewind = $params->get('heutewind', 1);
$heutewindspitze = $params->get('heutewindspitze');
$datumtitel = $params->get('datumtitel', 1);

require JModuleHelper::getLayoutPath('mod_dwd_wettermodul', $params->get('layout', 'vertikal'));
