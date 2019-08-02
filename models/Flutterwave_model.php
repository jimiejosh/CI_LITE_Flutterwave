<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
class Flutterwave_model extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
	}       

  


 
   public function pay($reference = false ,$type = 'live' , $customer_email = false, $currency = 'NGN', $country = 'NG' ){
///////////////

            $invoice_details = $this->db->get_where('invoice', array(
                'invoice_id' => $reference
            ))->row();
			
	//demo or live for type
	$customer_email = ( $customer_email == false )?'rand'.rand(10000,9999999).'@example.com':$customer_email;
	 //Add customer email here
	 //check documentation for other currencies and country codes
	 
	 //set payment reference

	 $amount = $invoice_details->amount;

 
 
	
	$txref = $reference; // ensure you generate unique references per transaction.
	$PBFPubKey = $this->config->item('flutterwave_key' , 'flutterwave'); // get your public key from the dashboard. 
	$redirect_url = base_url()."index.php/flutterwave/verify"; 
 

	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => json_encode([
	    'amount'=>$amount,
	    'customer_email'=>$customer_email,
	    'currency'=>$currency,
	    'txref'=>$txref,
	    'PBFPubKey'=>$PBFPubKey,
	    'redirect_url'=>$redirect_url, 
	    'payment_options' => 'account,ussd,card,mpesa,mobilemoneyghana,qr',
	    'country' => $country, 
            'custom_description' => "Make Payment for Invoive",
            'custom_title' => $invoice_details->title ]
	),
	  CURLOPT_HTTPHEADER => [
	    "content-type: application/json",
	    "cache-control: no-cache"
	  ],
	));

	 $response = curl_exec($curl);
	 
	 $err = curl_error($curl);

	if($err){ 
	return array('status' => false, 'msg' => 'We could not process this request. Please press the back button on your browser or reload this page');
	//exit();
	}
	$transaction = json_decode($response);

	if(!$transaction->data && !$transaction->data->link){
	  // there was an error from the API
	//  print_r('API returned error: ' . $transaction->message);
return array('status' => false, 'msg' => 'Please press the back button on your browser or reload this page');
	 
	//exit();
	}
 
	// redirect to page so User can pay  
	return array('status' => true, 'msg' => $transaction->data->link);
	;


///////////////
}


    public function verify_update($txref) {
///////////////
 
  if (isset($txref)) {
	  
 $status = $this->db->get_where('invoice', array(
                'invoice_id' => $reference
            ))->row();
			
			
		if($status['status'] == 'paid'){
		echo 'Transaction has been paid successfully! <a href="'.base_url().'">Return home</a>';
		 } else {

		$ref = $txref;
		$amount = $status->amount; //Correct Amount from Server
		$currency = 'NGN'; //Correct Currency from Server 
		$query = array(
		    "SECKEY" => $this->config->item('flutterwave_secretkey' , 'flutterwave'), 
		    "txref" => $ref 
		);

        $data_string = json_encode($query);
                
        $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch);

        $resp = json_decode($response, true); 
      	$paymentStatus = $resp['data']['status'];
        $chargeResponsecode = $resp['data']['chargecode'];
        $chargeAmount = $resp['data']['amount'];
        $chargeCurrency = $resp['data']['currency'];

        if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount)  && ($chargeCurrency == $currency)) {
          // transaction was successful...
  			 // please check other things like whether you already gave value for this ref


	 
	  $updater = array(
              'due' => 0,
              'amount_paid' => $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->amount_paid + $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->due,
              'payment_timestamp' => strtotime(date("m/d/Y")),
              'payment_method' => 'Flutterwave',
              'status' => 'paid'
            );
            $this->db->where($checker);
            $this->db->update('invoice', $updater);

 
echo 'Transaction has been paid successfully! <a href="'.base_url().'">Return home</a>';
	 
	 
          // if the email matches the customer who owns the product etc
          //Give Value and return to Success page
        } else {
            //Dont Give Value and return to Failure page
        }
	}
    }
		else {
      die('No reference supplied');
    }
//////////////
 }
    
} 
