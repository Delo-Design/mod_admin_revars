<?php
/*
 * @package     RadicalMart Filter Module
 * @subpackage  mod_radicalmart_filter
 * @version     __DEPLOY_VERSION__
 * @author      Delo Design - delo-design.ru
 * @copyright   Copyright (c) 2022 Delo Design. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link        https://delo-design.ru/
 */

namespace Joomla\Module\AdminRevars\Administrator\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\Module\AdminRevars\Administrator\Helper\AdminRevarsHelper;
use Joomla\Module\AdminRevars\Administrator\Field\VariablesField;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

	/**
	 * Returns the layout data.
	 *
	 * @throws \Exception
	 *
	 * @return  array Module layout data.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getLayoutData(): array
	{
		$data = parent::getLayoutData();

		/** @var AdminRevarsHelper $helper */
		$helper = $this->getHelperFactory()->getHelper('AdminRevarsHelper');

		$data['action'] = $helper->getAction();
		$data['form']   = $helper->getForm($data['module'], $data['params']);
		$data['plugin'] = AdminRevarsHelper::getPlugin();
		$app            = Factory::getApplication();

		// Load assets
		/** @var \Joomla\CMS\WebAsset\WebAssetRegistry $assetsRegistry */
		$assetsRegistry = $app->getDocument()->getWebAssetManager()->getRegistry();
		$assetsRegistry
			->addExtensionRegistryFile('mod_admin_revars')
			->addExtensionRegistryFile('com_radicalmart');

		return $data;
	}
}