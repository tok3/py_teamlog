<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author 		Tobias C. Koch
 * @package 	Rampenstuerung
 * @subpackage 	Ramstrg Module
 */
class Ramstrg_users_carrier_m extends MY_Model {

	public function __construct()
	{		
		parent::__construct();
		
		/**
		 * default table für rampensteuerung standorte 
		 * 
		 */
		$this->_table = 'ramstrg_users_carrier';
	}

}
