<?php
/*
Plugin Name: Eshopbox update cod pincodes
Plugin URI: http://www.vaibhavign.com
Description: Bluedart integration panel
Version: 1.0
Author: Vaibhav Sharma
Author Email: http://www.vaibhavign.com
*/

/**
 * Copyright (c) `date "+%Y"` Vaibhav Sharma. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_updatepincodes{
    public function __construct(){
        add_action('admin_menu', array( &$this, 'woocommerce_bluedart_admin_menu' )); 
       
    }
                            
function woocommerce_bluedart_admin_menu() {
    
    add_menu_page(__('Update pincode','wc-bluedart'), __('Update pincode','wc-bluedart'), 'edit_posts', 'eshopbox-pincode', array( &$this, 'update_pincode_page' ) );
    add_submenu_page( 'eshopbox-bluedart', 'Config', 'Config', 'edit_posts', 'bluedart_config', array( &$this, 'bluedart_config_page' ) );
}
 
function update_pincode_page (){
       global $wpdb;
    ?>
    
                 <div class="wrap">
			<div id="icon-options-general" class="icon32">
				<br />
			</div> 
			<h2><?php _e( 'Eshopbox Bluedart panel', 'wc-bluedart' ); ?></h2>
			<?php if ( isset( $_POST['pip_fields_submitted'] ) && $_POST['pip_fields_submitted'] == 'submitted' ) { ?>
			<div id="message" class="updated fade"><p><strong><?php _e( 'Your settings have been saved.', 'wc-bluedart' ); ?></strong></p></div>
			<?php } ?>
			<p><?php _e( 'Change settings for bluedart panel.', 'wc-bluedart' ); ?></p>
			<div id="content">
			  <form method="post" name="batchforma" id="batchforma" action="" enctype="multipart/form-data" >
				 
				  <div id="poststuff">
						<div class="postbox">
							<h3 class="hndle"><?php _e( 'Download softdata with manifest id', 'wc-bluedart' ); ?></h3>
							<div class="inside pip-preview">
							  <table class="form-table">
							    <tr>
    								<th>
    									<label for="eshopbox_bluedart_store_name"><b><?php _e( 'Manifest id:', 'wc-bluedart' ); ?></b></label>
    								</th>
    								<td>
    									
    									          <input type="file" name="batch" id="batch" />
            <input type="radio" name="rad" value="aramex" /> Aramex
            <input type="radio" name="rad" value="bluedart" /> Bluedart
            
    								</td>
    							</tr>
    							
    				<tr>
    								
    								<td>
    						 <p class="submit">
				 <input type="submit" name="subbatch" value="submit" />
                                  <input type="hidden" name="post1" value="post" />
			  </p>			
    									         
    								</td>
    							</tr>		
                                                                                 
                        
								</table>
							</div>
						</div>
					</div>
			 
		    </form>
		  </div>
		</div>
<?php
if($_POST['post1']=='post'){
  
        // echo '<pre>'; print_r($_FILES);
        // print_r($_POST);
	// for text files
// for excel file
//	if($_FILES['csvtext']['type']=='application/vnd.ms-excel'){
  $myFile = $_FILES['batch']['tmp_name']; 
//set_include_path(get_include_path() . PATH_SEPARATOR . 'class/');
include_once('class/PHPExcel/IOFactory.php');
try {
    
$objReader = PHPExcel_IOFactory::createReader('Excel5');

$objPHPExcel = $objReader->load($myFile);

//	$objPHPExcel = PHPExcel_IOFactory::load($myFile);

//echo 'test123'; exit;
} catch(Exception $e) {

	die('Error loading file "'.pathinfo($myFile,PATHINFO_BASENAME).'": '.$e->getMessage());

}



$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

//echo '<pre>';
//print_r($sheetData);
//exit;
$i=0;
foreach($sheetData as $sheetdata){

	$pincodes = trim($sheetdata['C']);
if(trim($sheetdata['N'])=='Yes'){
$pincode[$pincodes]= $pincodes ;
//$pincode[$i] = $pincodes ;
//print_r($sheetdata);
$i++;
}
}

$querystr = "SELECT pincode FROM wp_cod_dtdc";
$postid = $wpdb->get_results($querystr,ARRAY_A);
foreach($postid as $key=>$val){
    $dbpincode[$val['pincode']]=$val['pincode'];
}

$arrayToINsert = array_diff($pincode,$dbpincode);

$arrayToDelete = array_diff($dbpincode,$pincode);

echo sizeof($arrayToINsert).'--'.sizeof($arrayToDelete);

foreach($arrayToDelete as $key=>$val){
  //  $querystr = $wpdb->get_results("delete FROM wp_cod_dtdc where pincode=".$val);

}

foreach($arrayToINsert as $key=>$val){
    
  //  $querystr = $wpdb->get_results("delete FROM wp_cod_dtdc where pincode=".$val);

    $wpdb->insert( 
	'wp_cod_dtdc', 
	array( 
		'pincode' => $val

	)
);
   
}



//echo $i.'---';
//echo sizeof($pincode);
//echo 'test';
//echo '<pre>';
//print_r($pincode);
//print_r($arrayToDelete);
exit;

//print_r($xyz);

//	}
	//echo '<pre>';
	//print_r($_FILES);
         
     }

}



 
 function readArrayExportxls($orderIds){
      global $wpdb,$woocommerce;
                $finalarray[]=array("Airwaybill","Type","Reference Number","Sender / Store name","attention","address1","address2","address3","pincode","tel number","mobile number","Prod/SKU code","contents"
                ,"weight","Declared Value","Collectable Value","Vendor Code","Shipper Name","Return Address1","Return Address2","Return Address3","Return Pin","Length ( Cms )","Bredth ( Cms )","Height ( Cms )","Pieces","Area_customer_code","Handover Date","Handover Time"
                );

                
            foreach($orderIds as $key=>$val){
                 $theorder = new WC_Order($val);
              //   echo '<pre>';
              //   print_r($theorder);
                 $items = $theorder->get_items();
                 $product_id="";
                 $product_name="";
                 $productWeight="";
                 $quant = "";
                  foreach ( $items as $item ) {
                     // echo '<pre>';
                     // print_r($item);
                                           $p =  get_post_meta($item['product_id']);
                     

                     
                  $product_id .= $p['_sku'][0].',';
    $_product = $theorder->get_product_from_item( $item );
    $product_name .= $item['name'].',';
   // $product_id .= $item['product_id'].',';
    $product_variation_id = $item['variation_id'];
  //  $productWeight += $_product->get_weight()/1000;
     $productWeight += 0.4;
   // $quant +=$item['qty'];
         $quant =1;

}
               //  echo '<pre>';
              //   print_r($theorder);
$shipperName= get_option('shipper_name');
$shipperPin = get_option('return_pincode');
                 $vendorCode = get_option('vendor_code');
                 if($theorder->payment_method=='cod'){
                     $totalCollectible = $theorder->order_total;
                     $custCode = get_option('cod_areacustomer');
                     $payType = "COD";
                 } else {
                     $totalCollectible = 0;
                     $custCode = get_option('prepaid_areacustomer');
                     $payType = "NONCOD";
                 }
                 
                 if($theorder->shipping_address_2==''){
                     $shipAddress2 = '-';
                 } else {
                     $shipAddress2 = $theorder->shipping_address_2;
                 }
                 
                 if(get_option('return_address1')==''){
                     $shipperAddress1 = "-";
                 } else {
                     $shipperAddress1 = get_option('return_address1');
                 }
                 
                 if(get_option('return_address2')==''){
                     $shipperAddress2 = "-";
                 } else {
                     $shipperAddress2 = get_option('return_address2');
                 }
                 
                  if(get_option('return_address3')==''){
                     $shipperAddress3 = "-";
                 } else {
                     $shipperAddress3 = get_option('return_address3');
                 }
                 
     $dateTime = explode(' ',date('d-m-Y h:m:s',$manifestDetails[0]->dates));
                if($_POST['rad']=='' || $_POST['rad']=='both'){
                 $finalarray[] = array($theorder->order_custom_fields['_tracking_number'][0],$payType,$theorder->id,$shipperName,$theorder->shipping_first_name.' '.$theorder->shipping_last_name,$theorder->shipping_address_1,$shipAddress2,'-',
  $theorder->shipping_postcode,'-',$theorder->billing_phone,substr($product_id,0,-1),substr($product_name,0,-1),$productWeight, $theorder->order_total,$totalCollectible,$vendorCode,$shipperName,$shipperAddress1,$shipperAddress2,$shipperAddress3,$shipperPin,
                     "20","20","20",$quant,$custCode,$dateTime[0],$dateTime[1]);
                } else if($theorder->payment_method==$_POST['rad']){
                          $finalarray[] = array($theorder->order_custom_fields['_tracking_number'][0],$payType,$theorder->id,$shipperName,$theorder->shipping_first_name.' '.$theorder->shipping_last_name,$theorder->shipping_address_1,$shipAddress2,'-',
  $theorder->shipping_postcode,'-',$theorder->billing_phone,substr($product_id,0,-1),substr($product_name,0,-1),$productWeight, $theorder->order_total,$totalCollectible,$vendorCode,$shipperName,$shipperAddress1,$shipperAddress2,$shipperAddress3,$shipperPin,
                     "20","20","20",$quant,$custCode,$dateTime[0],$dateTime[1]); 
                }
                
                
                
                
                     
            }
        //    include 'class/PHPExcel/IOFactory.php';

//$myFile = $myFile;
//echo get_include_path() . PATH_SEPARATOR . 'class/';
//$objReader = PHPExcel_IOFactory::createReader('CSV');
//$objPHPExcel = $objReader->load($finalarray);
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//echo $objWriter->save('MyExcelFile.xls');
   ob_clean();     
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
header("Content-type: application/x-msexcel");     
header("Content-Disposition: attachment; filename=MyExcelFile.xls");
header("Pragma: no-cache");
header("Expires: 0");
            
    $outputBuffer = fopen("php://output", 'w');
	foreach($finalarray as $val) {
	    fputcsv($outputBuffer, $val);
	}
	fclose($outputBuffer);        
      exit;  
 }
 
        
        /**
         * Create admin manifest page
         * @global type $woocommerce
         */

function eshopbox_picklist_page() {
    global $woocommerce;
    global $wpdb;

    $args = array(
             'post_type' => 'shop_order',
             'post_status' => 'publish',
            'posts_per_page' => -1  
    );

    $my_query=get_posts($args);
    $finalarray[]=array("Product name","color","size","quantity");
    foreach($my_query as $key=>$val){
      $abc = new WC_Order($val->ID);
    //  echo '<pre>';
     // print_r($abc);
      if($abc->status=='processing'){

       foreach($abc->get_items() as $key=>$item){
          $sku = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value  FROM $wpdb->postmeta WHERE meta_key='_sku' AND post_id='%d' LIMIT 1", $item['product_id'] ) ); 
           
          echo '<pre>';
          print_r($item);
           
         //  $pro[$item['name']][$item['pa_color']][$item['pa_size']] = $pro[$item['name']][$item['pa_color']][$item['pa_size']] + $item['qty']; 
$pro[$val->ID][$sku][$item['pa_color']] = $item['pa_color']; 
$pro[$val->ID][$sku][$item['pa_size']] = $pro[$sku][$item['pa_size']];
$pro[$val->ID][$sku]['odate'] = $abc->order_date;
       }
      }

    }

    echo '<pre>';
    print_r($pro);
    foreach($pro as $key=>$val){
       foreach($val as $key1=>$val1){
           foreach($val1 as $key2=>$val2){
             $finalarray[] = array($key,$key1,$key2,$val2); 
           }
       }
    }

if($_GET['d']=='true'){
    ob_clean();
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=picklist.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    $this->outputCSV($finalarray);
}
    ?>
    <div id="manifesttable">
        <table width="100%" cellspacing="0" cellpadding="0" class="widefat">
            <thead>
                <tr>
        <th style="padding:7px 7px 8px; "><?php if(count($finalarray)>1){  ?><a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=eshopbox-picklist&d=true">Download</a><?php } ?></th>            
        <th style="padding:7px 7px 8px; ">Name</th>
        <th style="padding:7px 7px 8px; ">Color</th>
        <th style=" padding:7px 7px 8px;">Size</th>
        <th style="padding:7px 7px 8px;">Quantity</th>
       </tr></thead>
            <tfoot>
                <tr>
                    <th style="padding:7px 7px 8px; "><?php if(count($finalarray)>1){  ?><a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=eshopbox-picklist&d=true">Download</a><?php }  ?></th>
                <th style="padding:7px 7px 8px; ">Name</th>
        <th style="padding:7px 7px 8px; ">Color</th>
        <th style=" padding:7px 7px 8px;">Size</th>
        <th style="padding:7px 7px 8px;">Quantity</th>
        </tr></tfoot>

    <tbody id="manifdetail">
        <?php
    if(count($finalarray)>1){  
        unset($finalarray[0]);
        foreach($finalarray as $key=>$value){
       echo  '<tr>
                <th style="padding:7px 7px 8px; ">'.$value[0].'</th>
        <th style="padding:7px 7px 8px; ">'.$value[1].'</th>
        <th style=" padding:7px 7px 8px;">'.$value[2].'</th>
        <th style="padding:7px 7px 8px;">'.$value[3].'</th>
        </tr>';
    }} else {
        echo "No processing order";
    }
        ?>
 </tbody>
    </table>
</div>
 <?php
}     

public function outputCSV($finalarray){
	$outputBuffer = fopen("php://output", 'w');
	foreach($finalarray as $val) {
	    fputcsv($outputBuffer, $val);
	}
	fclose($outputBuffer);
        exit;
}

/**
     * Get the plugin url.
     *
     * @access public
     * @return string
     */
    public function plugin_url() {
        if ( $this->plugin_url ) return $this->plugin_url;
        return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     *
     * @access public
     * @return string
     */
    public function plugin_path() {
        if ( $this->plugin_path ) return $this->plugin_path;
        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
  

}
new WC_updatepincodes();
