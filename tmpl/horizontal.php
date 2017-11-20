<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - T. Hunziker / M. Bollmann
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JFactory::getDocument()->addStyleDeclaration(
	'.dwd_wettermodul.horizontal table {
		width: 100%;
	}
	.dwd_wettermodul.horizontal .border {
		border-left: 1px solid ' . $farbe . ';
	}
	.dwd_wettermodul.horizontal .color_text {
		color: ' . $zweitfarbe .';
	}
	.dwd_wettermodul.horizontal .temp {
		font-size: large;
		color: ' . $farbe .';
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
                            <strong>Aktuell</strong>
                        </td>
                    <?php elseif ($i == 1) : ?>
                        <td class="border">
                            <strong>Morgen</strong>
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
                <?php if (!$i) : ?>
                    <td colspan="2">
                <?php else : ?>
                    <td class="border">
                <?php endif; ?>
                    <img alt="" src="modules/mod_dwd_wettermodul/icons/<?php echo ModDwdwetterHelper::getIcon($list[$day . ' 18:00'], $time); ?>" width="100" height="100" />
                </td>
			<?php endforeach; ?>
		</tr>
		<tr class="text-center">
			<?php foreach ($days as $i => $day) : ?>
                <?php if (!$i) : ?>
                    <td colspan="2">
                <?php else : ?>
                    <td class="border">
                <?php endif; ?>
                    <span class="temp"><?php echo $list[$day . ' 18:00']['Tx']; ?>°C</span>
                </td>
			<?php endforeach; ?>
		</tr>
		<?php if (isset($days[0])) : ?>
			<?php $current = $list[$day0 . ' ' . $time . ':00']; ?>
			<?php if ($heutehohe) : ?>
				<tr>
					<td>H&ouml;he &uuml;. NN:</td>
					<td nowrap="nowrap""><?php echo ModDwdwetterHelper::getStation($params->get('station'))->alt; ?> m</td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heuteluft) : ?>
				<tr>
					<td>Luftdruck:</td>
					<td nowrap="nowrap"><?php echo $current['PPPP'] . ' ' . $units['PPPP']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heuteregen) : ?>
				<tr>
					<td>Niederschlag:</td>
					<td nowrap="nowrap"><?php echo $list[$day1 . ' 06:00']['RR24'] . ' ' . $units['RR24']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindrichtung) : ?>
				<tr>
					<td>Windrichtung:</td>
					<td><?php echo $current['dd'] . ' ' . $units['dd']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewind) : ?>
				<tr>
					<td>Geschwindigkeit:</td>
					<td nowrap="nowrap"><?php echo $current['ff'] . ' ' . $units['ff']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindspitze) : ?>
				<tr>
					<td>Windb&ouml;en:</td>
					<td nowrap="nowrap"><?php echo $current['fx'] . ' ' . $units['fx']; ?></td>
					<?php for ($i = 2; $i <= $count; $i++) : ?>
                        <td class="border"></td>
					<?php endfor; ?>
				</tr>
			<?php endif;
		endif; ?>
	</table>
	<div class="text-right"><small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small></div>
</div>