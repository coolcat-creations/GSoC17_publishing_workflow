<?php
/**
 * Items Model for a Prove Component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_prove
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       4.0
 */
namespace Joomla\Component\Workflow\Administrator\Model;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Model\ListModel;

/**
 * Model class for items
 *
 * @since  4.0
 */
class  Workflows extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'title',
				'state',
				'created_by'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = \JFactory::getApplication();
		$extension = $app->getUserStateFromRequest($this->context . '.filter.extension', 'extension', 'com_content', 'cmd');

		$this->setState('filter.extension', $extension);
		$parts = explode('.', $extension);

		// Extract the component name
		$this->setState('filter.component', $parts[0]);

		// Extract the optional section name
		$this->setState('filter.section', (count($parts) > 1) ? $parts[1] : null);
		parent::populateState($ordering, $direction);

		// TODO: Change the autogenerated stub
	}


	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\Table\Table  A JTable object
	 *
	 * @since   4.0
	 */
	public function getTable($type = 'Workflow', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  string  The query to database.
	 *
	 * @since   4.0
	 */
	public function getListQuery()
	{
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$select = $db->quoteName(array(
			'w.id',
			'w.title',
			'w.created',
			'w.published',
			'w.default',
			'u.name'
		));

		$query
			->select($select)
			->from($db->quoteName('#__workflows', 'w'))
			->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('w.created_by'));

		// Filter by extension
		if ($extension = $this->getState('filter.extension'))
		{
			$query->where($db->qn('extension') . ' = ' . $db->quote($db->escape($extension)));
		}

		// Filter by author
		if ($author = $this->getState('filter.created_by'))
		{
			$query->where($db->qn('created_by') . ' = ' . $db->quote($db->escape($author)));
		}

		// Filter by condition
		if ($condition = $this->getState('filter.state'))
		{
			$query->where($db->qn('state') . ' = ' . $db->quote($db->escape($condition)));
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where($db->qn('title') . ' LIKE ' . $search . ' OR ' . $db->qn('description') . ' LIKE ' . $search);
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'id');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->qn($db->escape($orderCol)) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
