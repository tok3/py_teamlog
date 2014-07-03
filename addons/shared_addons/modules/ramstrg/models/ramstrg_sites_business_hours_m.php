<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Tobias C. Koch
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class Ramstrg_sites_business_hours_m extends MY_Model {

   public function __construct()
   {		
	  parent::__construct();
		
	  /**
	   * default table fÃ¼r rampensteuerung standorte 
	   * 
	   */
	  $this->_table = 'ramstrg_sites_business_hours';
   }
	
   //create a new item
   public function create($input)
   {
	  return $this->db->insert($this->_table, $to_insert);
   }

   /**
	* get business hours
	*
	* @return void
	* @author 
	**/
   public function getBH ($_id)
   {
	
	  $this->db->select('*')
		 ->from($this->_table)
		 ->where('site_id', $_id)
		 ->order_by('day_start', 'asc')
		 ->order_by('time_start','asc');

	  $query = $this->db->get();

	  $result = $query->result();

	  return $result;
   }

   /**
	* delete business hours by site_id
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
	* check ob gegebene zeit in geschäftszeit des standortes liegt
	* 
	* @access 	public	
	* @param 	unix timestamp	
	* @param 	integer 	standort id
	* @return 	boolean	
	* 
	*/
   public function in_business($_tstamp, $_site_id)
   {

	  $day =  date('w', $_tstamp);
	  $time =  date('His', $_tstamp);


	  $this->db->select("*");
	  $this->db->from($this->_table);
	  $this->db->where('day_start <= ' .$day);
	  $this->db->where('day_end >= ' .$day);

	  $this->db->having("REPLACE(time_start, ':', '') <= " .$time);
	  $this->db->having("REPLACE(time_end, ':', '') >= " .$time);

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
