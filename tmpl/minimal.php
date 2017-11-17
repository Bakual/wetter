<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - T. Hunziker /M. Bollmann
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JFactory::getDocument()->addStyleDeclaration(
	'.dwd_wettermodul table {
		width: 100%;
	}
	.dwd_wettermodul table td {
		border-top: 1px solid ' . $farbe . ';
		width: 33%;
	}
	.dwd_wettermodul .color_text {
		color: ' . $zweitfarbe .';
	}
	.dwd_wettermodul .temp {
		color: ' . $farbe .';
	}'
);
?>
<div class="dwd_wettermodul">
	<?php if ($titel) : ?>
		<?php echo $titel; ?>
	<?php endif; ?>
	<table>
		<?php for ($i = 0; $i <= 3; $i++) : ?>
			<?php if (isset($list[$i])) : ?>
				<tr>
					<td class="color_text">
						<?php if (!$i) : ?>
							Aktuell
						<?php elseif ($i == 1) : ?>
							Morgen
						<?php else : ?>
							<?php echo ${'datum' . $i}; ?>
						<?php endif; ?>
					</td>
					<td class="text-center">
						<span class="temp"><?php echo $list[$i]['temp']; ?></span>
					</td>
					<td class="text-center">
						<img alt="<?php echo $list[$i]['beschreibung']; ?>" src="modules/mod_dwd_wettermodul/icons/<?php echo $list[$i]['himmel']; ?>" width="26" height="26" />
					</td>
				</tr>
			<?php endif; ?>
		<?php endfor; ?>
	</table>
	<div class="text-right"><small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small></div>
</div>
