<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.core');

foreach ($this->levels as $key => $value)
{
	$allLevels[$value->id] = $value->title;
}

JFactory::getDocument()->addScriptOptions('menus-edit-modules', ['viewLevels' => $allLevels, 'itemId' => $this->item->id]);
JHtml::_('stylesheet', 'com_menus/admin-item-edit_modules.css', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'com_menus/admin-item-edit_modules.min.js', array('version' => 'auto', 'relative' => true));

// Set main fields.
$this->fields = array('toggle_modules_assigned','toggle_modules_published');

echo JLayoutHelper::render('joomla.menu.edit_modules', $this); ?>

<table class="table">
	<thead>
		<tr>
			<th>
				<?php echo JText::_('COM_MENUS_HEADING_ASSIGN_MODULE');?>
			</th>
			<th class="text-center">
				<?php echo JText::_('COM_MENUS_HEADING_LEVELS'); ?>
			</th>
			<th class="text-center">
				<?php echo JText::_('COM_MENUS_HEADING_POSITION'); ?>
			</th>
			<th class="text-center">
				<?php echo JText::_('COM_MENUS_HEADING_DISPLAY'); ?>
			</th>
			<th class="text-center">
				<?php echo JText::_('COM_MENUS_HEADING_PUBLISHED_ITEMS'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->modules as $i => &$module) : ?>
		<?php if (is_null($module->menuid)) : ?>
			<?php if (!$module->except || $module->menuid < 0) : ?>
				<?php $no = 'no '; ?>
			<?php else : ?>
				<?php $no = ''; ?>
			<?php endif; ?>
		<?php else : ?>
			<?php $no = ''; ?>
		<?php endif; ?>
		<?php if ($module->published) : ?>
			<?php $status = ''; ?>
		<?php else : ?>
			<?php $status = 'unpublished '; ?>
		<?php endif; ?>
		<tr class="<?php echo $no; ?><?php echo $status; ?>row<?php echo $i % 2; ?>" id="tr-<?php echo $module->id; ?>">
			<td id="<?php echo $module->id; ?>" style="width:40%">
				<?php $link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id=' . $module->id . '&amp;tmpl=component&amp;view=module&amp;layout=modal'; ?>
				<a href="#moduleEdit<?php echo $module->id; ?>Modal" role="button" data-toggle="modal" title="<?php echo JText::_('COM_MENUS_EDIT_MODULE_SETTINGS'); ?>" id="title-<?php echo $module->id; ?>">
					<?php echo $this->escape($module->title); ?></a>
			</td>
			<td id="access-<?php echo $module->id; ?>" style="width:15%" class="text-center">
				<?php echo $this->escape($module->access_title); ?>
			</td>
			<td id="position-<?php echo $module->id; ?>" style="width:15%" class="text-center">
				<?php echo $this->escape($module->position); ?>
			</td>
			<td id="menus-<?php echo $module->id; ?>" style="width:15%" class="text-center">
				<?php if (is_null($module->menuid)) : ?>
					<?php if ($module->except) : ?>
						<span class="badge badge-success">
							<?php echo JText::_('JYES'); ?>
						</span>
					<?php else : ?>
						<span class="badge badge-danger">
							<?php echo JText::_('JNO'); ?>
						</span>
					<?php endif; ?>
				<?php elseif ($module->menuid > 0) : ?>
					<span class="badge badge-success">
						<?php echo JText::_('JYES'); ?>
					</span>
				<?php elseif ($module->menuid < 0) : ?>
					<span class="badge badge-danger">
						<?php echo JText::_('JNO'); ?>
					</span>
				<?php else : ?>
					<span class="badge badge-info">
						<?php echo JText::_('JALL'); ?>
					</span>
				<?php endif; ?>
			</td>
			<td id="status-<?php echo $module->id; ?>" class="text-center">
				<?php if ($module->published) : ?>
					<span class="badge badge-success">
						<?php echo JText::_('JYES'); ?>
					</span>
				<?php else : ?>
					<span class="badge badge-danger">
						<?php echo JText::_('JNO'); ?>
					</span>
				<?php endif; ?>
			</td>
			<?php echo JHtml::_(
				'bootstrap.renderModal',
				'moduleEdit' . $module->id . 'Modal',
				array(
					'title'       => JText::_('COM_MENUS_EDIT_MODULE_SETTINGS'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $link,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<a type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true"'
							. ' onclick="jQuery(\'#moduleEdit' . $module->id . 'Modal iframe\').contents().find(\'#closeBtn\').click();">'
							. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
							. '<button type="button" class="btn btn-primary" aria-hidden="true"'
							. ' onclick="jQuery(\'#moduleEdit' . $module->id . 'Modal iframe\').contents().find(\'#saveBtn\').click();">'
							. JText::_('JSAVE') . '</button>'
							. '<button type="button" class="btn btn-success" aria-hidden="true"'
							. ' onclick="jQuery(\'#moduleEdit' . $module->id . 'Modal iframe\').contents().find(\'#applyBtn\').click();">'
							. JText::_('JAPPLY') . '</button>',
				)
			); ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
