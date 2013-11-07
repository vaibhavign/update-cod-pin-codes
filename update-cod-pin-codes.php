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
			<h2><?php _e( 'Eshopbox Update COD pincode panel', 'wc-bluedart' ); ?></h2>
			<?php if ( isset( $_POST['pip_fields_submitted'] ) && $_POST['pip_fields_submitted'] == 'submitted' ) { ?>
			<div id="message" class="updated fade"><p><strong><?php _e( 'Your settings have been saved.', 'wc-bluedart' ); ?></strong></p></div>
			<?php } ?>
			<p><?php _e( 'Upload latest pincode xls file.', 'wc-bluedart' ); ?></p>
			<div id="content">
			  <form method="post" name="batchforma" id="batchforma" action="" enctype="multipart/form-data" >
				 
				  <div id="poststuff">
						<div class="postbox">
							<h3 class="hndle"><?php _e( 'Upload file', 'wc-bluedart' ); ?></h3>
							<div class="inside pip-preview">
							  <table class="form-table">
							    <tr>
    								<th>
    									<label for="eshopbox_bluedart_store_name"><b><?php _e( 'Upload file:', 'wc-bluedart' ); ?></b></label>
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
  
       //  echo '<pre>'; print_r($_FILES);
       //  print_r($_POST); exit;
   if(!isset($_POST['rad'])){die('Please select an option');}      
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
if($_POST['rad']=='aramex'){
// if sheet is of aramex
foreach($sheetData as $sheetdata){
	//$pincodes = trim($sheetdata['C']);  bluedart
//if(trim($sheetdata['N'])=='Yes'){  bluedart
	$pincodes = trim($sheetdata['A']);
        $cityname =  trim($sheetdata['C']); 
        $statename =  trim($sheetdata['E']);
if(trim($sheetdata['M'])=='Y'){
$pincode[$pincodes]= $pincodes ;
$pincodea[$pincodes]['city']= $cityname ;
$pincodea[$pincodes]['state']= $statename ; 
$pincodea[$pincodes]['cod']= 'Y' ;
//$pincode[$i] = $pincodes ;
//print_r($sheetdata);
$i++;
}
}

$querystr = "SELECT pincode FROM wp_cod_aramex";
$postid = $wpdb->get_results($querystr,ARRAY_A);
foreach($postid as $key=>$val){
    $dbpincode[$val['pincode']]=$val['pincode'];
}

$arrayToINsert = array_diff($pincode,$dbpincode);
$arrayToDelete = array_diff($dbpincode,$pincode);

//echo sizeof($arrayToINsert).'--'.sizeof($arrayToDelete);
$ax = 0;
foreach($arrayToDelete as $key=>$val){
  //  echo "delete FROM wp_cod_dtdc where pincode=".$val."<br>";
    $querystr = $wpdb->get_results("delete FROM wp_cod_aramex where pincode=".$val);
   $ax++; 
}

echo $ax." rows deleted </br>";

$bx = 0;
foreach($arrayToINsert as $key=>$val){
   //   echo "Insert FROM wp_cod_dtdc where pincode=".$val."<br>";
  //  $querystr = $wpdb->get_results("delete FROM wp_cod_dtdc where pincode=".$val);

    $wpdb->insert( 
	'wp_cod_aramex', 
	array( 
		'pincode' => $val,
                'city' => $pincodea[$val]['city'],
                'state'=>$pincodea[$val]['state'],
                'cod'=> $pincodea[$val]['cod']

	)
);
   $bx++;
}

echo $bx." rows added </br>";

//echo $i.'---';
//echo sizeof($pincode);
//echo 'test';
//echo '<pre>';
//print_r($pincode);
//print_r($arrayToDelete);
exit;
} else if($_POST['rad']=='bluedart'){
    
    // if sheet is of bluedart
foreach($sheetData as $sheetdata){
	$pincodes = trim($sheetdata['C']);  //bluedart
if(trim($sheetdata['N'])=='Yes'){  //bluedart
	//$pincodes = trim($sheetdata['A']);
        $cityname =  trim($sheetdata['D']); 
        $statename =  trim($sheetdata['F']);
//if(trim($sheetdata['M'])=='Y'){
$pincode[$pincodes]= $pincodes ;
$pincodea[$pincodes]['city']= $cityname ;
$pincodea[$pincodes]['state']= $statename ;
$pincodea[$pincodes]['cod']= 'Yes' ;
//$pincode[$i] = $pincodes ;
//print_r($sheetdata);
$i++;
}
}

$querystr = "SELECT pincode FROM wp_bluedart_codpins";
$postid = $wpdb->get_results($querystr,ARRAY_A);
foreach($postid as $key=>$val){
    $dbpincode[$val['pincode']]=$val['pincode'];
}

$arrayToINsert = array_diff($pincode,$dbpincode);
$arrayToDelete = array_diff($dbpincode,$pincode);

//echo sizeof($arrayToINsert).'--'.sizeof($arrayToDelete);
$ax = 0;
foreach($arrayToDelete as $key=>$val){
  //  echo "delete FROM wp_cod_dtdc where pincode=".$val."<br>";
    $querystr = $wpdb->get_results("delete FROM wp_bluedart_codpins where pincode=".$val);
   $ax++; 
}

echo $ax." rows deleted </br>";

$bx = 0;
foreach($arrayToINsert as $key=>$val){
   //   echo "Insert FROM wp_cod_dtdc where pincode=".$val."<br>";
  //  $querystr = $wpdb->get_results("delete FROM wp_cod_dtdc where pincode=".$val);

    $wpdb->insert( 
	'wp_bluedart_codpins', 
	array( 
		'pincode' => $val,
                'city' => $pincodea[$val]['city'],
                'state'=>$pincodea[$val]['state'],
                'cod'=> $pincodea[$val]['cod']

	)
);
   $bx++;
}

echo $bx." rows added </br>";

//echo $i.'---';
//echo sizeof($pincode);
//echo 'test';
//echo '<pre>';
//print_r($pincode);
//print_r($arrayToDelete);
exit;
}
//print_r($xyz);

//	}
	//echo '<pre>';
	//print_r($_FILES);
         
     }

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
