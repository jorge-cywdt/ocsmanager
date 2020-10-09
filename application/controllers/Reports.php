<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	public $tmp;
	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('functions');
	}

	public function resumeSales()
	{
		$msg = getMemory(array(''));
		if(checkSession()) {
			$return = array();
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null && $this->uri->segment(6) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(6);

				$typeStationDesc = getDescriptionTypeStation($typeStation);

				$mod = '';
				$typeEst = '';
				if($typeStation == 0) {
					$mod = 'DETAIL_SALE_COMB';
					$typeEst = 'comb';
					$titleDocument = 'Venta de Combustibles';
				} else {
					$mod = 'DETAIL_SALE_MARKET';
					$typeEst = 'market';
					$titleDocument = 'Venta en Market';
				}

				$dateBegin = $this->uri->segment(4);
				$dateEnd = $this->uri->segment(5);

				$formatDateBegin = formatDateCentralizer($dateBegin,2);
				$formatDateEnd = formatDateCentralizer($dateEnd,2);

				$qty_sale = 0;
				$type_cost = 0;


				$totalQty = 0;
				$totalSale = 0;

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStationDesc == 'MP' ? 'C' : $typeStationDesc,$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStationDesc == 'MP' ? 'C' : $typeStationDesc);
					}
				}

				//load our new PHPExcel library
				$this->load->library('calc');

				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:F1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fechas');
				$this->calc->getActiveSheet()->setCellValue('B3', $dateBegin);
				$this->calc->getActiveSheet()->setCellValue('C3', $dateEnd);

				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');

				//$this->calc->getActiveSheet()->setCellValue('B4', $msg['memory']);

				//Inicio de cabecera (tabla)
				if($typeStation == 0) {
					$this->calc->getActiveSheet()->setCellValue('A7', 'Combustible');
				} else {
					$this->calc->getActiveSheet()->setCellValue('A7', 'Línea');
				}
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				if($typeStation == 0) {
					$this->calc->getActiveSheet()->setCellValue('B7', 'Cantidad');
				}
				$this->calc->getActiveSheet()->setCellValue('C7', 'Venta');
				$this->calc->getActiveSheet()->setCellValue('D7', 'Costo');
				$this->calc->getActiveSheet()->setCellValue('E7', 'Margen');
				$this->calc->getActiveSheet()->setCellValue('F7', '%');
				$this->calc->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A7:F7')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera (tabla)

				$total_precio_ = 0.0;
				$total_costo_ = 0.0;
				$total_cantidad_ = 0.0;
				$total_utilidad_ = 0.0;
				$total_por_utilidad_ = 0.0;
				$row = 8;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('B7:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('C7:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('D7:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('E7:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('F7:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&qty_sale='.$qty_sale.'&type_cost='.$type_cost;
						$dataRemoteStations = getUncompressData($curl);
						//codigo  | descripcion | total_cantidad | total_venta | af_cantidad |       af_total        |    costo     |        venta_sin_igv        | descuentos | neto_cantidad | neto_soles
					}
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->client_name);
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $dataStation->name);
					$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('B'.$row.':E'.$row);
					$row++;

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;
					$total_costo = 0.0;
					$total_margen = 0.0;
					$total_por_margen = 0.0;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						if($typeStation == 0) {
							//array para comb
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);

									if($d[0] == '11620308') {
										//$gal = $d[9];///3.7853;
										$gal = converterUM(array('type' => 1, 'co' => $d[9]));//gal
										$venta = $d[10];//venta
										$costo = $d[6];//costo
										$venta_sin_igv = $d[7];//venta sin igv
										$utilidad = $venta_sin_igv - $costo;//utilidad
										$por_utilidad = (($utilidad/$costo)*1)*100;
									} else if($d[0] != '11620307') {
										$gal = $d[9];
										$venta = $d[10];
										$costo = $d[6];
										$utilidad = $d[7] - $d[6];
										$por_utilidad = (($utilidad/$costo)*1)*100;
									} else {
										$gal = converterUM(array('type' => 0, 'co' => $d[9]));//gal
										$venta = $d[10];//venta
										$costo = $d[6];//costo
										$venta_sin_igv = $d[7];//venta sin igv
										$utilidad = $venta_sin_igv - $costo;//utilidad
										$por_utilidad = (($utilidad/$costo)*1)*100;
									}

									//$sheet->getStyle("A1")->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('A'.$row, $d[1]);
									$this->calc->getActiveSheet()->setCellValue('B'.$row, $gal)->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$venta, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$costo, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$utilidad, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$por_utilidad, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');

									$total_cantidad += $gal;
									$total_precio += $venta;
									$total_costo += $costo;
									$total_margen += $utilidad;
									$total_por_margen += (($utilidad/$costo)*1)*100;
								}
								$row++;
							}
						} else {
							//market
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);

									$this->calc->getActiveSheet()->setCellValue('A'.$row, $d[1]);
									//$this->calc->getActiveSheet()->setCellValue('B'.$row, $d[2]);
									$imp = $d[5] == '' ? 0.00 : $d[5];//round
									$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$imp, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');

									$cost = $d[4] == '' ? 0.00 : $d[4];
									$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$cost, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');

									$mar = $d[6] == '' ? 0.00 : $d[6];//round
									$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$mar, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');

									$_por_margen = (($mar/$cost)*1)*100;//round
									$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$_por_margen, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');

									//$total_cantidad += $d[2];
									$total_precio += $d[5];
									$total_costo += $d[4];
									$total_margen += $d[6];
									$total_por_margen += $_por_margen;
								}
								$row++;
							}
						}

					} else {
						$return['status'] = 4;
					}

					$this->calc->getActiveSheet()->setCellValue('A'.$row, 'Total Estación');
					if($typeStation == 0) {
						$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$total_cantidad, 2, '.', ','));
					}

					$total_por_margen = (($total_margen/$total_costo)*1)*100;

					$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$total_precio, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
					$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$total_costo, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
					$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$total_margen, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
					$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$total_por_margen, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
					$row += 2;

					$total_cantidad_ += $total_cantidad;
					$total_precio_ += $total_precio;
					$total_costo_ += $total_costo;
					$total_utilidad_ += $total_margen;
					$total_por_utilidad_ += $total_por_margen;
				}
				$total_por_utilidad_ = (($total_utilidad_/$total_costo_)*1)*100;

				$row += 2;
				$this->calc->getActiveSheet()->setCellValue('A'.$row, 'Total Estaciones');
				if($typeStation == 0) {
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$total_cantidad_, 2, '.', ','));
				}
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$total_precio_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');

				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$total_costo_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');

				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$total_utilidad_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');

				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$total_por_utilidad_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');

				unset($dataRemoteStations);
				$msg2 = getMemory(array(''));
				//$this->calc->getActiveSheet()->setCellValue('C4', $msg2['memory']);

				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_sale_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache

				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');

			} else {
				echo 'No';
				//parametros vacios
				//error 404
			}
		} else {
			//no session
			//pagina de error 404
		}
	}

	public function resumeStock()
	{
		if(checkSession()) {
			$return = array();
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(5);

				$mod = '';
				$typeEst = '';
				if($typeStation == 0) {
					$mod = 'STOCK_COMB_R';//termpral para este reporte con serialize
					$typeEst = 'comb';
					$titleDocument = 'Stock de Combustibles';
				} else {
					$mod = 'STOCk_MARKET';//falta
					$typeEst = 'market';
					$titleDocument = 'Stock de Market';
				}

				$return['dateEnd'] = date('d/m/Y', strtotime(formatDateCentralizer($this->uri->segment(4),3). ' - 7 days'));

				$formatDateBegin = formatDateCentralizer($this->uri->segment(4),2);
				$formatDateEnd = formatDateCentralizer($return['dateEnd'],1);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 0 ? 'C' : 'M',$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 0 ? 'C' : 'M');
					}
				}

				//load our new PHPExcel library
				$this->load->library('calc');

				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:G1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fecha');
				$this->calc->getActiveSheet()->setCellValue('B3', $this->uri->segment(4));

				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');

				//Inicio de cabecera	(tabla)
				$this->calc->getActiveSheet()->setCellValue('A7', 'Producto');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				$this->calc->getActiveSheet()->setCellValue('B7', 'Capacidad');
				$this->calc->getActiveSheet()->setCellValue('C7', 'Inventario');
				$this->calc->getActiveSheet()->setCellValue('D7', 'Promedio Venta día');
				$this->calc->getActiveSheet()->setCellValue('E7', 'Tiempo Vaciar');
				$this->calc->getActiveSheet()->setCellValue('F7', 'Cant. ult. Compra');
				$this->calc->getActiveSheet()->setCellValue('G7', 'Fecha ult. Compra');
				$this->calc->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A7:G7')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera		(tabla)

				$row = 8;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('B7:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('14');
				$this->calc->getActiveSheet()->getStyle('C7:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('D7:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('E7:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('22');
				$this->calc->getActiveSheet()->getStyle('F7:F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('20');
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateEnd.'&to='.$formatDateBegin.'&warehouse_id='.$dataStation->almacen_id.'&days=7&isvaliddiffmonths=si';
						$dataRemoteStations = getUncompressData($curl);
					}
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->client_name);
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $dataStation->name);
					$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('B'.$row.':G'.$row);
					$row++;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$dataRemoteStations = unserialize($dataRemoteStations[0]);
						if($typeStation == 0) {

							foreach($dataRemoteStations as $key => $data) {
								$this->calc->getActiveSheet()->setCellValue('A'.$row, $data['desc_comb']);
								$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$data['nu_capacidad'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$data['nu_medicion'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$data['nu_venta'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('E'.$row, round((float)$data['tiempo']));
								$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$data['cantidad_ultima_compra'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('G'.$row, date("d/m/Y", strtotime($data['fecha_ultima_compra'])));
								$row++;
							}

						} else {
							//market
						}
					} else {
						$return['status'] = 4;
					}
					$row++;
				}


				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_stock_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
					            
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
			} else {
				echo 'No';
				//parametros vacios
				//error 404
			}
		} else {
			//no session
			//pagina de error 404
		}
	}

	public function generateCaclSumary() {
		if(checkSession()) {
			$return = array();
			//echo '3: '.$this->uri->segment(3).', 4: '.$this->uri->segment(4).', 5: '.$this->uri->segment(5);
			//exit;
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(6);
				$include = $this->uri->segment(9);
				$mod = '';
				$typeEst = '';
				if($typeStation == 3) {
					$mod = 'TOTALS_SUMARY_SALE';//termpral para este reporte con serialize
					$typeEst = 'comb';
					$titleDocument = 'Resumen de Combustibles';
				} else {
					$mod = 'ERR';//falta
					$typeEst = 'market';
					$titleDocument = 'Resumen de Market';
				}
				//$return['dateEnd'] = date('d/m/Y', strtotime(formatDateCentralizer($this->uri->segment(4),3). ' - 7 days'));

				$formatDateBegin = formatDateCentralizer($this->uri->segment(4),2);
				$formatDateEnd = formatDateCentralizer($this->uri->segment(5),2);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 3 ? 'C' : 'M',$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 3 ? 'C' : 'M');
					}
				}

				//echo 'formatDateBegin: '.$formatDateBegin.', formatDateEnd: '.$formatDateEnd.', typeStation: '.$typeStation;

				//load our new PHPExcel library
				$this->load->library('calc');
				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:G1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fecha');
				$this->calc->getActiveSheet()->setCellValue('B3', $this->uri->segment(4));

				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');

				$tipo = '';
				$textInclude = '';
				if($include == 0) {
					$tipo = 'Galones';
					$textInclude = 'No';
				} else if($include == 1) {
					$tipo = 'Galones';
					$textInclude = 'Si';
				} else if($include == 2) {
					$tipo = 'Soles';
					$textInclude = 'No';
				} else if($include == 3) {
					$tipo = 'Soles';
					$textInclude = 'Si';
				}

				$this->calc->getActiveSheet()->setCellValue('A6', 'Tipo');
				$this->calc->getActiveSheet()->setCellValue('B6', $tipo);
				$this->calc->getActiveSheet()->setCellValue('A7', 'Excluir consumo interno');
				$this->calc->getActiveSheet()->setCellValue('B7', $textInclude);

				//Inicio de cabecera	(tabla)
				$this->calc->getActiveSheet()->setCellValue('A9', 'Estación');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				$this->calc->getActiveSheet()->setCellValue('B9', '84');
				$this->calc->getActiveSheet()->setCellValue('C9', '90');
				$this->calc->getActiveSheet()->setCellValue('D9', '95');
				$this->calc->getActiveSheet()->setCellValue('E9', '97');
				$this->calc->getActiveSheet()->setCellValue('F9', 'D2');
				$this->calc->getActiveSheet()->setCellValue('G9', 'GLP');
				$this->calc->getActiveSheet()->setCellValue('H9', 'GNV');
				$this->calc->getActiveSheet()->setCellValue('I9', 'Total');
				$this->calc->getActiveSheet()->setCellValue('J9', 'Tienda');
				$this->calc->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A9:J9')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera		(tabla)

				$row = 10;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('B9:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('14');
				$this->calc->getActiveSheet()->getStyle('C9:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('D9:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('E9:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('22');
				$this->calc->getActiveSheet()->getStyle('F9:F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('G9:G256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('H')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('H9:H256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('I')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('I9:I256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('J')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('J9:J256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateEnd.'&to='.$formatDateBegin.'&warehouse_id='.$dataStation->almacen_id.'&days=7&isvaliddiffmonths=si&unserialize=1';
						$dataRemoteStations = getUncompressData($curl);
					}
					//$row++;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$dataRemoteStations = unserialize($dataRemoteStations[0]);

						if($typeStation == 3) {

							$_data = array();
							$value = array(0,0,0,0,0,0,0,0,0,0,0,0);
							foreach($dataRemoteStations as $key => $data) {
								$_data[] = $data;
								
								if ($include == 0) {
									$value[8] = $dataStation->name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = converterUM(array('type' => 0, 'co' => $data['neto_cantidad']));//gal

										//$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_cantidad'] != '') {
											$value[6] = converterUM(array('type' => 1, 'co' => $data['neto_cantidad']));//gal

											//$value[7] += $value[6];
										}
									}
									if ($data['codigo'] == '11620307') {
										$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										$value[7] += $value[6];
									} else if ($data['codigo'] != '11620307' || $data['codigo'] != '11620308') {
										$value[7] += $data['neto_cantidad'] != '' ? $data['neto_cantidad'] : 0;
									}
								} else if ($include == 1) {
									$value[8] = $dataStation->name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = $data['neto_cantidad'] - $data['cantidad_ci'];
										$value[5] = converterUM(array('type' => 0, 'co' => $value[5]));//gal

										//$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_cantidad'] != '') {
											$value[6] = $data['neto_cantidad'] - $data['cantidad_ci'];
											$value[6] = converterUM(array('type' => 1, 'co' => $value[6]));//gal

											//$value[7] += $value[6];
										}
									}
									if ($data['codigo'] == '11620307') {
										$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										$value[7] += $value[6];
									} else if($data['codigo'] != '11620307' || $data['codigo'] != '11620308') {
										$value[7] += ($data['neto_cantidad'] != '' ? $data['neto_cantidad'] : 0) - $data['cantidad_ci'];
									}
								} else if ($include == 2) {
									$value[8] = $dataStation->name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_soles'] != '') {
											$value[6] = $data['neto_soles'];
										}
									}
									$value[7] += $data['neto_soles'] != '' ? $data['neto_soles'] : 0;
								} else if ($include == 3) {
									$value[8] = $dataStation->name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_soles'] != '') {
											$value[6] = $data['neto_soles'] - $data['importe_ci'];
										}
									}
									$value[7] += ($data['neto_soles'] != '' ? $data['neto_soles'] : 0) - $data['importe_ci'];
								}
								//$row++;
							}
							/*echo '<hr><pre>';
							var_dump($value);
							echo '</pre>';exit;*/

							$total[0] += $value[0];
							$total[1] += $value[1];
							$total[2] += $value[2];
							$total[3] += $value[3];
							$total[4] += $value[4];
							$total[5] += $value[5];
							$total[6] += $value[6];
							$total[7] += $value[7];

							$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->name);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$value[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$value[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$value[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$value[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$value[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$value[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$value[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$value[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							
							/*echo 'mod: '.$mod.'<br>';
							echo '<pre>';
							var_dump($_data);
							echo '</pre>';
							exit;*/

						} else {
							//market
						}
					} else {
						$return['status'] = 4;
					}
					$row++;
				}
				$row += 2;

				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');

				///exit;

				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_sumary_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
					            
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
			} else {
				echo 'Error al enviar datos.';
			}
		}
	}

	public function generateCaclStatistics() {
		if(checkSession()) {
			$return = array();
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				//var_dump($this->uri->segment(3));exit;
				$typeStation = $this->uri->segment(8);
				$include = $this->uri->segment(11);
				$mod = '';
				$typeEst = '';
				if($typeStation == 4) {
					$mod = 'TOTALS_STATISTICS_SALE';//termpral para este reporte con serialize
					$typeEst = 'comb';
					$titleDocument = 'Estadística de Ventas';
				} else {
					$mod = 'ERR';//falta
					$typeEst = 'market';
					$titleDocument = 'Stock de Market';
				}

				$formatDateBegin = formatDateCentralizer($this->uri->segment(6),2);
				$formatDateEnd = formatDateCentralizer($this->uri->segment(7),2);

				$_formatDateBegin = formatDateCentralizer($this->uri->segment(4),2);
				$_formatDateEnd = formatDateCentralizer($this->uri->segment(5),2);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 4 ? 'C' : 'M',$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 4 ? 'C' : 'M');
					}
				}

				//echo 'formatDateBegin: '.$formatDateBegin.', formatDateEnd: '.$formatDateEnd.', typeStation: '.$typeStation;

				//load our new PHPExcel library
				$this->load->library('calc');
				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:G1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Anterior');
				$this->calc->getActiveSheet()->setCellValue('B3', $this->uri->segment(4));
				$this->calc->getActiveSheet()->setCellValue('C3', $this->uri->segment(5));

				$this->calc->getActiveSheet()->setCellValue('A4', 'Actual');
				$this->calc->getActiveSheet()->setCellValue('B4', $this->uri->segment(6));
				$this->calc->getActiveSheet()->setCellValue('C4', $this->uri->segment(7));

				$this->calc->getActiveSheet()->setCellValue('A5', 'Empresa');

				$textInclude = '';
				if ($include == 0) {
					$textInclude = 'No';
				} else if ($include == 1) {
					$textInclude = 'Si';
				}

				$this->calc->getActiveSheet()->setCellValue('A7', 'Excluir consumo interno');
				$this->calc->getActiveSheet()->setCellValue('B7', $textInclude);

				//Inicio de cabecera	(tabla)
				$this->calc->getActiveSheet()->setCellValue('A9', 'Estación');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				$this->calc->getActiveSheet()->setCellValue('B9', '84');
				$this->calc->getActiveSheet()->setCellValue('C9', '90');
				$this->calc->getActiveSheet()->setCellValue('D9', '95');
				$this->calc->getActiveSheet()->setCellValue('E9', '97');
				$this->calc->getActiveSheet()->setCellValue('F9', 'D2');
				$this->calc->getActiveSheet()->setCellValue('G9', 'GLP');
				$this->calc->getActiveSheet()->setCellValue('H9', 'GNV');
				$this->calc->getActiveSheet()->setCellValue('I9', 'Total');
				$this->calc->getActiveSheet()->setCellValue('J9', 'Market');
				$this->calc->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A9:J9')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera		(tabla)

				$row = 10;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('B9:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('C9:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('D9:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('E9:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('F9:F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('G9:G256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('H')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('H9:H256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('I')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('I9:I256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('J')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('I9:J256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sale_total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$sale__total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$dif_total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$text = array();

				$data_ = array();
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';

						$curl = $curl . '?mod='.$mod.'&_from='.$_formatDateBegin.'&_to='.$_formatDateEnd.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&days=7&isvaliddiffmonths=si&unserialize=1';
						//var_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					//
					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$dataRemoteStations = unserialize($dataRemoteStations[0]);

						if($typeStation == 4) {
							$sale = array(0,0,0,0,0,0,0,0,0,0,0,0);
							$sale_ = array(0,0,0,0,0,0,0,0,0,0,0,0);
							$dif = array(0,0,0,0,0,0,0,0,0,0,0,0);

							foreach ($dataRemoteStations as $key => $data) {
								$data_[$dataStation->taxid][] = $data['neto_venta'];
								
								if ($include == 0) {
									if ($data['codigo'] == '11620301') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[0] = $data['neto_venta'];
										} else {
											$sale_[0] = $data['neto_venta'];
											$dif[0] = amountPercentage(array('num1' => $sale[0], 'num2' => $sale_[0]));
										}
									} else if($data['codigo'] == '11620302') {
										if ($data['_type'] == 'actual') {
											$sale[1] = $data['neto_venta'];
										} else {
											$sale_[1] = $data['neto_venta'];
											$dif[1] = amountPercentage(array('num1' => $sale[1], 'num2' => $sale_[1]));
										}
									} else if($data['codigo'] == '11620305') {
										if ($data['_type'] == 'actual') {
											$sale[2] = $data['neto_venta'];
										} else {
											$sale_[2] = $data['neto_venta'];
											$dif[2] = amountPercentage(array('num1' => $sale[2], 'num2' => $sale_[2]));
										}
									} else if($data['codigo'] == '11620303') {
										if ($data['_type'] == 'actual') {
											$sale[3] = $data['neto_venta'];
										} else {
											$sale_[3] = $data['neto_venta'];
											$dif[3] = amountPercentage(array('num1' => $sale[3], 'num2' => $sale_[3]));
										}
									} else if($data['codigo'] == '11620304') {
										if ($data['_type'] == 'actual') {
											$sale[4] = $data['neto_venta'];
										} else {
											$sale_[4] = $data['neto_venta'];
											$dif[4] = amountPercentage(array('num1' => $sale[4], 'num2' => $sale_[4]));
										}
									} else if($data['codigo'] == '11620307') {
										if ($data['_type'] == 'actual') {
											$sale[5] = $data['neto_venta'];
										} else {
											$sale_[5] = $data['neto_venta'];
											$dif[5] = amountPercentage(array('num1' => $sale[5], 'num2' => $sale_[5]));
										}
									} else if($data['codigo'] == '11620308') {
										if ($data['_type'] == 'actual') {
											if ($data['neto_venta'] != '') {
												$sale[6] = $data['neto_venta'];
											}
										} else {
											if ($data['neto_venta'] != '') {
												$sale_[6] = $data['neto_venta'];
												$dif[6] = amountPercentage(array('num1' => $sale[6], 'num2' => $sale_[6]));
											}
										}
									}

									if ($data['_type'] == 'actual' && $data['codigo'] != 'MARKET') {
										$sale[7] += $data['neto_venta'] != '' ? $data['neto_venta'] : 0;
									} else if ($data['codigo'] != 'MARKET') {
										$sale_[7] += ($data['neto_venta'] != '' ? $data['neto_venta'] : 0) - ($data['importe_ci']);
										$dif[7] = amountPercentage(array('num1' => $sale[7], 'num2' => $sale_[7]));
									}

									if($data['codigo'] == 'MARKET') {
										if ($data['_type'] == 'actual') {
											if($data['neto_venta'] != '') {
												$sale[8] = $data['neto_venta'];
											}
										} else {
											if($data['neto_venta'] != '') {
												$sale_[8] = $data['neto_venta'];
												$dif[8] = amountPercentage(array('num1' => $sale[8], 'num2' => $sale_[8]));
											}
										}
									}

									if($key == 0) {
										$text[0] = $dataStation->name;
									} else if ($key == 1) {
										$text[1] = 'Anterior';
									}
								} else {
									if ($data['codigo'] == '11620301') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[0] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[0] = $data['neto_venta'] - $data['importe_ci'];
											$dif[0] = amountPercentage(array('num1' => $sale[0], 'num2' => $sale_[0]));
										}
									} else if ($data['codigo'] == '11620302') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[1] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[1] = $data['neto_venta'] - $data['importe_ci'];
											$dif[1] = amountPercentage(array('num1' => $sale[1], 'num2' => $sale_[1]));
										}
									} else if ($data['codigo'] == '11620305') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[2] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[2] = $data['neto_venta'] - $data['importe_ci'];
											$dif[2] = amountPercentage(array('num1' => $sale[2], 'num2' => $sale_[2]));
										}
									} else if ($data['codigo'] == '11620303') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[3] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[3] = $data['neto_venta'] - $data['importe_ci'];
											$dif[3] = amountPercentage(array('num1' => $sale[3], 'num2' => $sale_[3]));
										}
									} else if ($data['codigo'] == '11620304') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[4] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[4] = $data['neto_venta'] - $data['importe_ci'];
											$dif[4] = amountPercentage(array('num1' => $sale[4], 'num2' => $sale_[4]));
										}
									} else if ($data['codigo'] == '11620307') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[5] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[5] = $data['neto_venta'] - $data['importe_ci'];
											$dif[5] = amountPercentage(array('num1' => $sale[5], 'num2' => $sale_[5]));
										}
									} else if ($data['codigo'] == '11620308') {
										//84
										if ($data['_type'] == 'actual') {
											if ($data['neto_venta'] != '') {
												$sale[6] = $data['neto_venta'] - $data['importe_ci'];
											}
										} else {
											if ($data['neto_venta'] != '') {
												$sale_[6] = $data['neto_venta'] - $data['importe_ci'];
												$dif[6] = amountPercentage(array('num1' => $sale[6], 'num2' => $sale_[6]));
											}
										}
									}

									if ($data['_type'] == 'actual' && $data['codigo'] != 'MARKET') {
										$sale[7] += ($data['neto_venta'] != '' ? $data['neto_venta'] : 0) - $data['importe_ci'];
									} else if ($data['codigo'] != 'MARKET') {
										$sale_[7] += ($data['neto_venta'] != '' ? $data['neto_venta'] : 0) - ($data['importe_ci']);
										$dif[7] = amountPercentage(array('num1' => $sale[7], 'num2' => $sale_[7]));
									}

									if($data['codigo'] == 'MARKET') {
										if ($data['_type'] == 'actual') {
											if($data['neto_venta'] != '') {
												$sale[8] = $data['neto_venta'];
											}
										} else {
											if($data['neto_venta'] != '') {
												$sale_[8] = $data['neto_venta'];
												$dif[8] = amountPercentage(array('num1' => $sale[8], 'num2' => $sale_[8]));
											}
										}
									}

									if($key == 0) {
										$text[0] = $dataStation->name;
									} else if ($key == 1) {
										$text[1] = 'Anterior';
									}
								}

								$text[2] = 'Diferencia (%)';
							}

							$sale_total[0] += $sale[0];
							$sale_total[1] += $sale[1];
							$sale_total[2] += $sale[2];
							$sale_total[3] += $sale[3];
							$sale_total[4] += $sale[4];
							$sale_total[5] += $sale[5];
							$sale_total[6] += $sale[6];
							$sale_total[7] += $sale[7];
							$sale_total[8] += $sale[8];

							$sale__total[0] += $sale_[0];
							$sale__total[1] += $sale_[1];
							$sale__total[2] += $sale_[2];
							$sale__total[3] += $sale_[3];
							$sale__total[4] += $sale_[4];
							$sale__total[5] += $sale_[5];
							$sale__total[6] += $sale_[6];
							$sale__total[7] += $sale_[7];
							$sale__total[8] += $sale_[8];

							$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[0]);
							$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale[8], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');

							$row++;
							$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[1]);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale_[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale_[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale_[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale_[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale_[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale_[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale_[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale_[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale_[8], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');

							$row++;
							$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[2]);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$dif[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$dif[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$dif[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$dif[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$dif[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$dif[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$dif[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$dif[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$dif[8], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');

						} else {
							//market
						}
					} else {
						$return['status'] = 4;
					}
					$row++;
				}

				$row += 2;

				$this->calc->getActiveSheet()->setCellValue('A'.$row, 'Totales');
				$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale_total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale_total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale_total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale_total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale_total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale_total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale_total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale_total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale_total[8], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);

				$row++;
				$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[1]);
				$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale__total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale__total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale__total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale__total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale__total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale__total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale__total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale__total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale__total[8], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);


				$dif_total[0] = amountPercentage(array('num1' => $sale_total[0], 'num2' => $sale__total[0]));
				$dif_total[1] = amountPercentage(array('num1' => $sale_total[1], 'num2' => $sale__total[1]));
				$dif_total[2] = amountPercentage(array('num1' => $sale_total[2], 'num2' => $sale__total[2]));
				$dif_total[3] = amountPercentage(array('num1' => $sale_total[3], 'num2' => $sale__total[3]));
				$dif_total[4] = amountPercentage(array('num1' => $sale_total[4], 'num2' => $sale__total[4]));
				$dif_total[5] = amountPercentage(array('num1' => $sale_total[5], 'num2' => $sale__total[5]));
				$dif_total[6] = amountPercentage(array('num1' => $sale_total[6], 'num2' => $sale__total[6]));
				$dif_total[7] = amountPercentage(array('num1' => $sale_total[7], 'num2' => $sale__total[7]));
				$dif_total[8] = amountPercentage(array('num1' => $sale_total[8], 'num2' => $sale__total[8]));

				$row++;
				$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[2]);
				$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$dif_total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$dif_total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$dif_total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$dif_total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$dif_total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$dif_total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$dif_total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$dif_total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$dif_total[8], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);

				/*echo '<pre>';
				var_dump($data_);
				echo '</pre>';
				exit;*/

				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_statistics_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
					            
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
			} else {
				echo 'Error al enviar datos.';
			}
		}
	}

	/*public function generateCaclSumary() {
		if(checkSession()) {
			$data = $_GET;
			var_dump($data);

			//$this->tmp = $data;
			//$this->demoExcel();
			$_SESSION['data_tmp'] = $data;
		}
	}*/

	/**
	 * Demo
	 */
	public function combSales2()
	{
		$result = array();
		$stations = $this->input->post('stations');
		foreach ($stations as $key => $station) {
			$result[] = array('name' => $station, 'value' => 1);
		}
		echo json_encode($result);
	}

	/**
	 * Demo
	 */
	public function demoExcel()
	{
		//load our new PHPExcel library
		$this->load->library('calc');
		//$this->load->library('someclass');
		//var_dump($this->someclass);

		//activate worksheet number 1
		$this->calc->setActiveSheetIndex(0);
		//name the worksheet
		$this->calc->getActiveSheet()->setTitle('test worksheet');
		//set cell A1 content with some text
		$this->calc->getActiveSheet()->setCellValue('A1', 'This is just some text value');
		//change the font size
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		//make the font become bold
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		$this->calc->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$filename='just_some_random_name.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	public function demoajax()
	{
		//load our new PHPExcel library
		$this->load->library('calc');
		//$this->load->library('someclass');
		//var_dump($this->someclass);

		//activate worksheet number 1
		$this->calc->setActiveSheetIndex(0);
		//name the worksheet
		$this->calc->getActiveSheet()->setTitle('test worksheet');
		//set cell A1 content with some text
		$text = '';
		if($_SESSION['data_tmp'] != '') {
			$text = ' si existe TMP :)';
		}
		$this->calc->getActiveSheet()->setCellValue('A1', 'This is just some text value'.$text);
		//change the font size
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		//make the font become bold
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		$this->calc->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$filename='just_some_random_name.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		unset($this->tmp);
		$objWriter->save('php://output');
	}
}
