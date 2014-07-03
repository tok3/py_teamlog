<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Tobias C. Koch
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class Ramstrg_m extends MY_Model {

	public function __construct()
	{		
		parent::__construct();
		
		/**
		 * default table für rampensteuerung standorte 
		 * 
		 */
		$this->_table = 'ramstrg_sites';
	}
	
	//create a new item
	public function create($input)
	{
		$to_insert = array(
			'name' => $input['name'],
			'slug' => $this->_check_slug($input['slug']),
			'str' => $input['str'],
			'nr' => $input['nr'],
			'plz' => $input['plz'],
			'ort' => $input['ort'],

		);

		return $this->db->insert('ramstrg_sites', $to_insert);
	}

	//make sure the slug is valid
	public function _check_slug($slug)
	{
		$slug = strtolower($slug);
		$slug = preg_replace('/\s+/', '-', $slug);

		return $slug;
	}
}
