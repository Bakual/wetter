<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
 **/

use Bakual\Module\Wetter\Site\Helper\DwdWettermodulHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * @var array                     $list
 * @var \Joomla\Registry\Registry $params
 * @var stdClass                  $module
 * @var array                     $units
 * @var array                     $timeSteps
 * @var string                    $time
 * @var string                    $day0
 * @var string                    $day1
 * @var array                     $days
 * @var string                    $titel
 * @var string                    $farbe
 * @var string                    $zweitfarbe
 * @var string                    $heutehohe
 * @var string                    $heuteluft
 * @var string                    $heuteregen
 * @var string                    $heutewindrichtung
 * @var string                    $heutewind
 * @var string                    $heutewindspitze
 * @var string                    $datumtitel
 */

$helper = new DwdWettermodulHelper();

Factory::getApplication()->getDocument()->addStyleDeclaration(
		'.dwd_wettermodul.minimal table {
		width: 100%;
	}
	.dwd_wettermodul.minimal table td {
		border-top: 1px solid ' . $farbe . ';
		width: 33%;
	}
	.dwd_wettermodul.minimal .color_text {
		color: ' . $zweitfarbe . ';
	}
	.dwd_wettermodul.minimal .temp {
		color: ' . $farbe . ';
	}'
);
?>
<div class="dwd_wettermodul minimal">
	<?php if ($titel) : ?>
		<?php echo $titel; ?>
	<?php endif; ?>
	<table>
		<?php foreach ($days as $i => $day) : ?>
			<?php if ($i === 0) : ?>
				<?php if (isset($timeSteps[$day . 'T18:00:00.000Z'])) : ?>
					<?php $forecastIndex = $timeSteps[$day . 'T18:00:00.000Z']; ?>
				<?php else : ?>
					<?php $forecastIndex = $timeSteps[$day . 'T' . $time . ':00:00.000Z']; ?>
				<?php endif; ?>
			<?php else : ?>
				<?php $forecastIndex = $timeSteps[$day . 'T18:00:00.000Z'] ?>
			<?php endif; ?>
			<tr>
				<td class="color_text">
					<?php if (!$i) : ?>
						<?php echo Text::_('MOD_DWD_WETTERMODUL_DAY0'); ?>
					<?php elseif ($i == 1) : ?>
						<?php echo Text::_('MOD_DWD_WETTERMODUL_DAY1'); ?>
					<?php else : ?>
						<?php echo HtmlHelper::date($day, Text::_('DATE_FORMAT_LC4')); ?>
					<?php endif; ?>
				</td>
				<td class="text-center">
					<span class="temp">
						<?php if ($list->TX[$forecastIndex] !== '-') : ?>
							<?php echo round($list->TX[$forecastIndex] - 273.15); ?>°C
						<?php elseif ($list->TTT[$forecastIndex] !== '-') : ?>
							<?php echo round($list->TTT[$forecastIndex] - 273.15); ?>°C
						<?php else: ?>
							--
							<?php $helper->logError($forecastIndex, $day0, $time, $timeSteps, $list, 'minimal'); ?>
						<?php endif; ?>
					</span>
				</td>
				<td class="text-center">
					<img alt=""
						 src="modules/mod_dwd_wettermodul/icons/<?php echo $helper->getIcon($list, $forecastIndex, $time); ?>"
						 width="26" height="26"/>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<div class="text-right">
		<small><a href="https://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small>
	</div>
</div>
