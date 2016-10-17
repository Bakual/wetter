<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2016 - T. Hunziker / M. Bollmann
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JFactory::getDocument()->addStyleDeclaration(
	'.dwd_wettermodul table {
		width: 100%;
	}
	.dwd_wettermodul .border {
		border-left: 1px solid ' . $farbe . ';
	}
	.dwd_wettermodul .color_text {
		color: ' . $zweitfarbe .';
	}
	.dwd_wettermodul .temp {
		font-size: large;
		color: ' . $farbe .';
	}'
);
?>
<div class="dwd_wettermodul">
	<?php if ($titel) : ?>
		<h2><?php echo $titel; ?></h2>
	<?php endif; ?>
	<table>
		<?php if ($datumtitel) : ?>
			<tr class="color_text text-center">
				<?php if (isset($list[0])) : ?>
					<td colspan="2">
						<strong>Aktuell</strong>
					</td>
				<?php endif; ?>
				<?php if (isset($list[1])) : ?>
					<td class="border">
						<strong>Morgen</strong>
					</td>
				<?php endif; ?>
				<?php if (isset($list[2])) : ?>
					<td class="border">
						<strong><?php echo $datum2; ?></strong>
					</td>
				<?php endif; ?>
				<?php if (isset($list[3])) : ?>
					<td class="border">
						<strong><?php echo $datum3; ?></strong>
					</td>
				<?php endif; ?>
			</tr>
		<?php endif; ?>
		<tr class="text-center">
			<?php for ($i = 0; $i <= 3; $i++) : ?>
				<?php if (isset($list[$i])) : ?>
					<?php if (!$i) : ?>
						<td colspan="2">
					<?php else : ?>
						<td class="border">
					<?php endif; ?>
						<img alt="<?php echo $list[$i]['beschreibung']; ?>" src="modules/mod_dwd_wettermodul/icons/<?php echo $list[$i]['himmel']; ?>" width="100" height="100" />
					</td>
				<?php endif; ?>
			<?php endfor; ?>
		</tr>
		<tr class="text-center">
			<?php for ($i = 0; $i <= 3; $i++) : ?>
				<?php if (isset($list[$i])) : ?>
					<?php if (!$i) : ?>
						<td colspan="2">
					<?php else : ?>
						<td class="border">
					<?php endif; ?>
						<span class="temp"><?php echo $list[$i]['temp']; ?></span>
					</td>
				<?php endif; ?>
			<?php endfor; ?>
		</tr>
		<?php if ($textausgabe) : ?>
			<tr class="text-center">
				<?php for ($i = 0; $i <= 3; $i++) : ?>
					<?php if (isset($list[$i])) : ?>
						<?php if (!$i) : ?>
							<td colspan="2">
						<?php else : ?>
							<td class="border">
						<?php endif; ?>
							<?php echo $list[$i]['beschreibung']; ?>
						</td>
					<?php endif; ?>
				<?php endfor; ?>
			</tr>
		<?php endif; ?>
		<?php if (isset($list[0])) : ?>
			<?php if ($heutehohe) : ?>
				<tr>
					<td>H&ouml;he &uuml;. NN:</td>
					<td nowrap="nowrap""><?php echo htmlentities($list[0]['hohe']); ?></td>
					<?php for ($i = 1; $i <= 3; $i++) : ?>
						<?php if (isset($list[$i])) : ?>
							<td class="border"></td>
						<?php endif; ?>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heuteluft) : ?>
				<tr>
					<td>Luftdruck:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['luft']); ?></td>
					<?php for ($i = 1; $i <= 3; $i++) : ?>
						<?php if (isset($list[$i])) : ?>
							<td class="border"></td>
						<?php endif; ?>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heuteregen) : ?>
				<tr>
					<td>Niederschlag:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['regen']); ?></td>
					<?php for ($i = 1; $i <= 3; $i++) : ?>
						<?php if (isset($list[$i])) : ?>
							<td class="border"></td>
						<?php endif; ?>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindrichtung) : ?>
				<tr>
					<td>Windrichtung:</td>
					<td><?php echo htmlentities($list[0]['richtung']); ?></td>
					<?php for ($i = 1; $i <= 3; $i++) : ?>
						<?php if (isset($list[$i])) : ?>
							<td class="border"></td>
						<?php endif; ?>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewind) : ?>
				<tr>
					<td>Geschwindigkeit:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['wind']); ?></td>
					<?php for ($i = 1; $i <= 3; $i++) : ?>
						<?php if (isset($list[$i])) : ?>
							<td class="border"></td>
						<?php endif; ?>
					<?php endfor; ?>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindspitze) : ?>
				<tr>
					<td>Windb&ouml;en:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['spitze']); ?></td>
					<?php for ($i = 1; $i <= 3; $i++) : ?>
						<?php if (isset($list[$i])) : ?>
							<td class="border"></td>
						<?php endif; ?>
					<?php endfor; ?>
				</tr>
			<?php endif;
		endif; ?>
	</table>
	<div class="text-right"><small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small></div>
</div>