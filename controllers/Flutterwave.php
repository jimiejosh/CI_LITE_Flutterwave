<?php
/*       The MIT License (MIT)
	*	Copyright (c) 2018 josh jimie <joshjimie@gmail.com>
	*
	*	Permission is hereby granted, free of charge, to any person obtaining a copy
	*	 of this software and associated documentation files (the "Software"), to deal
	*	 in the Software without restriction, including without limitation the rights
	*	 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	*	 copies of the Software, and to permit persons to whom the Software is
	*	 furnished to do so, subject to the following conditions:
	*
	*	The above copyright notice and this permission notice shall be included in
	*	all copies or substantial portions of the Software.
	*
	*	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	*	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	*	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	*	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	*	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	*	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	*	THE SOFTWARE.
	*	 
	*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Flutterwave extends CI_Controller {

	 
	public $app_version = 1;

	 function __construct()
	  {
	    parent::__construct(); 
			  
			$this->load->helper('url');
			$this->load->library('session'); 
		$this->load->library('form_validation');
		$this->config->load('flutterwave', TRUE); //or instead your file name.
			$this->load->model('flutterwave_model');  

		 //  $this->output->enable_profiler(TRUE);
	  }


function index($ref = false){
 
	die('Invalid Reference Provided' );  
	} 
	
function pay($ref = false){
 if (! $ref){ 
	die('Invalid Reference Provided' );  
	} 
	//generate payment link
	$payment_link = $this->flutterwave_model->pay( $ref);
 	 
	if($payment_link['status'] == true ){ redirect( $payment_link['msg']); } else { echo   $payment_link['msg'] ; }
}




function verify(){

	 $txref = $this->input->get_post('txref', true);

	if( isset($txref) ){ 
		$sales = $this->flutterwave_model->get_invoice( $txref );
	} else { 
		echo 'Reference not available! <a href="'.base_url().'">Return home</a>';
		exit();
	}
	$this->flutterwave_model->verify_update( $txref );


}

}
