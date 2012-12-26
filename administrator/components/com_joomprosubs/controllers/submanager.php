<?php
/**
 * @version		$Id: submanager.php 276 2011-08-12 19:18:08Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Joomprosubs list controller class.
 *
 * @since		1.6
 */
class JoomproSubsControllerSubManager extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = 'Subscription', $prefix = 'JoomproSubsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Function to process csv report task
	 */
	public function csvReport()
	{
		$model = $this->getModel('CSVReport', 'JoomproSubsModel', 
			array('ignore_request' => true));
		$model->setModelState();
		$data = $model->getItems();
		$this->exportReport($data);
	}

	/**
	 * Function to export the report as CSV file
	 *
	 * @param	array	$data	Array of data objects from query
	 *
	 * @return	boolean	true if successful, false otherwise
	 */
	protected function exportReport($data)
	{
		// Set headers
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename='.'subscriptions.csv');

		if ($fp = fopen('php://output', 'w')) {
			// Output the first row with column headings
			if ($data[0]) {
				fputcsv($fp, array_keys(JArrayHelper::fromObject($data[0])));
			}

			// Output the rows
			foreach ($data as $row) {
				fputcsv($fp, JArrayHelper::fromObject($row));
			}
				
			// Close the file
			fclose($fp);
		}
		JFactory::getApplication()->close();
	}
} // end of class