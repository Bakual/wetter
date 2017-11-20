<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - Thomas Hunziker
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

// DWD Wettervorhersage Modul
// Wetterdaten:(c) DWD - Deutscher Wetterdienst, Offenbach
// Grafiken: (c) J. Correa - www.jcorrea.es
// Modul: (c) M. Bollmann - www.stranddorf.de
// **************************************************************************
// Das Modul lädt aktuelle Wetterdaten und Vorhersagen vom FTP Server des DWD.
// Die Daten werden lokal zwischengespeichert und grafisch aufgearbeitet. 
// Die Wetterdaten der Grundversorgung dürfen frei verwendet werden, sind jedoch urheberrechtlich geschützt.
// **************************************************************************

defined('_JEXEC') or die();

require_once __DIR__ . '/helper.php';

$cacheparams               = new stdClass;
$cacheparams->cachemode    = 'static';
$cacheparams->class        = 'ModDwdwetterHelper';
$cacheparams->method       = 'getList';
$cacheparams->methodparams = $params;

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

if (!$list)
{
	return;
}

$units = ModDwdwetterHelper::getUnits();

$timestamp = time();
$day0      = date('d.m.y', $timestamp);
$day1      = date('d.m.y', $timestamp + (1 * 24 * 60 * 60));
$time      = str_pad(floor(date('H', $timestamp)/3)*3, '2', '0');

$days = array();

if ($params->get('tag0'))
{
	$days[0] = $day0;
}
if ($params->get('tag1'))
{
	$days[1] = $day1;
}
if ($params->get('tag2'))
{
	$days[2] = date('d.m.y', $timestamp + (2 * 24 * 60 * 60));
}
if ($params->get('tag3'))
{
	$days[3] = date('d.m.y', $timestamp + (3 * 24 * 60 * 60));
	$days[4] = date('d.m.y', $timestamp + (4 * 24 * 60 * 60));
	$days[5] = date('d.m.y', $timestamp + (5 * 24 * 60 * 60));
}

$titel             = $params->get('titel');
$farbe             = $params->get('farbe', '#3366cc');
$zweitfarbe        = $params->get('zweitfarbe', '#666666');
$heutehohe         = $params->get('heutehohe');
$heuteluft         = $params->get('heuteluft', 1);
$heuteregen        = $params->get('heuteregen', 1);
$heutewindrichtung = $params->get('heutewindrichtung', 1);
$heutewind         = $params->get('heutewind', 1);
$heutewindspitze   = $params->get('heutewindspitze');
$datumtitel        = $params->get('datumtitel', 1);

require JModuleHelper::getLayoutPath('mod_dwd_wettermodul', $params->get('layout', 'vertikal'));
