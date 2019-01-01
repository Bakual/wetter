<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - T. Hunziker /M. Bollmann
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;

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
			<?php $forecastIndex = $timeSteps[$day . 'T18:00:00.000Z'] ?>
			<tr>
				<td class="color_text">
					<?php if (!$i) : ?>
						<?php echo Text::_('MOD_DWD_WETTERMODUL_DAY0'); ?>
					<?php elseif ($i == 1) : ?>
						<?php echo Text::_('MOD_DWD_WETTERMODUL_DAY1'); ?>
					<?php else : ?>
						<?php echo JHtml::date($day, JText::_('DATE_FORMAT_LC4')); ?>
					<?php endif; ?>
				</td>
				<td class="text-center">
					<span class="temp">
						<?php if ($list->TX[$forecastIndex]) : ?>
							<?php echo round($list->TX[$forecastIndex] - 273.15); ?>°C
						<?php else: ?>
							--
							<?php $errorstring = 'Temperature not found for ForecastIndex "' . $forecastIndex . '" at time "' . $day0 . ' ' . $time . ':00". '; ?>
							<?php $errorstring .= 'Layout in use was "minimal".' ?>
							<?php $errorstring .= "\n" . print_r($list->TX, true); ?>
							<?php Log::add($errorstring, Log::WARNING, 'dwd_wetter'); ?>
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
