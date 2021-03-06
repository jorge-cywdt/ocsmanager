<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('COrg_model');
		$this->load->helper('functions');
	}

	public function combustibles()
	{	
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Ventas > Combustibles';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 0;
			$this->load->view('ventas/combustibles',$data);
		}
	}

	public function market()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Ventas > Market Tienda';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('M');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 1;
			$this->load->view('ventas/market',$data);
		}
	}

	public function market_playa()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Ventas > Market Playa';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 2;
			$this->load->view('ventas/market_playa',$data);
		}
	}

	public function resumen()
	{	
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Ventas > Resumen';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 3;
			$this->load->view('ventas/resumen',$data);
		}
	}

	public function estadistica()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Ventas > Estadística';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');
			$data['previous_start_date'] = getDatePrevious('d/m/Y',2);

			$data['typeStation'] = 4;
			$this->load->view('ventas/estadistica',$data);
		}
	}

	public function market_productos_linea()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Ventas > Productos por Línea (MT)';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('M');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 5;
			$this->load->view('ventas/market_productos_linea',$data);
		}
	}

	/**
	 * Mercaderia representado en moneda
	 * Add 2017-12-27
	 */
	public function mercaderias()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Ventas > Mercaderías';
			$data['name'] = 'Mercaderías';
			$data['actions'] = array(
				'submit' => 'btn-search-merchandise-sale'
			);
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('M');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 1;
			$this->load->view('ventas/mercaderias',$data);
		}
	}
}