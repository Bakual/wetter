<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - T. Hunziker / M. Bollmann
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

Factory::getDocument()->addStyleDeclaration(
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
                        <td class="border">
                            <strong><?php echo Text::_('MOD_DWD_WETTERMODUL_DAY1'); ?></strong>
                        </td>
					<?php else: ?>
                        <td class="border">
                            <strong><?php echo $day; ?></strong>
                        </td>
					<?php endif; ?>
				<?php endforeach; ?>
            </tr>
		<?php endif; ?>
        <tr class="text-center">
			<?php foreach ($days as $i => $day) : ?>
				<?php $forecastIndex = $timeSteps[$daysEn[$i] . 'T18:00:00.000Z'] ?>
				<?php if (!$i) : ?>
                    <td colspan="2">
				<?php else : ?>
                    <td class="border">
				<?php endif; ?>
                <img alt=""
                     src="modules/mod_dwd_wettermodul/icons/<?php echo ModDwdwetterHelper::getIcon($list, $forecastIndex, $time); ?>"
                     width="100" height="100"/>
                </td>
			<?php endforeach; ?>
        </tr>
        <tr class="text-center">
			<?php foreach ($days as $i => $day) : ?>
				<?php $forecastIndex = $timeSteps[$daysEn[$i] . 'T18:00:00.000Z'] ?>
				<?php if (!$i) : ?>
                    <td colspan="2">
				<?php else : ?>
                    <td class="border">
				<?php endif; ?>
                <span class="temp"><?php echo $list->TX[$forecastIndex] - 273.15; ?>°C</span>
                </td>
			<?php endforeach; ?>
        </tr>
		<?php if (isset($days[0])) : ?>
			<?php $forecastIndex = $timeSteps[$day0en . 'T18:00:00.000Z']; ?>
			<?php if ($heutehohe) : ?>
                <tr>
                    <td><?php echo Text::_('MOD_DWD_WETTERMODUL_HOEHE'); ?></td>
                    <td nowrap="nowrap"><?php echo ModDwdwetterHelper::getStation($params->get('station'))->alt; ?> m</td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
                </tr>
			<?php endif; ?>
			<?php if ($heuteluft) : ?>
                <tr>
                    <td><?php echo Text::_('MOD_DWD_WETTERMODUL_LUFTDRUCK'); ?></td>
                    <td nowrap="nowrap"><?php echo $list->PPPP[$forecastIndex] . ' ' . $units['PPPP']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
                </tr>
			<?php endif; ?>
			<?php if ($heuteregen) : ?>
                <tr>
                    <td><?php echo Text::_('MOD_DWD_WETTERMODUL_NIEDERSCHLAG'); ?></td>
                    <td nowrap="nowrap"><?php echo $list->RRdc[$timeSteps[$day1en . 'T06:00:00.000Z']] . ' ' . $units['RRdc']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
                </tr>
			<?php endif; ?>
			<?php if ($heutewindrichtung) : ?>
                <tr>
                    <td><?php echo Text::_('MOD_DWD_WETTERMODUL_WINDRICHTUNG'); ?></td>
                    <td><?php echo ModDwdwetterHelper::getDirection($list->DD[$forecastIndex]); ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
                </tr>
			<?php endif; ?>
			<?php if ($heutewind) : ?>
                <tr>
                    <td><?php echo Text::_('MOD_DWD_WETTERMODUL_WINDGESCHWINDIKEIT'); ?></td>
                    <td nowrap="nowrap"><?php echo $list->FF[$forecastIndex] . ' ' . $units['FF']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
                </tr>
			<?php endif; ?>
			<?php if ($heutewindspitze) : ?>
                <tr>
                    <td><?php echo Text::_('MOD_DWD_WETTERMODUL_WINDSPITZE'); ?></td>
                    <td nowrap="nowrap"><?php echo $list->FX3[$forecastIndex] . ' ' . $units['FX3']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
                </tr>
			<?php endif;
		endif; ?>
    </table>
    <div class="text-right">
        <small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small>
    </div>
</div>