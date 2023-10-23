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

namespace Joomla\Module\AdminRevars\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

class AdminRevarsHelper
{
	/**
	 * Revars form object.
	 *
	 * @var  array|null
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected ?array $_form = null;

	/**
	 * Revars plugin object.
	 *
	 * @var  mixed
	 *
	 * @since  1.1.0
	 */
	protected static $_plugin = null;

	/**
	 * Method to get form action url.
	 *
	 * @return  string  The action url.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getAction(): string
	{
		return Route::link('administrator', 'index.php?option=com_ajax&type=module&module=admin_revars', false);
	}

	/**
	 * Method to get  form.
	 *
	 * @param   object    $module  Module data object.
	 * @param   Registry  $params  Module params.
	 *
	 * @return  Form|false  The Form object or false on error.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getForm(object $module, Registry $params)
	{
		if ($this->_form === null)
		{
			$this->_form = [];
		}

		if (!isset($this->_form[$module->id]))
		{
			if (empty($params->get('variables')))
			{
				$this->_form[$module->id] = false;

				return false;
			}

			$plugin = self::getPlugin();

			/** @var Form $form */
			$formFactory = Factory::getContainer()->get(FormFactoryInterface::class);
			$form        = $formFactory->createForm('mod_admin_revars.' . $module->id, [
				'control' => 'mod_admin_revars_' . $module->id
			]);

			$xml      = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><form/>');
			$fieldset = $xml->addChild('fieldset');
			$fieldset->addAttribute('name', 'general');

			foreach ($params->get('variables', []) as $item)
			{
				$field = $fieldset->addChild('field');
				$field->addAttribute('name', $item);

				$label = $item;
				if (!empty($plugin->params_variables[$item]))
				{
					if (!empty($plugin->params_variables[$item]['comment']))
					{
						$label = $plugin->params_variables[$item]['comment'];
					}

					$field->addAttribute('default', $plugin->params_variables[$item]['value']);
				}

				$field->addAttribute('label', $label);
				$field->addAttribute('type', 'textarea');
			}

			$form->load($xml->asXML());
			$this->_form[$module->id] = $form;

			return $form;
		}

		return $this->_form[$module->id];
	}

	/**
	 * Method to ajax save plugin params.
	 *
	 * @throws \Exception
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function getAjax(): array
	{
		if (!Session::checkToken('POST'))
		{
			throw new \Exception(Text::_('JINVALID_TOKEN_NOTICE'));
		}

		// Load language
		$app = Factory::getApplication();
		$app->getLanguage()->load('mod_admin_revars', JPATH_ADMINISTRATOR);

		// Get module_id
		$module_id = $app->input->get('module_id');
		if (empty($module_id))
		{
			throw new \Exception(Text::_('MOD_ADMIN_REVARS_ERROR_MODULE_NOT_FOUND'));
		}

		// Get plugin data
		$plugin = self::getPlugin();
		if (empty($plugin))
		{
			throw new \Exception(Text::_('MOD_ADMIN_REVARS_ERROR_PLUGIN_NOT_FOUND'));
		}

		// Get new data
		$control = 'mod_admin_revars_' . $module_id;
		$data    = $app->input->get($control, [], 'array');
		if (empty($data))
		{
			throw new \Exception(Text::_('MOD_ADMIN_REVARS_ERROR_DATA_EMPTY'));
		}

		// Prepare update data
		$update               = new \stdClass();
		$update->extension_id = $plugin->id;
		$update->params       = $plugin->params->toArray();

		$changes   = [];
		$variables = (!empty($update->params['variables'])) ? $update->params['variables'] : [];
		foreach ($variables as &$variable)
		{
			if (!empty($data[$variable['variable']]))
			{
				$changes[]         = $variable['variable'];
				$variable['value'] = $data[$variable['variable']];
			}
		}

		// Save changes
		$message = (!empty($changes)) ?
			Text::sprintf('MOD_ADMIN_REVARS_MESSAGES_CHANGES', implode(', ', $changes))
			: Text::_('MOD_ADMIN_REVARS_MESSAGES_NO_CHANGES');
		if (!empty($changes))
		{
			/** @var DatabaseDriver $db */
			$db                          = Factory::getContainer()->get(DatabaseDriver::class);
			$update->params['variables'] = $variables;
			$update->params              = (new Registry($update->params))->toString();
			$db->updateObject('#__extensions', $update, 'extension_id');

			self::$_plugin = null;
		}

		return ['message' => $message];
	}

	/**
	 * Method to get revars plugin object.
	 *
	 * @return mixed Plugin object on success, False on failure.
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public static function getPlugin()
	{
		if (self::$_plugin === null)
		{
			$plugin = PluginHelper::getPlugin('system', 'revars');
			if ($plugin)
			{
				$plugin->params           = new Registry($plugin->params);
				$variables                = (new Registry($plugin->params->get('variables')))->toArray();
				$plugin->params_variables = [];
				foreach ($variables as $v => $variable)
				{
					$variable['key']                                 = $v;
					$plugin->params_variables[$variable['variable']] = $variable;
				}
				$plugin->link = Route::link('administrator',
					'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $plugin->id);
			}

			self::$_plugin = $plugin;
		}

		return self::$_plugin;
	}
}