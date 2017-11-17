<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - T. Hunziker / M.Bollmann
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JFactory::getDocument()->addStyleDeclaration(
	'.dwd_wettermodul .row_header {
		border-top: 1px solid ' . $farbe . ';
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
		<?php if (isset($list[0])) : ?>
			<tr>
				<td colspan="2" class="row_header color_text">
					<?php if ($datumtitel) : ?>
						<strong>Aktuell</strong>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="text-center">
					<span class="temp"><?php echo $list[0]['temp']; ?></span>
					<?php if ($textausgabe) : ?>
						<br />
						<?php echo $list[0]['beschreibung']; ?>
					<?php endif; ?>
				</td>
				<td class="text-center">
					<img alt="<?php echo $list[0]['beschreibung']; ?>" src="modules/mod_dwd_wettermodul/icons/<?php echo $list[0]['himmel']; ?>" width="50" height="50" />
				</td>
			</tr>
			<?php if ($heutehohe) : ?>
				<tr>
					<td>H&ouml;he &uuml;. NN:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['hohe']); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($heuteluft) : ?>
				<tr>
					<td>Luftdruck:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['luft']); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($heuteregen) : ?>
				<tr>
					<td>Niederschlag:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['regen']); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindrichtung) : ?>
				<tr>
					<td>Windrichtung:</td>
					<td><?php echo htmlentities($list[0]['richtung']); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($heutewind) : ?>
				<tr>
					<td>Geschwindigkeit:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['wind']); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($heutewindspitze) : ?>
				<tr>
					<td>Windb&ouml;en:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['spitze']); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>
		<?php for ($i = 1; $i <= 3; $i++) : ?>
			<?php if (isset($list[$i])) : ?>
				<tr>
					<td colspan="2" class="row_header color_text">
						<?php if ($datumtitel) : ?>
							<?php if ($i == 1) : ?>
								<strong>Morgen</strong>
							<?php else : ?>
								<strong><?php echo ${'datum' . $i}; ?></strong>
							<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="text-center">
						<span class="temp"><?php echo $list[$i]['temp']; ?></span>
						<?php if ($textausgabe) : ?>
							<br />
							<?php echo $list[$i]['beschreibung']; ?>
						<?php endif; ?>
					</td>
					<td class="text-center">
						<img alt="<?php echo $list[$i]['beschreibung']; ?>" src="modules/mod_dwd_wettermodul/icons/<?php echo $list[$i]['himmel']; ?>" width="50" height="50" />
					</td>
				</tr>
			<?php endif; ?>
		<?php endfor; ?>
		<tr>
			<td colspan="2" class="row_header text-right">
				<small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small>
			</td>
		</tr>
	</table>
</div>
