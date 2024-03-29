<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * mithra62 - Export It
 *
 * @package		mithra62:Export_it
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2011, mithra62, Eric Lamb.
 * @link		http://mithra62.com/projects/view/export-it/
 * @version		1.0
 * @filesource 	./system/expressionengine/third_party/export_it/
 */
 
 /**
 * Export It - Export Library
 *
 * Contains all the Export methods. 
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/libraries/Export_data/Export_data.php
 */
class Export_data
{
	public $disable_download = FALSE;
	
	public function __construct()
	{
		$this->EE =& get_instance();
		//$this->disable_download = TRUE;
		$this->EE->load->library('Export_data/export_disqus');
		$this->EE->load->library('Export_data/export_json');
		$this->EE->load->library('Export_data/export_ee_xml');
		$this->EE->load->library('Export_data/export_xls');
		$this->EE->load->helper('utilities');
	}
	
	public function export_channel_entries($data, $export_format, $filename = 'channel_entry_export')
	{
		switch($export_format)
		{	
			case 'xml':
			default:
				$this->download_xml($data, $filename.'.xml', 'channel_entries', 'entry');
			break;
			
			case 'json':
				$this->download_json($data, $filename.'.json');
			break;
			
			case 'xls':
				$this->download_array($data, TRUE, $filename.'.xls');
			break;			
		}		
	}
	
	public function export_channel_entry($data, $export_format)
	{
		switch($export_format)
		{	
			case 'xml':
			default:
				$this->download_xml($data, 'channel_entry_export.xml', 'channel_entries', 'entry');
			break;
			
			case 'json':
				$this->download_json($data, 'channel_entry_export.json');
			break;
			
			case 'xls':
				$this->download_array($data, TRUE, 'channel_entry_export.xls');
			break;
		}		
	}	
	
	public function export_comments($data, $format)
	{
		switch($format)
		{			
			case 'disqus':
			default:
				$this->download_disqus($data, TRUE, 'comment_export.rss');
			break;
			
			case 'xml':
				$this->download_xml($data, 'comment_export.xml', 'Comments', 'comment');
			break;
			
			case 'json':
				$this->download_json($data, 'comment_export.json');
			break;
				
			case 'xls':
				$this->download_array($data, TRUE, 'comment_export.xls');
			break;					
		}
	}
	
	public function export_comment($export_format = 'json', $comment_id = '')
	{
		$where = array();
		if($comment_id != '')
		{
			$where['comment_id'] = $comment_id;
		}
		
		$data = $this->EE->channel_data->get_comments($where);
		switch($export_format)
		{
			case 'disqus':
			default:
				$this->download_disqus($data, TRUE, 'comment_export.rss');
			break;
			
			case 'xml':
				$this->download_xml($data, 'comment_export.xml', 'Comments', 'comment');
			break;
			
			case 'json':
				$this->download_json($data, 'comment_export.json');
			break;			
		}
	}	
	
	public function export_members($data, $format = 'xls')
	{
		if($this->disable_download)
		{
			$data = $this->sanitize_member($data);
		}
		
		foreach($data AS $key => $value)
		{
			if(isset($value['join_date']))
			{
				$data[$key]['join_date'] = ($value['join_date'] != '0' ? m62_convert_timestamp($value['join_date']) : '0');
			}
		
			if(isset($value['last_entry_date']))
			{
				$data[$key]['last_entry_date'] = ($value['last_entry_date'] != '0' ? m62_convert_timestamp($value['last_entry_date']) : '0');
			}
				
			if(isset($value['last_comment_date']))
			{
				$data[$key]['last_comment_date'] = ($value['last_comment_date'] != '0' ? m62_convert_timestamp($value['last_comment_date']) : '0');
			}
				
			if(isset($value['last_activity']))
			{
				$data[$key]['last_activity'] = ($value['last_activity'] != '0' ? m62_convert_timestamp($value['last_activity']) : '0');
			}
		
			if(isset($value['last_visit']))
			{
				$data[$key]['last_visit'] = ($value['last_visit'] != '0' ? m62_convert_timestamp($value['last_visit']) : '0');
			}
			
			if(isset($value['last_forum_post_date']))
			{
				$data[$key]['last_forum_post_date'] = ($value['last_forum_post_date'] != '0' ? m62_convert_timestamp($value['last_forum_post_date']) : '0');
			}
			
			if(isset($value['last_email_date']))
			{
				$data[$key]['last_email_date'] = ($value['last_email_date'] != '0' ? m62_convert_timestamp($value['last_email_date']) : '0');
			}			
		}		
				
		switch($format)
		{
			case 'xls':
			default:
				$this->download_array($data, TRUE, 'member_export.xls');
			break;
			
			case 'ee_xml':
				$this->download_ee_xml($data, 'ee_member_export.xml');
			break;
			
			case 'xml':
				$this->download_xml($data, 'member_export.xml', 'members', 'member');
			break;
			
			case 'json':
				$this->download_json($data, 'member_export.json');
			break;			
		}		
	}	
	
	public function export_mailing_list($data, $format = 'xls')
	{	
		switch($format)
		{
			case 'xls':
			default:
				$this->download_array($data, TRUE, 'mailing_list_export.xls');
			break;
			
			case 'xml':
				$this->download_xml($data, 'mailing_list_export.xml', 'mailing_list', 'subscriber');
			break;
			
			case 'json':
				$this->download_json($data, 'mailing_list_export.json');
			break;			
		}
	}
	
	public function export_category($export_format = 'json', $cat_id = '')
	{
		$where = array();
		if($cat_id != '')
		{
			$where['cat_id'] = $cat_id;
		}
		
		$data = $this->EE->channel_data->get_category($where);
		switch($export_format)
		{	
			case 'xml':
				$this->download_xml($data, 'category_export.xml', 'categories', 'category');
			break;
			
			case 'json':
				$this->download_json($data, 'category_export.json');
			break;			
		}
	}

	public function export_category_posts($export_format = 'json', $cat_id = '')
	{
		$where = array();
		if($cat_id != '')
		{
			$where['cat_id'] = $cat_id;
		}
		
		$data = $this->EE->channel_data->get_category_posts($where);
		switch($export_format)
		{	
			case 'xml':
				$this->download_xml($data, 'category_posts_export.xml', 'entries', 'entry');
			break;
			
			case 'json':
				$this->download_json($data, 'category_posts_export.json');
			break;			
		}
	}

	public function export_categories($export_format = 'json', $entry_id = '')
	{
		$where = array();
		$where['entry_id'] = $entry_id;
		
		$data = $this->EE->channel_data->get_categories($where);
		switch($export_format)
		{	
			case 'xml':
				$this->download_xml($data, 'categories_export.xml', 'categories', 'category');
			break;
			
			case 'json':
				$this->download_json($data, 'categories_export.json');
			break;			
		}
	}	
	
	public function download_json(array $data, $file_name = '')
	{
		if(!$this->disable_download)
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$file_name\"");
		}
		echo $this->EE->export_json->generate($data);
	}
	
	public function download_xml($data, $file_name, $root_name, $branch_name = 'item', $subbranch_name = 'sub')
	{
		if(!$this->disable_download)
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$file_name\"");
		}

		$this->EE->load->library('xml_writer');
	    $this->EE->xml_writer->setRootName($root_name);
	    $this->EE->xml_writer->initiate();

	    foreach($data AS $i => $item)
	    {
	    	$this->EE->xml_writer->startBranch($branch_name);
	    	foreach($item AS $key => $value)
	    	{
	    		$this->_add_xml_nodes($key, $value);
	    	}
	    	
	    	$this->EE->xml_writer->endBranch();
	    }

	    $this->EE->xml_writer->getXml(true);		
	}
	
	private function _add_xml_nodes($key, $value)
	{
		if(!is_array($value) && !is_numeric($key))
		{
			$wrap = TRUE;
			if(is_numeric($value))
			{
				$wrap = FALSE;
			}
			$this->EE->xml_writer->addNode($key, $value, array(), $wrap);
			return;			
			
		}

		if(is_array($value) && !is_numeric($key))
		{
			$this->EE->xml_writer->startBranch($key);
		}
		foreach($value AS $_key => $sub)
		{
			if(!is_array($sub))
			{
				$wrap = TRUE;
				if(is_numeric($value))
				{
					$wrap = FALSE;
				}				
				$this->EE->xml_writer->addNode($_key, $sub, array(), $wrap);
			}
			else 
			{					
				$this->_add_xml_nodes($_key, $sub);
				
			}				
		}
		
		if(is_array($value) && !is_numeric($key))
    	{
    		$this->EE->xml_writer->endBranch();
		}
	    			
	}
	
	public function download_ee_xml($data, $file_name = FALSE)
	{
		if(!$this->disable_download)
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$file_name\"");
		}
		
		echo $this->EE->export_ee_xml->generate($data);
	}
	
	/**
	 * Forces an array to download as a csv file
	 * @param array $arr
	 * @param bool $keys_as_headers
	 * @param bool $file_name
	 */
	public function download_array(array $arr, $keys_as_headers = TRUE, $file_name = 'download.txt')
	{
		if(!$this->disable_download)
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$file_name\"");
		}
		
		echo $this->EE->export_xls->create($arr, TRUE);
	}	
	
	public function download_disqus(array $arr)
	{
		$file_name = 'disqus.rss';
		if(!$this->disable_download)
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$file_name\"");
		}
				
		$return = $this->EE->export_disqus->generate($arr);
		
		echo $return;
	}
	
	private function sanitize_member(array $users)
	{
		$count = count($users);
		for($i=0; $i<$count;$i++)
		{
			if(isset($users[$i]['password']))
			{
				unset($users[$i]['password']);
			}
		}
		return $users;
	}
}