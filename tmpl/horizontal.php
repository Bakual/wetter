<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2016 - T. Hunziker / M. Bollmann
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();
?>
<div class="dwd_wettermodul">
	<table style="width: 100%; border-collapse: collapse;text-align:left;">
		<?php
		if ($titel) : ?>
			<tr><td colspan="5" style="text-align: left"><h2><?php echo $titel; ?></h2><br /></td></tr>
		<?php endif;
		if ($datumtitel) : ?>
			<tr>
				<?php
				if (isset($list[0])) : ?>
					<td colspan="2" style="border:none; color: <?php echo $zweitfarbe; ?>;text-align: center;">
						<strong>Aktuell</strong>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif;

				if (isset($list[1])) : ?>
					<td style="border-left: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;text-align: center;">
						<strong>Morgen</strong>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif;

				if (isset($list[2])) : ?>
					<td style="border-left: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;text-align: center;">
						<strong><?php echo $datum2; ?></strong>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif;

				if (isset($list[3])) : ?>
					<td style="border-left: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>; text-align: center;">
						<strong><?php echo $datum3; ?></strong>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif; ?>
			</tr>
		<?php endif; ?>
		<tr>
			<?php
			if (isset($list[0])) : ?>
				<td colspan="2" style="border: none;color: <?php echo $farbe; ?>; text-align: center;">
					<img alt="<?php echo $list[0]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[0]['himmel']; ?>" width="100" height="100" />
				</td>
			<?php else : ?>
				<td></td>
			<?php endif;

			if (isset($list[1])) : ?>
				<td style="border-left: 1px solid <?php echo $farbe; ?>; text-align: center;">
					<img alt="<?php echo $list[1]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[1]['himmel']; ?>" width="100" height="100" />
				</td>
			<?php else : ?>
				<td></td>
			<?php endif;

			if (isset($list[2])) : ?>
				<td style="border-left: 1px solid <?php echo $farbe; ?>; text-align: center;">
					<img alt="<?php echo $list[2]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[2]['himmel']; ?>" width="100" height="100" />
				</td>
			<?php else : ?>
				<td></td>
			<?php endif;

			if (isset($list[3])) : ?>
				<td style="border-left: 1px solid <?php echo $farbe; ?>; text-align: center;">
					<img alt="<?php echo $list[3]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[3]['himmel']; ?>" width="100" height="100" />
				</td>
			<?php else : ?>
				<td></td>
			<?php endif; ?>
		</tr>
		<tr>
			<?php
			if (isset($list[0])) : ?>
				<td colspan="2" style="border: none;color: <?php echo $farbe; ?>;text-align: center; padding:8px;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[0]['temp']; ?></span>
				</td>
			<?php else : ?>
				<td></td>
			<?php endif;

			if (isset($list[1])) : ?>
				<td style="border-left: 1px solid <?php echo $farbe; ?>;color: <?php echo $farbe; ?>; text-align: center; padding:8px;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[1]['temp']; ?></span>
				</td>
			<?php else : ?>
				<td></td>
			<?php endif;

			if (isset($list[2])) : ?>
				<td style="border-left: 1px solid <?php echo $farbe; ?>;color: <?php echo $farbe; ?>; text-align: center; padding:8px;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[2]['temp']; ?></span>
				</td>
			<?php else : ?>
				<td></td>
			<?php endif;

			if (isset($list[3])) : ?>
				<td style="border-left: 1px solid <?php echo $farbe; ?>; color: <?php echo $farbe; ?>; text-align: center; padding:8px;">
					<span style="font-size: large; color: <?php echo $farbe; ?>;"><?php echo $list[3]['temp']; ?></span>
				</td>
			<?php else : ?>
				<td></td>
			<?php endif; ?>
		</tr>
		<?php
		if ($textausgabe) : ?>
			<tr>
				<?php if (isset($list[0])) : ?>
					<td colspan="2" style="border:none;text-align: center;">
						<?php echo $list[0]['beschreibung']; ?>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif;

				if (isset($list[1])) : ?>
					<td style="border-left: 1px solid <?php echo $farbe; ?>; text-align: center;">
						<?php echo $list[1]['beschreibung']; ?>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif;

				if (isset($list[2])) : ?>
					<td style="border-left: 1px solid <?php echo $farbe; ?>; text-align: center;">
						<?php echo $list[2]['beschreibung']; ?>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif;

				if (isset($list[3])) : ?>
					<td style="border-left: 1px solid <?php echo $farbe; ?>; text-align: center;">
						<?php echo $list[3]['beschreibung']; ?>
					</td>
				<?php else : ?>
					<td></td>
				<?php endif; ?>
			</tr>
		<?php endif;

		if (isset($list[0])) :
			if ($heutehohe) : ?>
				<tr>
					<td>H&ouml;he &uuml;. NN:</td>
					<td nowrap="nowrap" style="border:none;"><?php echo htmlentities($list[0]['hohe']); ?></td>
					<?php
					if (isset($list[1])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[2])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[3])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endif;

			if ($heuteluft) : ?>
				<tr>
					<td>Luftdruck:</td>
					<td nowrap="nowrap" style="border:none;"><?php echo htmlentities($list[0]['luft']); ?></td>
					<?php
					if (isset($list[1])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[2])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[3])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endif;

			if ($heuteregen) : ?>
				<tr>
					<td>Niederschlag:</td>
					<td nowrap="nowrap" style="border:none;"><?php echo htmlentities($list[0]['regen']); ?></td>
					<?php
					if (isset($list[1])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[2])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[3])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endif;

			if ($heutewindrichtung) : ?>
				<tr>
					<td>Windrichtung:</td>
					<td style="border:none;"><?php echo htmlentities($list[0]['richtung']); ?></td>
					<?php
					if (isset($list[1])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[2])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[3])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endif;

			if ($heutewind) : ?>
				<tr>
					<td>Geschwindigkeit:</td>
					<td nowrap="nowrap" style="border:none;"><?php echo htmlentities($list[0]['wind']); ?></td>
					<?php
					if (isset($list[1])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[2])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[3])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endif;

			if ($heutewindspitze) : ?>
				<tr>
					<td>Windb&ouml;en:</td>
					<td nowrap="nowrap" style="border:none;"><?php echo htmlentities($list[0]['spitze']); ?></td>
					<?php
					if (isset($list[1])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[2])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif;

					if (isset($list[3])) : ?>
						<td style="border-left: 1px solid <?php echo $farbe; ?>;"></td>
					<?php else : ?>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endif;
		endif;
		// Bitte beachten: Die Entfernung der Copyrighthinweise stellt eine Urheberrechtsverletzung dar! ?>
		<tr>
			<td colspan="5" style="text-align: right; font-size: 11px; color:gray; border:none; padding:5px; nowrap;">
				&copy; Deutscher Wetterdienst | <a title="Ferienhaus an der Ostsee" href="http://www.stranddorf.de">
					<img alt="Ferienhaus Ostsee" src="<?php echo 'modules/mod_dwd_wettermodul/icons/icon.png'; ?>" width="10" height="10" style="border: none; vertical-align:middle; display: inline-block;" />
				</a>
			</td>
		</tr>
	</table>
</div>