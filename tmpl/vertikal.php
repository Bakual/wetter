<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - T. Hunziker / M.Bollmann
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

Factory::getDocument()->addStyleDeclaration(
	'.dwd_wettermodul.vertikal .row_header {
		border-top: 1px solid ' . $farbe . ';
	}
	.dwd_wettermodul.vertikal .color_text {
		color: ' . $zweitfarbe . ';
	}
	.dwd_wettermodul.vertikal .temp {
		font-size: large;
		color: ' . $farbe . ';
	}'
);
?>
<div class="dwd_wettermodul vertikal">
	<?php if ($titel) : ?>
        <h2><?php echo $titel; ?></h2>
	<?php endif; ?>
    <table>
		<?php if ($days[0]) : ?>
			<?php unset($days[0]); ?>
			<?php $current = $list[$day0 . ' ' . $time . ':00']; ?>
            <tr>
                <td colspan="2" class="row_header color_text">
					<?php if ($datumtitel) : ?>
                        <strong>Aktuell</strong>
					<?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <span class="temp"><?php echo $current['TT']; ?>°C</span>
                </td>
                <td class="text-center">
                    <img alt=""
                         src="modules/mod_dwd_wettermodul/icons/<?php echo ModDwdwetterHelper::getIcon($current, $time); ?>"
                         width="50" height="50"/>
                </td>
            </tr>
			<?php if ($heutehohe) : ?>
                <tr>
                    <td>H&ouml;he &uuml;. NN:</td>
                    <td nowrap="nowrap"><?php echo ModDwdwetterHelper::getStation($params->get('station'))->alt; ?>m
                    </td>
                </tr>
			<?php endif; ?>
			<?php if ($heuteluft) : ?>
                <tr>
                    <td>Luftdruck:</td>
                    <td nowrap="nowrap"><?php echo $current['PPPP'] . ' ' . $units['PPPP']; ?></td>
                </tr>
			<?php endif; ?>
			<?php if ($heuteregen) : ?>
                <tr>
                    <td>Niederschlag:</td>
                    <td nowrap="nowrap"><?php echo $list[$day1 . ' 06:00']['RR24'] . ' ' . $units['RR24']; ?></td>
                </tr>
			<?php endif; ?>
			<?php if ($heutewindrichtung) : ?>
                <tr>
                    <td>Windrichtung:</td>
                    <td><?php echo ModDwdwetterHelper::getDirection($current['dd']); ?></td>
                </tr>
			<?php endif; ?>
			<?php if ($heutewind) : ?>
                <tr>
                    <td>Geschwindigkeit:</td>
                    <td nowrap="nowrap"><?php echo $current['ff'] . ' ' . $units['ff']; ?></td>
                </tr>
			<?php endif; ?>
			<?php if ($heutewindspitze) : ?>
                <tr>
                    <td>Windb&ouml;en:</td>
                    <td nowrap="nowrap"><?php echo $current['fx'] . ' ' . $units['fx']; ?></td>
                </tr>
			<?php endif; ?>
		<?php endif; ?>
		<?php foreach ($days as $i => $day) : ?>
			<?php if (isset($list[$day . ' 18:00'])) : ?>
				<?php $row = $list[$day . ' 18:00'] ?>
                <tr>
                    <td colspan="2" class="row_header color_text">
						<?php if ($datumtitel) : ?>
							<?php if ($i == 1) : ?>
                                <strong>Morgen</strong>
							<?php else : ?>
                                <strong><?php echo $day; ?></strong>
							<?php endif; ?>
						<?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        <span class="temp"><?php echo $row['Tx']; ?>°C</span>
                    </td>
                    <td class="text-center">
                        <img alt=""
                             src="modules/mod_dwd_wettermodul/icons/<?php echo ModDwdwetterHelper::getIcon($row, $time); ?>"
                             width="50"
                             height="50"/>
                    </td>
                </tr>
			<?php endif; ?>
		<?php endforeach; ?>
        <tr>
            <td colspan="2" class="row_header text-right">
                <small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small>
            </td>
        </tr>
    </table>
</div>
