<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller

{
	public function __construct()
	{

		parent::__construct();
		$this->load->model('M_index');
	}
	public function index()
	{
		$data['data_kamar'] = $this->M_index->get_data()->result();
		$json = json_encode($data);
		print_r($json);
	}

	public function template()
	{
		$this->load->view('template');
	}
}
