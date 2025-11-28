<?php
/**
 * @package     Wettermodul
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

defined('_JEXEC') or die;

/**
 * The relatedsermons module service provider.
 *
 * @since  7.0.0
 */
return new class () implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	public function register(Container $container): void
	{
		$container->registerServiceProvider(new ModuleDispatcherFactory('\\Bakual\\Module\\Wetter'));
		$container->registerServiceProvider(new HelperFactory('\\Bakual\\Module\\Wetter\\Site\\Helper'));

		$container->registerServiceProvider(new Module());
	}
};