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
 * @var DwdWettermodulHelper      $helper
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

Factory::getApplication()->getDocument()->addStyleDeclaration(
		'.dwd_wettermodul.horizontal table {
		width: 100%;
	}
	.dwd_wettermodul.horizontal .border {
		border-left: 1px solid ' . $farbe . ';
	}
	.dwd_wettermodul.horizontal .color_text {
		color: ' . $zweitfarbe . ';
	}
	.dwd_wettermodul.horizontal .temp {
		font-size: large;
		color: ' . $farbe . ';
	}'
);

$count = count($days);
?>
<div class="dwd_wettermodul horizontal">
	<?php if ($titel) : ?>
		<h2><?php echo $titel; ?></h2>
	<?php endif; ?>
	<table>
		<?php if ($datumtitel) : ?>
			<tr class="color_text text-center">
				<?php foreach ($days as $i => $day) : ?>
					<?php if (!$i) : ?>
						<td colspan="2">
							<strong><?php echo Text::_('MOD_DWD_WETTERMODUL_DAY0'); ?></strong>
						</td>
					<?php elseif ($i == 1) : ?>
						<td class="border d-none d-md-table-cell">
							<strong><?php echo Text::_('MOD_DWD_WETTERMODUL_DAY1'); ?></strong>
						</td>
					<?php else: ?>
						<td class="border d-none d-md-table-cell">
							<strong><?php echo HTMLHelper::date($day, Text::_('DATE_FORMAT_LC4')); ?></strong>
						</td>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		<?php endif; ?>
		<tr class="text-center">
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
				<?php if (!$i) : ?>
					<td colspan="2">
				<?php else : ?>
					<td class="border  d-none d-md-table-cell">
				<?php endif; ?>
				<img alt=""
					 src="modules/mod_dwd_wettermodul/icons/<?php echo $helper->getIcon($list, $forecastIndex, $time); ?>"
					 width="100" height="100"/>
				</td>
			<?php endforeach; ?>
		</tr>
		<tr class="text-center">
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
				<?php if (!$i) : ?>
					<td colspan="2">
				<?php else : ?>
					<td class="border d-none d-md-table-cell">
				<?php endif; ?>
				<span class="temp">
					<?php if ($list->TX[$forecastIndex] !== '-') : ?>
						<?php echo round($list->TX[$forecastIndex] - 273.15); ?>°C
					<?php elseif ($list->TTT[$forecastIndex] !== '-') : ?>
						<?php echo round($list->TTT[$forecastIndex] - 273.15); ?>°C
					<?php else: ?>
						--
						<?php $helper->logError($forecastIndex, $day, $time, $timeSteps, $list, 'horizontal'); ?>
					<?php endif; ?>
				</span>
				</td>
			<?php endforeach; ?>
		</tr>
		<?php if (isset($days[0])) : ?>
			<?php if (isset($timeSteps[$day0 . 'T18:00:00.000Z'])) : ?>
				<?php $forecastIndex = $timeSteps[$day0 . 'T18:00:00.000Z']; ?>
			<?php else : ?>
				<?php $forecastIndex = $timeSteps[$day0 . 'T' . $time . ':00:00.000Z']; ?>
			<?php endif; ?>
			<?php if ($heutehohe) : ?>
				<tr>
					<td><?php echo Text::_('MOD_DWD_WETTERMODUL_HOEHE'); ?></td>
					<td nowrap="nowrap"><?php echo $helper->getStation($params->get('station'))->alt; ?>m
					</td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
						<td class="border d-none d-md-table-cell"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heuteluft) : ?>
				<tr>
					<td><?php echo Text::_('MOD_DWD_WETTERMODUL_LUFTDRUCK'); ?></td>
					<td nowrap="nowrap"><?php echo round($list->PPPP[$forecastIndex] / 100) . ' hPa'; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
						<td class="border d-none d-md-table-cell"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heuteregen) : ?>
				<tr>
					<td><?php echo Text::_('MOD_DWD_WETTERMODUL_NIEDERSCHLAG'); ?></td>
					<td nowrap="nowrap"><?php echo round($list->RRdc[$timeSteps[$day1 . 'T06:00:00.000Z']]) . ' mm'; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
						<td class="border d-none d-md-table-cell"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindrichtung) : ?>
				<tr>
					<td><?php echo Text::_('MOD_DWD_WETTERMODUL_WINDRICHTUNG'); ?></td>
					<td><?php echo $helper->getDirection($list->DD[$forecastIndex]); ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
						<td class="border d-none d-md-table-cell"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewind) : ?>
				<tr>
					<td><?php echo Text::_('MOD_DWD_WETTERMODUL_WINDGESCHWINDIKEIT'); ?></td>
					<td nowrap="nowrap"><?php echo round($list->FF[$forecastIndex] * 3.6) . ' km/h'; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
						<td class="border d-none d-md-table-cell"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindspitze) : ?>
				<tr>
					<td><?php echo Text::_('MOD_DWD_WETTERMODUL_WINDSPITZE'); ?></td>
					<td nowrap="nowrap"><?php echo round($list->FX3[$forecastIndex] * 3.6) . ' km/h'; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
						<td class="border d-none d-md-table-cell"></td>
					<?php endfor; ?>
				</tr>
			<?php endif;
		endif; ?>
	</table>
	<div class="text-right">
		<small><a href="https://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small>
	</div>
</div>
