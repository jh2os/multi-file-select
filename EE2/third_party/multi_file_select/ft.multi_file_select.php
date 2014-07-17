<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//--------------------------------------------------------------
// Johnathan Waters
// Papercut Interactive
// Last updated 25 April 2014 [updated version number]
//--------------------------------------------------------------

// Initially we need to load this class in order to upload our room scene image in our field type
ee()->load->library('file_field');
class Multi_file_select_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> 'Multi-file Select',
		'version'	=> '0.001'
	);
	
	var $has_array_data = TRUE;
	/**
	 *  Constructor
	 */
	function __construct() {
		// Standard EE stuff that defines libraries and functions for us (see EE documentation)
		$this->EE =& get_instance();

		// Doing it a second time for good luck.
		$this->EE->load->library('file_field');	
	}
	
	/**
	 * Display Field in matrix cell
	 *
	 * @access	public
	 * @param	existing data
	 * @return	matrix field html
	 *
	 */
	function display_cell( $data ) {
		$this->EE->load->library('file_field');
		$this->_include_theme_css('css/multifileselect.css');	
		$this->_include_theme_js('js/multi-file-select.js');
		$this->_include_theme_js('js/jquery.tablednd.js');
		$this->EE->cp->add_to_foot("
		<script>
			$(document).ready(function() {
				Matrix.bind('multi_file_select', 'display', function(cell){
					addrow();
					removerow();
					bindinput();
					updateinput($('.multifileselectform .removerow:last'));
				});
			});
		</script>");
		
		$thesettings = $this->settings;
				
		$filedir = (int)$thesettings['multifiledir'];
		$filetype = (isset($thesettings['multifiletype'])) ? $thesettings['multifiletype'] : 'img';

		
		$thedata ='';
		$dataA = explode( '|' , $data );

		for($i = 0 ; $i < count($dataA) - 1; $i++) {
			
			$iData = explode( '~' , $dataA[$i] );
			$originalFile = ($iData[1] != '') ? '{filedir_'.$iData[0].'}'.$iData[1] : '';
			$originalTitle = (isset($iData[2])) ? $iData[2] : '';
			$originalAlt = ($iData[0] != '') ? $iData[3]: '';	

			$query = ee()->db->select('id, url')
							->from('upload_prefs')
							->where(array('id' => $iData[0]))
							->get();

			$row = $query->result_array();
			
			$Image = ($filetype == 'all') ? $this->EE->config->slash_item('theme_folder_url').'cp_global_images/default.png' : $row[0]['url'].'/_thumbs/'.$iData[1];
			
			$thedata .= '<tr><td style="width:30%;text-align:center">'.'<img src="'.$Image.'"><br><p>'.$iData[1] .'</p>
			<input type="hidden" name="filename" value="'.$iData[1] .'">
			<input type="hidden" name="filedir" value="'.$iData[0] .'">' . '</td>
								<td style="width:30%"><fieldset class="holder"><input name="filetitle" style="width:98%" value="'.$originalTitle.'"></fieldset></td>
								<td style="width:30%"><fieldset class="holder"><input name="filealt" style="width:98%" value="'.$originalAlt.'"></fieldset></td>
								<td class="removerow" style="width:10%;text-align:center"><button>x</button></td></tr>';
		}
	
		// Now we finish our output
		return '
			<fieldset class="multitest"><table id="thistable" class="multifileselectform" data-directory="'.$filedir.'" style="width:100%" >
				<tr>
					<td style="width:30%;text-align:center"><b>File</b></td>
					<td style="width:30%;text-align:center"><b>File Display Title</b></td>
					<td style="width:30%;text-align:center"><b>File Alt-Text</b></td>
					<td style="width:10%;text-align:center"><b>Remove</b></td>
				</tr>'.$thedata.'</table>'
					.form_input($this->cell_name, $data, 'id="'.$this->field_name
				.'" style="display:none"').'<p class="addrowmulti"> + add row</p><br></fieldset>';
		
	}
	
	
	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data)
	{
		$this->EE->load->library('file_field');	
		$this->_include_theme_css('css/multifileselect.css');	
		$this->_include_theme_js('js/multi-file-select.js');
		$this->_include_theme_js('js/jquery.tablednd.js');

		$thesettings = $this->settings;
				
		$filedir = (int)$thesettings['multifiledir'];
		$filetype = (isset($thesettings['multifiletype'])) ? $thesettings['multifiletype'] : 'img';
		$query = ee()->db->select('id, url')
						->from('upload_prefs')
						->where(array('id' => $filedir))
						->get();

		$row = $query->result_array();
		
		
		$thedata ='';
		$dataA = explode( '|' , $data );

		for($i = 0 ; $i < count($dataA) - 1; $i++) {
			
			$iData = explode( '~' , $dataA[$i] );
			$originalFile = ($iData[0] != '') ? '{filedir_'.$iData[0].'}'.$iData[1] : '';
			$originalTitle = (isset($iData[2])) ? $iData[2] : '';
			$originalAlt = ($iData[0] != '') ? $iData[3]: '';
			
			$Image = ($filetype == 'all') ? $this->EE->config->slash_item('theme_folder_url').'cp_global_images/default.png' : $row[0]['url'].'/_thumbs/'.$iData[1];
			
			
			$thedata .= '<tr><td class="multifile'.$i.'" style="width:30%;text-align:center">'. 
								//ee()->file_field->field('multifile'.$i, $originalFile, (int)$filedir, $filetype) 
								'<img src="'.$Image .'"><br><p>'.$iData[1] .'</p>
								<input type="hidden" name="filename" value="'.$iData[1] .'">
								<input type="hidden" name="filedir" value="'.$iData[0] .'"></td>
								<td style="width:30%"><fieldset class="holder"><input name="filetitle" style="width:98%" value="'.$originalTitle.'"></fieldset></td>
								<td style="width:30%"><fieldset class="holder"><input name="filealt" style="width:98%" value="'.$originalAlt.'"></fieldset></td>
								<td class="removerow" style="width:10%;text-align:center"><button>x</button></td></tr>';
		}
	
		// Now we finish our output
		return '
		<form class="multitest"><table id="thistable" class="multifileselectform" data-directory="'.$filedir.'" style="width:100%">
			<tr>
				<td style="width:30%;text-align:center"><b>File</b></td>
				<td style="width:30%;text-align:center"><b>File Display Title</b></td>
				<td style="width:30%;text-align:center"><b>File Alt-Text</b></td>
				<td style="width:10%;text-align:center"><b>Remove</b></td>
			</tr>'.$thedata.'</table></form>'
				.form_input($this->field_name, $data, 'id="'.$this->field_name
			.'" style="display:none;"').'<p class="addrowmulti"> + add row</p><br>';
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	field contents
	 * @return	replacement text
	 *
	 */
	
	// This is what we see on our displayed page
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		$returnString = '';
		$loopcount = 0;
		if ($tagdata != FALSE ) {
			
			$dataRows = explode("|", $data);
			
			array_pop($dataRows);
		
			foreach($dataRows as $row) {
				$loopcount++;
					
				$output = $tagdata;
				$dataColumn = explode("~",$row);
				
				
				$query = ee()->db->select('id, url')
								->from('upload_prefs')
								->where(array('id' => $dataColumn[0]))
								->get();

				
				$row = $query->result_array();
												
				$output = str_replace("{multi_file_src}", $row[0]['url'].$dataColumn[1], $output);
				$output = str_replace("{multi_file_title}", $dataColumn[2], $output);
				$output = str_replace("{multi_file_alt}", $dataColumn[3], $output);
				$output = str_replace("{multi_count}", $loopcount, $output);
				$returnString .= $output;
			}
		}
		return $returnString;
	}
	// --------------------------------------------------------------------
	
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Settings Screen
	 *
	 * @access	public
	 * @return	local settings
	 *
	 */
	function display_settings($data)
	{

		$settingsFileDir = (isset($data['multifiledir'])) ? $data['multifiledir'] : '';
		$settingsFileType =  (isset($data['multifiletype'])) ? $data['multifiletype'] : '';
		
		$options = array(
			'multifiledir'	=> $settingsFileDir,
			'multifiletype'	=> $settingsFileType
		);

		$query = ee()->db->select('id, name')
						->from('upload_prefs')
						->get();
		if ($query->num_rows() > 0) {
			
			$form = '<table class="mainTable padTable" cellspacing="0" cellpadding="0" border="0" >
			<thead><tr><th>Multi-File Select Options</th><th></th></tr></thead><tr><td><h4>Select a file upload directory</h4></td>
					<td><select name="multifiledir"><option value="0">All</option>';
			
			foreach($query->result_array() as $row) {
				$selected = ($row['id'] == $options['multifiledir']) ? 'selected' : '';
				$form .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';
			}
			
			if($options['multifiletype'] == 'all') {
				$checked = array('checked','');
			} else {
				$checked = array('','checked');
			}
			
			$form .= '</select></td></tr>';
			
			$form .= '<tr><td><h4>Please select which file type</h4><td>
						<input type="radio" name="multifiletype" id="pdf" value="all" '.$checked[0].'>&nbsp;PDF/Other&nbsp;
						<input type="radio" name="multifiletype" id="img" value="img" '.$checked[1].'>&nbsp;Images</td></table>';
		
		} else {
			
			$form = "<p>No upload preferences detected</p><p>Please add a file upload directory and try again</p>";
			
		}

		return $form;
	}
	// --------------------------------------------------------------------
	
	function display_cell_settings( $data ) {
		$settingsFileDir = (isset($data['multifiledir'])) ? $data['multifiledir'] : '';
		$settingsFileType =  (isset($data['multifiletype'])) ? $data['multifiletype'] : '';
	
		$options = array(
			'multifiledir'	=> $settingsFileDir,
			'multifiletype'	=> $settingsFileType
			);

		$query = ee()->db->select('id, name')
					->from('upload_prefs')
					->get();
		if ($query->num_rows() > 0) {
		
			$form = '<table class="mainTable padTable" cellspacing="0" cellpadding="0" border="0" >
				<thead><tr><th>Multi-File Select Options</th><th></th></tr></thead><tr><td><h4>Select a file upload directory</h4></td>
				<td><select name="multifiledir"><option value="0">All</option>';
		
			foreach($query->result_array() as $row) {
				$selected = ($row['id'] == $options['multifiledir']) ? 'selected' : '';
				$form .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';
			}
		
			if($options['multifiletype'] == 'all') {
				$checked = array('checked','');
			} else {
				$checked = array('','checked');
			}
		
			$form .= '</select></td></tr>';
		
			$form .= '<tr><td><h4>Please select which file type</h4><td>
						<input type="radio" name="multifiletype" id="pdf" value="all" '.$checked[0].'>&nbsp;PDF/Other&nbsp;
						<input type="radio" name="multifiletype" id="img" value="img" '.$checked[1].'>&nbsp;Images</td></table>';
	
		} else {
		
			$form = "<p>No upload preferences detected</p><p>Please add a file upload directory and try again</p>";
		
		}

		return $form;
	}


	/**
	 * Save Settings
	 *
	 * @access	public
	 * @return	field settings
	 *
	 */
	function save_settings($data)
	{
		return array(
			'multifiledir' 	=> $this->EE->input->post('multifiledir'),
			'multifiletype'	=> $this->EE->input->post('multifiletype')
		);
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Install Fieldtype
	 *
	 * @access	public
	 * @return	default global settings
	 *
	 */
	function install()
	{
		return array(
			'multifiledir' => ''
		);
		return array(
			'multifiletype'=> ''
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Control Panel Javascript
	 *
	 * @access	public
	 * @return	void
	 *
	 */
	function _cp_js()
	{
		// This js is used on the global and regular settings
		// pages, but on the global screen the map takes up almost
		// the entire screen. So scroll wheel zooming becomes a hindrance.
		
		//$this->EE->cp->add_to_head('');
		$this->EE->cp->load_package_js('cp');
	}

	/**
	 * Theme URL
	 */
	private function _theme_url()
	{
		if (! isset($this->cache['theme_url']))
		{
			$theme_folder_url = defined('URL_THIRD_THEMES') ? URL_THIRD_THEMES : $this->EE->config->slash_item('theme_folder_url').'third_party/';
			$this->cache['theme_url'] = $theme_folder_url.'multi_file_select/';
		}

		return $this->cache['theme_url'];
	}

	/**
	 * Include Theme CSS
	 */
	private function _include_theme_css($file)
	{
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'"/>');
	}

	/**
	 * Include Theme JS
	 */
	private function _include_theme_js($file)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().$file.'"></script>');
	}
}

/* End of file ft.multi_file_select.php */
/* Location: ./system/expressionengine/third_party/multi_file_select/ft.multi_file_select.php */
