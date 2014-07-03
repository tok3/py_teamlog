<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	ramstrg 
 * @subpackage 	Ramstrg Module
 */
class trucks extends Public_Controller
{
   private $trucks_m;

   public function __construct()
   {
	  parent::__construct();
	  Events::trigger('is_logged_in');

	  $this->load->library('form_validation');
	  $this->trucks_m = $this->load->model('ramstrg_trucks_m');

	  $this->lang->load('ramstrg');

	  $this->template
		 ->append_css('module::ramstrg.css')
		 ->append_css('module::calendar.css')
		 ->append_css('module::jquery.datetimepicker.css')
		 ->append_css('module::tablesorter/style.css')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::tablesorter/js/jquery.tablesorter.min.js')
		 ->append_js('module::ramstrg.js');
   }

   // --------------------------------------------------------------------
   /**
	* start here
	*/

   public function index($offset = 0)
   {

	  

	  $data = $this->format->object_to_array($this->ramstrg_trucks_m->get_all());

	  // grid konfigurieren und zurückgeben 
	  $conf['id'] = 'truckGrid';

	  $cols = array('name','lic_number','id');

	  $truckGrid = new sortable_grid($conf);
	  // $truckGrid->copy_col('id','del_id');

	  $truckGrid->set_heading(array('Name','Kennzeichen',''));
	  $truckGrid->set_edit_link(array('id'=>$this->router->fetch_module() .'/'. $this->router->fetch_class() . '/edit'));
	  $gridData = $truckGrid->arrangeCols($data,$cols);

	  $grid = $truckGrid->getGrid($gridData);



	  $this->template
		 ->title('')
		 ->set('addTruck',$grid)
		 ->set('editLink',$this->router->fetch_module() .'/'. $this->router->fetch_class() . '/edit')
		 ->set('grid',$grid)
		 ->build('trucks_list');

   }


   // --------------------------------------------------------------------
   /**
	* lkw details
	* 
	* @access 	public	
	* @param 	int	lkw id
	* @return 	output	
	* 
	*/
   public function edit($_truck_id = 0)
   {
	  $this->load->library('form_validation');

	  $data = $this->input->post('data');

	  $validation_rules = array(
								array(
									  'field'   => 'data[name]',
									  'label'   => 'Name',
									  'rules'   => 'required'
									  ),
				
								array(
									  'field'   => 'data[lic_number]',
									  'label'   => 'Kennzeichen',
									  'rules'   => 'required'
									  )
								);

	  $this->form_validation->set_rules($validation_rules);

	  $errors = '';

	  
	  if($this->input->post('submit'))
		 {

			if ($this->form_validation->run() === FALSE)
			   {
				  $errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() .'
				</div></div>';
			   }
			else
			   {
				  $data = $this->input->post('data');
				  if($_truck_id < 1)
					 {
						//insert

						$data['carrier_id'] = $this->session->userdata('carrier_id');

						$_truck_id = $this->trucks_m->insert($data);
						redirect(current_url() . '/' . $_truck_id);
			
					 }
				  else
					 {

						$this->trucks_m->update($_truck_id, $data);

						redirect(current_url());
						
					 }


			   }

		 }

	  $data = $this->ramstrg_trucks_m->get_by('id', $_truck_id);

	  $this->template
		 ->title('')
		 ->append_js('module::calendar.js')
		 ->set('fields',$this->trucks_form_fields($data))
		 ->set('backlink',$this->router->fetch_module() .'/'. $this->router->fetch_class())
		 ->set('form_errors', $errors)
		 ->build('truck_details');

   }

   // --------------------------------------------------------------------
   /**
	* trucks detail form
	* 
	* @access 	private	
	* @param 	array 	
	* @return 	array	
	* 
	*/
   private function trucks_form_fields($data = '')
   {
	  if(is_object($data))
		 {
			$data = (array) $data;
		 }	

	  $namePreFx = '';
	  $idPreFx = 'trucks_';

	  if($this->input->post('data'))
		 {
			$data = $this->input->post('data');
		 }
	  else
		 {
			$data = $data;
		 }


	  $formfields['open'] = form_open();
	  $formfields['close'] = form_close();

	  $formfields['delete'] = '';
	  if($this->uri->segment(4) != "")
		 {
			$formfields['delete'] = '<a href="' . $this->router->fetch_module() .'/'. $this->router->fetch_class() . '/delete/' . $this->uri->segment(4) . '" class="button secondary tiny radius delBtn">L&ouml;schen&nbsp;<i class="fi-trash size-14">&nbsp;</i></a>&nbsp;';
		 }
	  $formfields['submit'] = '<button name="submit" value="1" type="submit" class="tiny radius">Speichern&nbsp;<i class="fi-save size-14">&nbsp;</i></button>';


	  $fields = $this->db->field_data('ramstrg_trucks');

	  foreach ($fields as $field)
		 {
			$field->name;
			$field->type;
			$field->max_length;
			$field->primary_key;


			$value = set_value('data[date]', isset($data[$field->name]) ? $data[$field->name] : '');
	
			if($field->name == 'lic_number')
			   {
				  $value = strtoupper($value);
			   }
			// standard input	  
			$conf = array(
						  'name'        => 'data[' . $field->name . ']',
						  'id'          => $idPreFx  . $field->name,
						  'maxlength'   => $field->max_length,
						  'value'       => $value,
						  );

			$formfields[$namePreFx . $field->name] = form_input($conf);

		 } 

	  return $formfields;
   } 

   // --------------------------------------------------------------------
   /**
	* delete truck
	* 
	* @access 	public	
	* @param 	integer	truck id	
	* @return 	void	
	* 
	*/
   public function delete($_truck_id)
   {
	  $this->ramstrg_trucks_m->delete($_truck_id);
	  redirect($this->router->fetch_module() .'/'. $this->router->fetch_class());

   }
   // --------------------------------------------------------------------
   /**
	* sortable grid generieren 
	* 
	* @access 	private	
	* @param 	array	result array
	* @param	array	grid config
	* @parram 	array	cols to display
	* @param 	array	column heading 	
	*/
   public function getDefGrid($_data, $_conf = array(), $cols = '', $heading = '')
   {

	  if($_data == FALSE)
		 {
			return FALSE;
		 }

	  $grid = new sortable_grid($_conf); // instantiate grid

	  if($cols != '')
		 {
			$_data = $grid->arrangeCols($_data,$cols); // nur spalten aus $cols anzeigen
		 }

	  if($heading != '')
		 {
			$grid->set_heading($heading); // überschriften für columnen setzen
		 }

	  return $grid->getGrid($_data);

   }

}
