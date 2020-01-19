<?php


jimport('joomla.filesystem.file');
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * CSV upload Model
 *
 * @since  0.0.1
 */
class CoursebookModelCsv extends JModelAdmin
{
	public function parseCsv(){
	
		$category = JFactory::getApplication()->input->get('category_id');
		$file = JFactory::getApplication()->input->files->get('file');

		if(empty($file)){
			$this->setError(JText::_('COM_COURSEBOOK_NO_FILE_SELECTED'));

			return false;
		}

		// db drivers
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = array('task', 'answer', 'category_id', 'published');
		$values = [];

		if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
    		while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
		        foreach ($data as $str){
		        	if(!empty($str)){
		        		$question = explode("{", $str)[0].'{}'.explode("}", $str)[1]; // TODO красивее сделать!
		        		$answer = explode("}",explode("=", $str)[1])[0];

		        		$values[] = '"'.$question.'","'.$answer.'", "'.$category.'", 1';
		        	}

		        }
		    }
		    // Prepare the insert query.
			$query
				->insert($db->quoteName('#__coursebook_tasks'))
				->columns($columns)
				->values($values);

			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			$db->execute();

    	fclose($handle);
    	}

	}


	public function getForm($data = array(), $loadData = false)
	{
		// Get the form.
		$form = $this->loadForm('com_coursebook.csv', 'csv');

		return $form;
	}

}