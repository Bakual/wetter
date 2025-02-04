<?php
/**
 * @package    Wettermodul
 * @author     Thomas Hunziker <admin@bakual.net>
 * @copyright  (C) 2022 - T. Hunziker /M. Bollmann
 * @license    http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

Factory::getDocument()->addStyleDeclaration(
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
							<?php ModDwdwetterHelper::logError($forecastIndex, $day0, $time, $timeSteps, $list, 'minimal'); ?>
						<?php endif; ?>
					</span>
				</td>
				<td class="text-center">
					<img alt=""
						 src="modules/mod_dwd_wettermodul/icons/<?php echo ModDwdwetterHelper::getIcon($list, $forecastIndex, $time); ?>"
						 width="26" height="26"/>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<div class="text-right">
		<small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small>
	</div>
</div>
