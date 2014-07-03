<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Tobias C. Koch
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class Ramstrg_ramp_m extends MY_Model {

   public function __construct()
   {		
	  parent::__construct();
		
	  /**
	   * default table für rampensteuerung standorte 
	   * 
	   */
	  $this->_table = 'ramstrg_sites_ramps';
   }
	
	
   //create a new item
   public function create($input)
   {
	
	  return $this->db->insert($this->_table,$input);
   }

   public function get_overview($_where = '')
   {

	  if(is_array($_where) && count($_where) > 0)
		 {
			$this->db->where($_where);
		 }
	  $this->db->select('ramstrg_sites.name AS site_name, ramstrg_sites.plz,ramstrg_sites.ort, ' . $this->_table .'.*');
	  $this->db->from($this->_table);
	  $this->db->join('ramstrg_sites', $this->_table .'.site_id = ramstrg_sites.id', 'left');
	  $this->db->order_by('ramstrg_sites.name','asc');	
	  $query = $this->db->get();
	  $result = $query->result();

	  return $result;
   }

}
