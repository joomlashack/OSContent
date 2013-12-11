<?php
/*
* OSContent for Joomla 1.7.X
* @version 1.5
* @Date 04.10.2009
* @copyright (C) 2007-2009 Johann Eriksen
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Official website: http://www.baticore.com
*/

defined('_JEXEC') or die();

class OSContentControllerDelete extends JController
{

	function __construct()
	{
		parent::__construct();

		 //Register Extra tasks
		 //$this->registerTask( 'create'  , 	'deleteOSContent' );
	}

	/**
	 * display the form
	 * @return void
	 */
	function display()
	{
		JRequest::setVar( 'view', 'delete' );
		parent::display();
	}

	/**
	 * save sections
	 */
	function delete()
	{
		$model = $this->getModel('delete');
		if(!$model->deleteOSContent()) {
			$msg = JText::_( "ERROR_DELETE" );
		} else {
			$msg = JText::_( "SUCCESS_DELETE");
		}

		$this->setRedirect( "index.php?option=com_oscontent&view=delete",$msg );
	}

}
?>
