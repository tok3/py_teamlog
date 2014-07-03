<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Tobias C. Koch
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class Ramstrg_sites_holidays_m extends MY_Model {

   public function __construct()
   {		
	  parent::__construct();
		
	  /**
	   * default table für rampensteuerung standorte 
	   * 
	   */
	  $this->_table = 'ramstrg_sites_holidays';
   }
	
   //create a new item
   public function create($input)
   {
	  return $this->db->insert($this->_table , $to_insert);
   }

   /**
	* get business hours
	*
	* @return void
	* @author 
	**/
   public function get($_id)
   {
	
	  $this->db->select('*')
		 ->from($this->_table)
		 ->where('site_id', $_id)
		 ->order_by('date_start', 'asc')
		 ->order_by('time_start','asc');

	  $query = $this->db->get();

	  $result = $query->result();

	  return $result;
   }



   /**
	* delete column
	*
	* @return void
	* @author 
	**/
   public function del_by_col ($_id, $_col = 'site_id')
   {
	  if(is_array($_id))
		 {
			foreach ($_id as $key => $value) 
			   {
				  $this->db->delete($this->_table, array($_col => $value));
			   }
		 }
	  else
		 {
			$this->db->delete($this->_table, array($_col => $_id));	
		 }
	
	  echo $this->db->last_query();

   }


   // --------------------------------------------------------------------
   /**
	* check ob gegebene zeit in betriebsferien des standortes liegt
	* 
	* @access 	public	
	* @param 	unix timestamp	
	* @param 	integer 	standort id
	* @return 	boolean	
	* 
	*/
   public function is_holiday($_tstamp, $_site_id)
   {
	 
	  $time = date('YmdHis',$_tstamp);

	  $this->db->select("*, CONCAT(REPLACE(date_start,'-',''), REPLACE(time_start, ':', ''),'') AS start_comp,  CONCAT(REPLACE(date_end,'-',''), REPLACE(time_end, ':', ''),'') AS end_comp");
	  $this->db->from($this->_table);
 	  $this->db->having("CONCAT(REPLACE(date_start,'-',''), REPLACE(time_start, ':', ''),'') <= " .$time);
 	  $this->db->having("CONCAT(REPLACE(date_end,'-',''), REPLACE(time_end, ':', ''),'') >= " .($time +1));

	  $this->db->where('site_id',$_site_id);


	  $query = $this->db->get();

	  $result = $query->result();
	 
	  if(count($result) > 0)
		 {
			return TRUE;
		 }
	  else
		 {
			return FALSE;
		 }

   }

}
