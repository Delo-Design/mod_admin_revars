<?php
/*
 * @package     Revars Administrator Module
 * @version     __DEPLOY_VERSION__
 * @author      Delo Design - delo-design.ru
 * @copyright   Copyright (c) 2023 Delo Design. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link        https://delo-design.ru/
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * Template variables
 * -----------------
 *
 * @var  Form|false $form   Filter form object.
 * @var  string     $action Form action href.
 * @var  object     $plugin Revars plugin object..
 * @var   object    $module Module object.
 * @var   Registry  $params Module params.
 */

if (empty($form) || empty($plugin))
{
	return false;
}

// Load asses
/** @var Joomla\CMS\WebAsset\WebAssetManager $assets */
$assets = Factory::getApplication()->getDocument()->getWebAssetManager();
$assets->useScript('mod_admin_revars.ajax');

?>
<form action="<?php echo $action; ?>" method="get" onsubmit="window.ModAdminRevars.ajaxSubmit(event);"
	  class="px-3 pb-3">
	<div class="mb-3">
		<a href="<?php echo $plugin->link; ?>" target="_blank">
			<?php echo Text::_('MOD_ADMIN_REVARS_GO_TO_PLUGIN'); ?>
		</a>
	</div>
	<div>
		<?php foreach ($form->getFieldsets() as $fieldset)
		{
			echo $form->renderFieldset($fieldset->name);
		} ?>
		<input type="hidden" name="module_id" value="<?php echo $module->id; ?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<div>
		<button type="submit" class="btn btn-primary"><?php echo Text::_('MOD_ADMIN_REVARS_SUBMIT'); ?></button>
	</div>
</form>