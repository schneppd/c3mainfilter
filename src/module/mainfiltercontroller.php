<?php
/*
 * Process inputs from module view(c3mainfilter),
 * tells the model what to save
 * tells the view what to display/expose to prestashop
 * 
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since v0.1 2016/09/17
 */

namespace NsC3MainFilterModule;

include_once(dirname(__FILE__) . '/mainfiltermodel.php');
include_once(dirname(__FILE__) . '/../framework/modulecontroller.php');
include_once(dirname(__FILE__) . '/../framework/moduleio.php');

class MainFilterController extends \NsC3Framework\ModuleController {

	/*
	 * the constructor
	 * 
	 * @author Schnepp David
	 * @since v0.1 2016/09/17
	 * @param ModuleInformations $infos the informations for this module
	 * @param DatabaseConnection $databaseConnection the database connection to use for the models
	 */
	public function __construct($infos, $databaseConnection) {
		parent::__construct($infos);
		$this->model = new MainFilterModel($databaseConnection);
	}

}
