<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();
?>
<div class="dwd_wettermodul">
	<table style="width: 100%; border-collapse: collapse;text-align:left;">
		<?php
		if ($titel) : ?>
			<tr><td colspan="2" style="text-align: left"><h2><?php echo $titel; ?></h2></td></tr>
		<?php endif;

		if (isset($list[0])) : ?>
			<tr>
				<td colspan="2" style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;">
					<?php if ($datumtitel) : ?>
						<strong>Aktuell</strong>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="text-align: center;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[0]['temp']; ?></span>
					<?php
					if ($textausgabe) : ?>
						<br />
						<?php echo $list[0]['beschreibung'];
					endif; ?>
				</td>
				<td style="text-align: center">
					<img alt="<?php echo $list[0]['beschreibung']; ?>" src="<?php echo JURI::base() . '/modules/mod_dwd_wettermodul/icons/' . $list[0]['himmel']; ?>" width="50" height="50" />
				</td>
			</tr>
			<?php
			if ($heutehohe) : ?>
				<tr>
					<td>H&ouml;he &uuml;. NN:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['hohe']); ?></td>
				</tr>
			<?php endif;

			if ($heuteluft) : ?>
				<tr>
					<td>Luftdruck:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['luft']); ?></td>
				</tr>
			<?php endif;

			if ($heuteregen) : ?>
				<tr>
					<td>Niederschlag:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['regen']); ?></td>
				</tr>
			<?php endif;

			if ($heutewindrichtung) : ?>
				<tr>
					<td>Windrichtung:</td>
					<td><?php echo htmlentities($list[0]['richtung']); ?></td>
				</tr>
			<?php endif;

			if ($heutewind) : ?>
				<tr>
					<td>Geschwindigkeit:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['wind']); ?></td>
				</tr>
			<?php endif;

			if ($heutewindspitze) : ?>
				<tr>
					<td>Windb&ouml;en:</td>
					<td nowrap="nowrap"><?php echo htmlentities($list[0]['spitze']); ?></td>
				</tr>
			<?php endif;
		endif;
 
		if (isset($list[1])) : ?>
			<tr>
				<td colspan="2" style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;">
					<?php
					if ($datumtitel) : ?>
						<strong>Morgen</strong>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="text-align: center;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[1]['temp']; ?></span>
					<?php
					if ($textausgabe) : ?>
						<br />
						<?php echo $list[1]['beschreibung'];
					endif; ?>
				</td>
				<td style="text-align: center">
					<img alt="<?php echo $list[1]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[1]['himmel']; ?>" width="50" height="50" />
				</td>
			</tr>
		<?php endif;

		if (isset($list[2])) : ?>
			<tr>
				<td colspan="2" style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;">
					<?php
					if ($datumtitel) : ?>
						<strong><?php echo $datum2; ?></strong>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="text-align: center;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[2]['temp']; ?></span>
					<?php
					if ($textausgabe) : ?>
						<br />
						<?php echo $list[2]['beschreibung'];
					endif; ?>
				</td>
				<td style="text-align: center">
					<img alt="<?php echo $list[2]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[2]['himmel']; ?>" width="50" height="50" />
				</td>
			</tr>
		<?php endif;

		if (isset($list[3])) : ?>
			<tr>
				<td colspan="2" style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;">
					<?php
					if ($datumtitel) : ?>
						<strong><?php echo $datum3; ?></strong>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="text-align: center;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[3]['temp']; ?></span>
					<?php
					if ($textausgabe) : ?>
						<br />
						<?php echo $list[3]['beschreibung'];
					endif; ?>
				</td>
				<td style="text-align: center">
					<img alt="<?php echo $list[3]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[3]['himmel']; ?>" width="50" height="50" />
				</td>
			</tr>
		<?php endif;
		// Bitte beachten: Die Entfernung der Copyrighthinweise stellt eine Urheberrechtsverletzung dar! ?>
		<tr>
			<td colspan="2" style="text-align: right; font-size: 11px; color:gray; border-top: 1px solid <?php echo $farbe; ?>;">
				&copy; Deutscher Wetterdienst | <a title="Wetter Ostsee" href="http://www.wetter-ostsee.de">
					<img alt="Wetter Ostsee" src="<?php echo JURI::base() . '/modules/mod_dwd_wettermodul/icons/icon.png'; ?>" width="10" height="10" style="border: none; vertical-align:middle;" />
				</a>
			</td>
		</tr>
	</table>
</div>
