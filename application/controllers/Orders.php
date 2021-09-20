<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Orders';
		$this->load->model('model_services');
		$this->load->model('model_orders');
		$this->load->model('model_products');
		$this->load->model('model_company');
	}

	/* 
	* It only redirects to the manage order page
	*/
	public function index()
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Manage Orders';
		$this->render_template('orders/index', $this->data);		
	}

	/*
	* Fetches the orders data from the orders table 
	* this function is called from the datatable ajax function
	*/
	public function fetchOrdersData()
	{
		$result = array('data' => array());

		$data = $this->model_orders->getOrdersData();

		foreach ($data as $key => $value) {

			$count_total_item = $this->model_orders->countOrderItem($value['id']);
			$date = date('d-m-Y', $value['date_time']);
			$time = date('h:i a', $value['date_time']);

			$date_time = $date . ' ' . $time;

			// button
			$buttons = '';

			if(in_array('viewOrder', $this->permission)) {
				if($value['type']==1)
				$buttons .= '<a target="__blank" href="'.base_url('orders/printDiv/'.$value['id']).'" class="btn btn-default"><i class="fa fa-print"></i></a>';
			    else if($value['type']==2)
				$buttons .= '<a target="__blank" href="'.base_url('orders/printDivs/'.$value['id']).'" class="btn btn-default"><i class="fa fa-print"></i></a>';

			}

			if(in_array('updateOrder', $this->permission)) {
                if($value['type']==1)
				$buttons .= ' <a href="'.base_url('orders/update/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			    elseif($value['type']==2)
			    	$buttons .= ' <a href="'.base_url('orders/updates/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			}

			if(in_array('deleteOrder', $this->permission)) {

				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}

			if($value['paid_status'] == 1) {
				$paid_status = '<span class="label label-success">Paid</span>';	
			}
			else {
				$paid_status = '<span class="label label-warning">Not Paid</span>';
			}
            if($value['type']==1){
			$result['data'][$key] = array(
				$value['bill_no'],
				"Pro - ".$value['id'],
				$value['customer_name'],
				$value['customer_phone'],
				$date_time,
				$count_total_item,
				$value['net_amount'],
				$paid_status,
				$buttons
			);
		}
		else if($value['type']==2)
		{
			$result['data'][$key] = array(
				$value['bill_no'],
				"Ser - ".$value['id'],
				$value['customer_name'],
				$value['customer_phone'],
				$date_time,
				$count_total_item,
				$value['net_amount'],
				$paid_status,
				$buttons
			);
		}
		} // /foreach

		echo json_encode($result);
	}

	/*
	* If the validation is not valid, then it redirects to the create page.
	* If the validation for each input field is valid then it inserts the data into the database 
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function create()
	{
		if(!in_array('createOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Add Order';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$order_id = $this->model_orders->create();
        	
        	if($order_id) {
        		$this->session->set_flashdata('success', 'Successfully created');
        		redirect('orders/update/'.$order_id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/create/', 'refresh');
        	}
        }
        else {
            // false case
        	$company = $this->model_company->getCompanyData(1);
        	$this->data['company_data'] = $company;
        	$this->data['service_vat'] = false;
        	$this->data['product_vat'] = ($company['product_vat'] > 0) ? true : false;

        	$this->data['products'] = $this->model_products->getActiveProductData();      	

            $this->render_template('orders/create', $this->data);
        }	
	}

	/*
	* It gets the product id passed from the ajax method.
	* It checks retrieves the particular product data from the product id 
	* and return the data into the json format.
	*/
	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		if($product_id) {
			$product_data = $this->model_products->getProductData($product_id);
			echo json_encode($product_data);
		}
	}


	public function getserviceValueById()
	{
		$product_id = $this->input->post('product_id');
		if($product_id) {
			$product_data = $this->model_services->getServiceData($product_id);
			echo json_encode($product_data);
		}
	}


	public function creates()
	{
		if(!in_array('createOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Add Order';

		$this->form_validation->set_rules('service[]', 'Service name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$order_id = $this->model_orders->create();
        	
        	if($order_id) {
        		$this->session->set_flashdata('success', 'Successfully created');
        		redirect('orders/updates/'.$order_id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/creates/', 'refresh');
        	}
        }
        else {
            // false case
        	$company = $this->model_company->getCompanyData(1);
        	$this->data['company_data'] = $company;
        	$this->data['service_vat'] = ($company['service_vat'] > 0) ? true : false;
        	$this->data['product_vat'] = ($company['product_vat'] > 0) ? true : false;

        	$this->data['services'] = $this->model_services->getActiveServiceData();      	

            $this->render_template('orders/creates', $this->data);
        }	
	}
	/*
	* It gets the all the active product inforamtion from the product table 
	* This function is used in the order page, for the product selection in the table
	* The response is return on the json format.
	*/
	public function getTableProductRow()
	{
		$products = $this->model_products->getActiveProductData();
		echo json_encode($products);
	}

	public function getTableServiceRow()
	{
		$products = $this->model_services->getActiveServiceData();
		echo json_encode($products);
	}

	/*
	* If the validation is not valid, then it redirects to the edit orders page 
	* If the validation is successfully then it updates the data into the database 
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function update($id)
	{
		if(!in_array('updateOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if(!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Update Order';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$update = $this->model_orders->update($id);
        	
        	if($update == true) {
        		$this->session->set_flashdata('success', 'Successfully updated');
        		redirect('orders/update/'.$id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/update/'.$id, 'refresh');
        	}
        }
        else {
            // false case
        	$company = $this->model_company->getCompanyData(1);
        	$this->data['company_data'] = $company;
        	$this->data['service_vat'] = false;
        	$this->data['product_vat'] = ($company['product_vat'] > 0) ? true : false;

        	$result = array();
        	$orders_data = $this->model_orders->getOrdersData($id);

    		$result['order'] = $orders_data;
    	     $orders_item = $this->model_orders->getOrdersItemData($orders_data['id']);
             $flag = 0;
    		foreach($orders_item as $k => $v) {
    			$result['order_item'][] = $v;
    			if($v['service_id']!= NULL)
    				$flag = 1;
    			else if($v['product_id']!=NULL)
    				$flag = 2;
    		}
    		if($flag == 1)
            {
	    		$this->data['order_data'] = $result;

	        	$this->data['services'] = $this->model_services->getActiveServiceData();      	

	            $this->render_template('orders/edits', $this->data);
           }
           else if($flag ===2)
           {
           	    $this->data['order_data'] = $result;

	        	$this->data['products'] = $this->model_products->getActiveProductData();      	

	            $this->render_template('orders/edit', $this->data);
           }
        }
	}


	public function updates($id)
	{
		if(!in_array('updateOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if(!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Update Order';

		$this->form_validation->set_rules('service[]', 'Service name', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$update = $this->model_orders->update($id);
        	
        	if($update == true) {
        		$this->session->set_flashdata('success', 'Successfully updated');
        		redirect('orders/updates/'.$id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/updates/'.$id, 'refresh');
        	}
        }
        else {
            // false case
        	$company = $this->model_company->getCompanyData(1);
        	$this->data['company_data'] = $company;
        	$this->data['service_vat'] = ($company['service_vat'] > 0) ? true : false;
        	$this->data['product_vat'] = false;

        	$result = array();
        	$orders_data = $this->model_orders->getOrdersData($id);

    		$result['order'] = $orders_data;
    		$orders_item = $this->model_orders->getOrdersItemData($orders_data['id']);

    		foreach($orders_item as $k => $v) {
    			$result['order_item'][] = $v;
    		}

    		$this->data['order_data'] = $result;

        	$this->data['services'] = $this->model_services->getActiveServiceData();      	

            $this->render_template('orders/edits', $this->data);
        }
	}



	/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function remove()
	{
		if(!in_array('deleteOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$order_id = $this->input->post('order_id');

        $response = array();
        if($order_id) {
            $delete = $this->model_orders->remove($order_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Successfully removed"; 
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Error in the database while removing the product information";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refersh the page again!!";
        }

        echo json_encode($response); 
	}

	/*
	* It gets the product id and fetch the order data. 
	* The order print logic is done here 
	*/


function convertNumberToWord($num = false)
{
		if(!in_array('deleteOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
    $num = str_replace(array(',', ' '), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven',
        'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
    );
    $list2 = array('', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety', 'Hundred');
    $list3 = array('', 'Thousand', 'Million', 'Billion'
    );
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : '' ) . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    }
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    $data= implode(' ', $words);
	return $data." ".'Birr Only';
}


	public function printDiv($id)
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);

			$order_date = date('d/m/Y', $order_data['date_time']);
			$paid_status = ($order_data['paid_status'] == 1) ? "Paid" : "Unpaid";
			$net_amount=$order_data['gross_amount']+$order_data['vat_charge']; 
			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>Sonet | Invoice</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">
			  <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

			</head>
			<body onload="window.print();">
			
			<div class="wrapper">
			<div style="display:flex;">
			<div style="width:60%">
			<img src="'.base_url('logo.jpg').'" width="250px" height="150px"></img>
			</div>
			<div style="width:40%"><br>
			<table>';
			if($order_data['paid_status'] == 1){
			$html .=' 
			<tr><th colspan="3"><center><h5><b>Cash Sales Attachment </center> </th></tr>
			<tr><th><font color="green">Company</font></th><td>'.$company_info['company_name'].'</td></tr>
			          <tr><th><font color="green">FS Number</font></th><td>&nbsp SO-IN-'.$order_data['id'].'</td></tr>
			           <tr><th><font color="green">Date</font></th><td>&nbsp'.$order_date.'</td></tr>
			          </table></b>
			          </small>';
		}
			else{
			$html .='
			<tr><th colspan="3"><center><h5><b>Proforma </center> </th></tr>	

			          <tr><th><font color="green">Company</font></th><td>&nbsp'.$company_info['company_name'].'</td></tr>
			          <tr><th><font color="green">Proforma No</font></th><td>&nbsp SO-PR-'.$order_data['id'].'</td></tr>
			           <tr><th><font color="green">Date</font></th><td>&nbsp'.$order_date.'</td></tr>
			          </table></b>
			          </small>
			          
			      ';
			  }
			 $html .='
			          </div>
			</div>
			
			   <section class="invoice">
			    <!-- title row -->
			   <div class="col" style="width: 100%;display:flex">
  <div class="row" style="width: 56%; margin-left:1px;">
    <h3><font color="green">FROM</font></h3><hr color="green" style=" margin-right:200px;border-color:green">
    <pre style="background:#fff; border:0px">Sub City: Kirkos, Woreda 01
Vat No. 8111490821
Tin 0008332271
Tel: 251-911670484
Email: info@sonetict.com
P.O Box 23543/1000
Addis Ababa, Ethiopia</pre>
  </div>
  <div class="row" style="width: 34%">
    <h3><font color="green">TO</font></h3><hr  style=" border-color: green" ></hr>
                      
              <b><font color="green">Tin No:</font></b> '.$order_data['tin_no'].'<br>
              <b><font color="green">Name:</b></font> '.$order_data['customer_name'].'<br>
              <b><font color="green">Address:</b> </font>'.$order_data['customer_address'].' <br />
              <b><font color="green">Phone:</b></font> '.$order_data['customer_phone'].'
  </div>
</div>

			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 ">
			        <table border="1px" bordercolor="white"  class="table " >
			          <thead">
			          <tr style="color:green" class="table ">
			            <th>Item no.</th>
			            <th colspan="2">Description</th>
			            <th>Qty</th>
			            <th>Price</th>
			            <th>Total</th>
			          </tr>
			          </thead>
			          <tbody>

			          ';  

			          foreach ($orders_items as $k => $v) {

			          	$product_data = $this->model_products->getProductData($v['product_id']);   
			          		
			          	
			          	$html .= '<tr>
				            <th style="background-color:#F1F1F1 !important;">'.$product_data['id'].'</td>
				            <th style="background-color:#F1F1F1 !important;">'.$product_data['name'].'</td>
				            <td style="background-color:#F1F1F1 !important;"></td>
				            <th style="background-color:#F1F1F1 !important;">'.$v['qty'].'</td>
				            <th style="background-color:#F1F1F1 !important;">'.$v['rate'].'</td>
				            <th style="background-color:#F1F1F1 !important;">'.$v['amount'].'</td>
			          	</tr>
			          	<tr border-bottom: 1px solid #ddd;"><td style="background-color:#F1F1F1 !important;"></td>
			          	<td style="color:gray;background-color:#F1F1F3 !important;"><h6>'.$product_data['description'].'</h6></td><td style="background-color:#F1F1F1 !important;"></td><td style="background-color:#F1F1F1 !important;"></td><td style="background-color:#F1F1F1 !important;"></td>
			          	<td style="background-color:#F1F1F1 !important;"></td> </tr>';
			           
			          }
			          
			          $html .= '

			            <tr><td></td><td></td><td></td><td></td>
			              <th style="background-color:#F1F1F1 !important;">Sub total:</th>
			              <td style="background-color:#F1F1F1 !important"><b>'.$order_data['gross_amount'].'</td>
			            </tr>';

			             if($order_data['vat_charge'] > 0) {
			            	$html .= '<tr><td></td><td></td><td></td><td></td>
				              <th style="background-color:#F1F1F1 !important">Vat Charge ('.$order_data['vat_charge_rate'].'%)</th>
				              <td style="background-color:#F1F1F1 !important"><b>'.$order_data['vat_charge'].'</td>
				            </tr>';
			            }
			            
			            $html .='
			             <tr><td></td><td></td><td></td><td></td>
			              <th style="background-color:#F1F1F1 !important;">Paid Status:</th>
			              <td style="background-color:#F1F1F1 !important; "><b>'.$paid_status.'</td>
			            </tr>
			            <tr><td></td><td></td><td></td><td></td>
			              <th style="background-color:green !important; color:white">Net Amount:</th>
			              <td style="background-color:green !important; color:white"><b>'.$net_amount.'</td>
			            </tr>

			           <tr><td></td><td></td><td></td><td></td><td colspan="2" style="background-color:gray !important; color:black"><center><b>'.$this->convertNumberToWord($net_amount).' </td></tr>
			          </table>
			       
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			  echo $html;
	
		}
	}

	public function printDivs($id)
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);

			$order_date = date('d/m/Y', $order_data['date_time']);
			$paid_status = ($order_data['paid_status'] == 1) ? "Paid" : "Unpaid";
			$net_amount=$order_data['gross_amount']+$order_data['vat_charge']; 
			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>Sonet | Invoice</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">
			  <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

			</head>
			<body onload="window.print();">			
			<div class="wrapper">
			<div style="display:flex;">
			<div style="width:60%">
			<img src="'.base_url('logo.jpg').'" width="250px" height="150px"></img>
			</div>
			<div style="width:40%"><br>
			<table>';
			if($order_data['paid_status'] == 1){
			$html .=' 
			<tr><th colspan="3"><center><h5><b>Cash Sales Attachment </center> </th></tr>
			<tr><th><font color="green">Company</font></th><td>'.$company_info['company_name'].'</td></tr>
			          <tr><th><font color="green">FS Number</font></th><td>&nbsp SO-IN-'.$order_data['id'].'</td></tr>
			           <tr><th><font color="green">Date</font></th><td>&nbsp'.$order_date.'</td></tr>
			          </table></b>
			          </small>';
		}
			else{
			$html .='
			<tr><th colspan="3"><center><h5><b>Proforma </center> </th></tr>	
			
			
			          			        
			   
			          <tr><th><font color="green">Company</font></th><td>&nbsp'.$company_info['company_name'].'</td></tr>
			          <tr><th><font color="green">Proforma No</font></th><td>&nbsp SO-PR-'.$order_data['id'].'</td></tr>
			           <tr><th><font color="green">Date</font></th><td>&nbsp'.$order_date.'</td></tr>
			          </table></b>
			          </small>
			          
			      ';
			  }
			 $html .='
			 </div>
			</div>
			   <section class="invoice">
			    <!-- title row -->
			     <div class="col" style="width: 100%;display:flex">
  <div class="row" style="width: 56%; margin-left:1px;">
    <h3><font color="green">FROM</font></h3><hr color="green" style=" margin-right:200px;border-color:green">
    <pre style="background:#fff; border:0px">Sub City: Kirkos, Woreda 01
Vat No. 8111490821
Tin 0008332271
Tel: 251-911670484
Email: info@sonetict.com
P.O Box 23543/1000
Addis Ababa, Ethiopia</pre>
  </div>
  <div class="row" style="width: 34%">
    <h3><font color="green">TO</font></h3><hr  style=" border-color: green" ></hr>
                      
              <b><font color="green">Tin No:</font></b> '.$order_data['tin_no'].'<br>
              <b><font color="green">Name:</b></font> '.$order_data['customer_name'].'<br>
              <b><font color="green">Address:</b> </font>'.$order_data['customer_address'].' <br />
              <b><font color="green">Phone:</b></font> '.$order_data['customer_phone'].'
  </div>
</div>

			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table border="1px" bordercolor="white"  class="table table-striped" >
			          <thead">
			          <tr style="color:green" class="table table-striped">
			            <th>Item no.</th>
			            <th colspan="2">Description</th>
			            <th>Qty</th>
			            <th>Price</th>
			            <th>Total</th>
			          </tr>
			          </thead>
			          <tbody>

			          ';  

			          foreach ($orders_items as $k => $v) {

			          	$product_data = $this->model_services->getServiceData($v['service_id']);   
			          		
			          	
			          	$html .= '<tr>
				            <th style="background-color:#F1F1F1 !important;">'.$product_data['id'].'</td>
				            <th style="background-color:#F1F1F1 !important;">'.$product_data['name'].'</td>
				            <td style="background-color:#F1F1F1 !important;"></td>
				            <th style="background-color:#F1F1F1 !important;">'.$v['qty'].'</td>
				            <th style="background-color:#F1F1F1 !important;">'.$v['rate'].'</td>
				            <th style="background-color:#F1F1F1 !important;">'.$v['amount'].'</td>
			          	</tr>
			          	<tr border-bottom: 1px solid #ddd;"><td style="background-color:#F1F1F1 !important;"></td>
			          	<td style="color:gray;background-color:#F1F1F3 !important;"><h6>'.$product_data['description'].'</h6></td><td style="background-color:#F1F1F1 !important;"></td><td style="background-color:#F1F1F1 !important;"></td><td style="background-color:#F1F1F1 !important;"></td>
			          	<td style="background-color:#F1F1F1 !important;"></td> </tr>';
			           
			          }
			          
			          $html .= '

			            <tr><td></td><td></td><td></td><td></td>
			              <th style="background-color:#F1F1F1 !important;">Sub total:</th>
			              <td style="background-color:#F1F1F1 !important"><b>'.$order_data['gross_amount'].'</td>
			            </tr>';

			             if($order_data['vat_charge'] > 0) {
			            	$html .= '<tr><td></td><td></td><td></td><td></td>
				              <th style="background-color:#F1F1F1 !important">Vat Charge ('.$order_data['vat_charge_rate'].'%)</th>
				              <td style="background-color:#F1F1F1 !important"><b>'.$order_data['vat_charge'].'</td>
				            </tr>';
			            }
			            
			            $html .='
			             <tr><td></td><td></td><td></td><td></td>
			              <th style="background-color:#F1F1F1 !important;">Paid Status:</th>
			              <td style="background-color:#F1F1F1 !important; "><b>'.$paid_status.'</td>
			            </tr>
			            <tr><td></td><td></td><td></td><td></td>
			              <th style="background-color:green !important; color:white">Net Amount:</th>
			              <td style="background-color:green !important; color:white"><b>'.$net_amount.'</td>
			            </tr>

			           <tr><td></td><td></td><td></td><td></td><td colspan="2" style="background-color:gray !important; color:black"><center><b>'.$this->convertNumberToWord($net_amount).' </td></tr>
			          </table>
			       
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			  echo $html;
	
		}
	}

}