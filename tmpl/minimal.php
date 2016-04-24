<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2016 - T. Hunziker /M. Bollmann
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();
?>
<div class="dwd_wettermodul">
	<table style="width: 100%; border-collapse: collapse;text-align:left;">
		<?php
		if ($titel) : ?>
			<tr><td colspan="3" style="text-align: left"><?php echo $titel; ?></td></tr>
		<?php endif;

		if (isset($list[0])) : ?>
			<tr>
				<td style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;width: 33%;">Aktuell</td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center;">
					<span style="color: <?php echo $farbe; ?>;"><?php echo $list[0]['temp']; ?></span>
				</td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center">
					<img alt="<?php echo $list[0]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[0]['himmel']; ?>" width="26" height="26" />
				</td>
			</tr>
		<?php endif;

		if (isset($list[1])) : ?>
			<tr>
				<td style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;width: 33%;">Morgen</td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center;">
					<span style="color: <?php echo $farbe; ?>;"><?php echo $list[1]['temp']; ?></span>
				</td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center">
					<img alt="<?php echo $list[1]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[1]['himmel']; ?>" width="26" height="26" />
				</td>
			</tr>
		<?php endif;

		if (isset($list[2])) : ?>
			<tr>
				<td style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;width: 33%;"><?php echo $datum2; ?></td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center;">
					<span style="color: <?php echo $farbe; ?>;"><?php echo $list[2]['temp']; ?></span>
				</td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center">
					<img alt="<?php echo $list[2]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[2]['himmel']; ?>" width="26" height="26" />
				</td>
			</tr>
		<?php endif;

		if (isset($list[3])) : ?>
			<tr>
				<td style="border-top: 1px solid <?php echo $farbe; ?>;color: <?php echo $zweitfarbe; ?>;width: 33%;"><?php echo $datum3; ?></td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center;">
					<span style="color: <?php echo $farbe; ?>;"><?php echo $list[3]['temp']; ?></span>
				</td>
				<td style="border-top: 1px solid <?php echo $farbe; ?>; text-align: center">
					<img alt="<?php echo $list[3]['beschreibung']; ?>" src="<?php echo 'modules/mod_dwd_wettermodul/icons/' . $list[3]['himmel']; ?>" width="26" height="26" />
				</td>
			</tr>
		<?php endif;
		// Bitte beachten: Die Entfernung der Copyrighthinweise stellt eine Urheberrechtsverletzung dar! ?>
		<tr><td colspan="3" style="text-align: right; font-size: 11px; color:gray; border:none;padding:5px; border-top: 1px solid <?php echo $farbe; ?>; nowrap;">
			&copy; Deutscher Wetterdienst | <a title="Ferienhaus an der Ostsee" href="http://www.stranddorf.de">
					<img alt="Ferienhaus Ostsee" src="<?php echo 'modules/mod_dwd_wettermodul/icons/icon.png'; ?>" width="10" height="10" style="border: none; vertical-align:middle; display: inline-block;" />
				</a>
		</td></tr>
	</table>
</div>
