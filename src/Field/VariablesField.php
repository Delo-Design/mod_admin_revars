<?php
/*
 * @package     Revars Administrator Module
 * @version     __DEPLOY_VERSION__
 * @author      Delo Design - delo-design.ru
 * @copyright   Copyright (c) 2023 Delo Design. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link        https://delo-design.ru/
 */

namespace Joomla\Module\AdminRevars\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\Module\AdminRevars\Administrator\Helper\AdminRevarsHelper;
use Joomla\Registry\Registry;

class VariablesField extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'variables';

	/*
	* Method to get the field options.
	*
	* @throws  \Exception
	*
	* @return  array  The field option objects.
	*
	* @since __DEPLOY_VERSION__
	*/
	protected function getOptions(): array
	{
		// Prepare options
		$options = parent::getOptions();

		$plugin = AdminRevarsHelper::getPlugin();
		if ($plugin)
		{
			foreach ($plugin->params_variables as $variable)
			{
				if (empty($variable['variable']))
				{
					continue;
				}
				$option        = new \stdClass();
				$option->text  = (!empty($variable['comment'])) ? $variable['comment'] : $variable['variable'];
				$option->value = $variable['variable'];

				$options[] = $option;
			}
		}

		return $options;
	}
}