<?php
/* 
 * Used to manage all ajax calls for the modulerequirement
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since v0.2 2016/10/30
 */

require_once(dirname(__FILE__) . '/../../src/framework/moduleio.php');

class C3MainFilterAjaxModuleFrontController extends ModuleFrontController {
	//must rerord module front controller url as c3mainfilter or modify the js files
	public function __construct() {
		parent::__construct();
		if (Tools::getValue('ajax')) {
			$this->ajax = true;
		}
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$this->context = Context::getContext();
		else
			$this->context = (object) null;
	}
	
	public function init() {
		parent::init();
	}
	
	/* 
	 * process all post calls
	 * @author Schnepp David <david.schnepp@schneppd.com>
	 * @since v0.2 2016/10/30
	 */
	public function postProcess() {
		if(!$this->ajax)
			die();//do not process
		switch (Tools::getValue('action')) {
			//if call to get mainfilter choice data
			case 'get_available_choices':
				die($this->getAvailableChoices());
				break;
			default:
				exit;
		}
	}

	/* 
	 * process demands and returns corresponding data
	 * @author Schnepp David <david.schnepp@schneppd.com>
	 * @since v0.2 2016/10/30
	 * @return string:json data response
	 */
	public function getAvailableChoices() {
		$res = Array();
		
		$rawSelection = '';
		$id_filter_selection_group = 0;
		
		if(Tools::isSubmit('selection'))
			$rawSelection = (string) Tools::getValue('selection');
		if(Tools::isSubmit('id_filter_selection_group'))
			$id_filter_selection_group = (int) Tools::getValue('id_filter_selection_group');
		
		$file = '';
		if($this->isSelectionProvided($rawSelection)) {
			//process choice
		}

		if($id_filter_selection_group > 0) {
			$file = 'filter-' . $id_filter_selection_group . $file . '.json';
			$filePath = dirname(__FILE__) . '/../../../../cache/c3mainfilter-cache/' . $file;
			$fileContent = \NsC3MainFilterFramework\ModuleIO::getFileContentToString($filePath);
			return Tools::jsonEncode($fileContent);
		}

		return Tools::jsonEncode($res);
	}

	/* 
	 * tells if selection data is provided
	 * @author Schnepp David <david.schnepp@schneppd.com>
	 * @since v0.2 2016/10/30
	 * @return boolean response
	 */
	protected function isSelectionProvided(&$selection) {
		if(empty($selection))
			return false;
		
		return true;
	}
	
}