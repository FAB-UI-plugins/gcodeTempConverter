<?php
/*
Plugin Name: Gcode Temp Converter
Plugin URI: https://github.com/tohara/FAB-UI-plugins
Version: 0.1
Description: Update gcode file bed and extruder temperature settings.
Author: Tom Haraldseid
Author URI:
Plugin Slug: gcodeTempConverter
Icon:
*/
 

 
class gcodeTempConverter extends Plugin {

public function __construct()
	{
		parent::__construct();
			
		$this->lang->load($_SESSION['language']['name'], $_SESSION['language']['name']);
		$this->layout->add_css_file(array('src'=>'application/plugins/gcodeTempConverter/assets/css/filemanager.css', 'comment'=>'css for filemanager module'));
		$this->layout->add_css_file(array('src'=>'application/plugins/gcodeTempConverter/assets/css/icons.css', 'comment'=>''));
		
		
		$this->load->helper('url');
		
		define('MY_PLUGIN_URL', site_url().'plugin/gcodeTempConverter/');
		define('MY_PLUGIN_PATH', PLUGINSPATH.'gcodeTempConverter/');
	}

	public function index(){

		//carico X class database
		$this->load->database();
		$this->load->model('objects');
		//~ $this->load->model('tasks');
		
		
		//carico helpers
		$this->load->helper('ft_file_helper');
		$this->load->helper('smart_admin_helper');
		$this->load->helper('ft_date_helper');

		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js', 'comment'=>''));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/jquery.dataTables.min.js',         'comment'=>''));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.colVis.min.js',         'comment'=>''));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.tableTools.min.js',     'comment'=>''));
		$this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.bootstrap.min.js',      'comment'=>''));
       

        
        $_table = $this->load->view('index/table', '', TRUE);
        
		$attr['data-widget-icon'] = 'fa fa-cubes';
        $_widget_table = widget('objects'.time(), 'Objects',  $attr, $_table, false, true, true);
        

		$js_in_page = $this->load->view('index/js', '', TRUE);
		$this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => 'INDEX FUNCTIONS'));


        $data['_table']            = $_widget_table;
        $data['_disk_total_space'] = disk_total_space('/');
        $data['_disk_free_space']  = disk_free_space('/');
        $data['_disk_used_space']  = disk_total_space('/') - disk_free_space('/');
        
       
		$this->layout->view('index/index', $data);
	
	}
	
	public function edit($id_object){
	   
       /** LOAD DATABASE */
       $this->load->database();
	   $this->load->model('objects');
       $this->load->model('files');
       
       /** LOAD HELPERS */
       $this->load->helper('smart_admin_helper');
       $this->load->helper('ft_date_helper');
       $this->load->helper('ft_file_helper');
       
       if($this->input->post()){
            $this->objects->update($id_object, $this->input->post());
       }
       
       /** LOAD OBJECT */
       $_object = $this->objects->get_obj_by_id($id_object);
       
       /** LOAD FILES ID */
       $_files_id = $this->objects->get_files($id_object);
       $_files = array();
       
       foreach($_files_id as $id){
        
        $_files[] = $this->files->get_file_by_id($id);
        
       }
       
       $printable_files[] = '.gc';
       $printable_files[] = '.gcode';
       $printable_files[] = '.nc';
       
      
       $_widget_data['_id_object']       = $id_object;
       $_widget_data['_files']           = $_files;
       $_widget_data['_printable_files'] = $printable_files;
       
       
       /** LOAD TABLE CONTENT */
       $_table = $this->load->view('edit/table', $_widget_data, TRUE);
       
       /** CREATE WIDGET */
       $attr['data-widget-icon'] = 'fa fa-files-o';
       $_widget_table = widget('objects'.time(), 'Files',  $attr, $_table, false, true, true);
       
       
       
       /** LAYOUT */
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/jquery.dataTables.min.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.colVis.min.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.tableTools.min.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.bootstrap.min.js', 'comment'=>''));
       
	   
	   $data['_object'] = $_object;
       $data['_widget'] = $_widget_table;
	   
       $js_in_page = $this->load->view('edit/js', $data, TRUE);
       $this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => 'EDIT FUNCTIONS'));
       
       
      
       //$this->layout->set_compress(false);
	   $this->layout->view('edit/index', $data);

	}


	public function convert($object_id = 0, $file_id = 0){
        
        
        $data['_object_id'] = $object_id;
        $data['_file_id']   = $file_id;
        
        /** LAYOUT */
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/jquery.dataTables.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.colVis.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.tableTools.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/dataTables.bootstrap.min.js', 'comment'=>''));
        
 
            
            //ini_set( 'error_reporting', E_ALL );
			//ini_set( 'display_errors', true );
            
            /** LOAD HELPER */
            $this->load->helper('ft_file_helper');
            
            //carico X class database
      		$this->load->database();
        	$this->load->model('files');
            $file = $this->files->get_file_by_id($file_id);

            
            $data['_success'] = false;
      

			$attributes = json_decode($file->attributes, TRUE);
			
			
			
			$data['_file']         = $file;
            $data['is_stl']        = strtolower($file->file_ext) == '.stl' ;
			
			
			/** IF NOT A STL FILE, GET GCODE MODEL INFO */
			if(!$data['is_stl']){
				
				$data['dimesions'] = 'processing';
				$data['filament'] = 'processing';
				$data['number_of_layers'] = 'processing';
				$data['estimated_time'] = 'processing';
				
			
				if(is_array($attributes)){
					$dimensions = $attributes['dimensions'];
					
					$x = number_format($dimensions['x'], 2, '.', '');
					$y = number_format($dimensions['y'], 2, '.', '');
					$z = number_format($dimensions['z'], 2, '.', '');
					
					$data['dimesions'] = $x.' x '.$y.' x '.$z.' mm';
					$data['filament'] = number_format($attributes['filament'], 2, '.', '').' mm';
					$data['number_of_layers'] = $attributes['number_of_layers'];
					$data['estimated_time'] = $attributes['estimated_time'];
					
				}else{
					
					if(strtolower($file->file_ext) == '.gc'  || strtolower($file->file_ext) == '.gcode' && $file->attributes != 'Processing'){	
						gcode_analyzer($file->id);
					}
					
					
				}
			}
			
           	$js_in_page = $this->load->view('convert/js', $data, TRUE);
           	$css_in_page = $this->load->view('convert/css', '', TRUE);
           
           	/** LAYOUT SETUP */
           	$this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => '')); 
           	$this->layout->add_css_in_page(array('data'=> $css_in_page, 'comment' => '')); 
          
           
           	$this->layout->set_compress(false); 
            
      


		
        $this->layout->view('convert/index', $data);
        
    }



}

?>
