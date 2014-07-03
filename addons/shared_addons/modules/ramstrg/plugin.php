<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Jerel Unruh - PyroCMS Dev Team
 * @website		http://unruhdesigns.com
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class Plugin_Ramstrg extends Plugin
{
	/**
	 * Item List
	 * Usage:
	 * 
	 * {{ ramstrg:items limit="5" order="asc" }}
	 *      {{ id }} {{ name }} {{ slug }}
	 * {{ /ramstrg:items }}
	 *
	 * @return	array
	 */
	function items()
	{
		$limit = $this->attribute('limit');
		$order = $this->attribute('order');
		
		return $this->db->order_by('name', $order)
						->limit($limit)
						->get('ramstrg_items')
						->result_array();
	}
}

/* End of file plugin.php */
