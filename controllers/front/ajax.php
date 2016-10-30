<?php
/* 
 * Used to manage all ajax calls for the modulerequirement
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since v0.2 2016/10/30
 */

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
	 * process demand and returns corresponding data
	 * @author Schnepp David <david.schnepp@schneppd.com>
	 * @since v0.2 2016/10/30
	 * @return string:json data response
	 */
	public function getAvailableChoices() {
		$res = Array();
		$res['test'] = true;
		return Tools::jsonEncode($res);
	}

}