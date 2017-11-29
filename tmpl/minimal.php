<?php
/**
 * @package         Wettermodul
 * @author          Thomas Hunziker <admin@bakual.net>
 * @copyright   (C) 2017 - T. Hunziker /M. Bollmann
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

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
			<?php $row = $list[$day . ' 18:00'] ?>
            <tr>
                <td class="color_text">
					<?php if (!$i) : ?>
                        Aktuell
					<?php elseif ($i == 1) : ?>
                        Morgen
					<?php else : ?>
						<?php echo $day; ?>
					<?php endif; ?>
                </td>
                <td class="text-center">
                    <span class="temp"><?php echo $row['TT']; ?>°C</span>
                </td>
                <td class="text-center">
                    <img alt=""
                         src="modules/mod_dwd_wettermodul/icons/<?php echo ModDwdwetterHelper::getIcon($row, $time); ?>"
                         width="26" height="26"/>
                </td>
            </tr>
		<?php endforeach; ?>
    </table>
    <div class="text-right">
        <small><a href="http://www.dwd.de/">&copy; Deutscher Wetterdienst</a></small>
    </div>
</div>
