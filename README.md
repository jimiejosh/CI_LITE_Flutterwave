## CI_LITE_Flutterwave - Codeigniter - FLutterwave Standard Payment Library
	Simple and Lite FLUTTERWAVE Standard Payment Library on codeigniter

## Class Features

- Pay with Flutterwave!
- Multi currency support
- Add auto generated invoice 
- Light weight and with demo (Standard)
- Support country and currency
- Need no install and has no database, just copy files to the right folder
- Can be integrated to any Codeigniter application
- Tested with codeigniter version 3.0 but backward compactible with version 2.0
- Compatible with PHP 5.0 and later
- Much more!

## Why you might need it

 To manually integrate FLutterwave into your website. When you're done, you will have added a Flutterwave Payments and supporting code to your website so that customers can click to place orders through Fluterwave.


## Currency and countries supported
NG (Nigeria)
NGN
USD
EUR
GBP

UGX ( Uganda)
TZS ( Tanzania)
GHS ( Ghana )
USD ( For Ghana Merchants only)
KES ( Kenya Card & Mpesa payments)
ZAR ( South africa)

## License

This software is distributed under the MIT license. Please read LICENSE.txt for information on the
software availability and distribution.

## Installation & loading



    Drop the provided files into the CodeIgniter project
    Configure your flutterwave details inside the application/config/flutterwave.php file refer to https://developer.flutterwave.com/docs/
    Modify the controller example supplied (application/controller/fluterwave.php) to fit your needs

	
## A Simple Example

  To use CI_LITE_Flutterwave load the library in your controller
 

		View sample controller code below
```php
<?php
class Flutterwave extends CI_Controller {

	 
	public $app_version = 1;

	 function __construct()
	  {
	  	parent::__construct(); 	  
		$this->load->helper('url');
		$this->load->library('session'); 
		$this->load->library('form_validation');
		$this->config->load('flutterwave', TRUE); 
		$this->load->model('flutterwave_model');  
	  }


function index(){
 
	//generate payment link
	$payment_link = $this->flutterwave_model->getpaymentlink( false,false, false ,'demo' ,'NGN',  'NG' );
 	$newinvoice = array(
		//add all the values needed for the invoice here
		);
	 $this->flutterwave_model->insert_invoice( $newinvoice);
	redirect($payment_link);
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
	
	
```

You'll find it easy to implement.

That's it. You should now be ready to use CI_LITE_Flutterwave!

## Localization
CI_LITE_Flutterwave defaults to English
