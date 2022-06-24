<?php
session_start();

include("includes/connect.inc.php");
//$user_id = $_SESSION['user_id']
if (!isset($_SESSION['user_id']))
    header("location:index.php");

$user_id = $_SESSION['user_id'];
$sql = "SELECT full_name, employee_code FROM tbl_users WHERE user_id=".$user_id;
$rslt_user = $mysqli->query($sql);
$row_user = $rslt_user->fetch_assoc();
$user_id = $_SESSION['user_id'];

$arrEducation = array("1"=>"Less than Secondary",
	 "2"=>"Secondary",
	"3" => "Higher Secondary",
	"4" => "Diploma",
	"5" => "Graduation",
    "6" => "Engineering Graduation",
	"7" => "Post Graduation",
	"8" => "EngineeringPost Graduation",
	"9" => "PhD");

	$div_result = $mysqli->query("SELECT * FROM  tbl_dimensions where (dimension_type=2 OR dimension_type=3) and is_active=1");
	$country_result = $mysqli->query("SELECT * FROM  tbl_country ");

	$sql= "SELECT * FROM tbl_spouse_data WHERE user_id=".$user_id;
	$result_data = $mysqli->query($sql);
	$row_data = $result_data->fetch_assoc();
	$married = $row_data["married"];
	$allowance_objection = $row_data["allowance_objection"];
	$spouse_name = $row_data["spouse_name"];
	//$marriage_date = $row_data["marriage_date"];
	$marriage_date = (!empty($row_data['marriage_date']) && $row_data['marriage_date']!=='0000-00-00')?date('d-m-Y',strtotime($row_data['marriage_date'])):"";
	$education = $row_data["education"];
	$specialization = $row_data["specialization"];
	$current_work_status = $row_data["current_work_status"];
	$current_in_aries = $row_data["current_in_aries"];
	$curr_department = $row_data["curr_department"];
	$curr_company = $row_data["curr_company"];
	$curr_location = $row_data["curr_location"];
	$prev_work_status = $row_data["prev_work_status"];
	$leave_job_reason = $row_data["leave_job_reason"];
	$prev_in_aries = $row_data["prev_in_aries"];
	$prev_department = $row_data["prev_department"];
	$prev_company = $row_data["prev_company"];
	$prev_location = $row_data["prev_location"];
	$current_holding_account = $row_data["current_holding_account"];
	$current_living_status = $row_data["current_living_status"];
	$account_name = $row_data["account_name"];
	$account_number = $row_data["account_number"];
	$account_type = $row_data["account_type"];
	$bank_name = $row_data["bank_name"];
	$branch_code = $row_data["branch_code"];
	$ifsc_code = $row_data["ifsc_code"];
	$swift_code = $row_data["swift_code"];
	$iban_no = $row_data["iban_no"];
	$country_id = $row_data["country_id"];
	$marriage_cetificate = $row_data["marriage_cetificate"];
	$educational_certificate = $row_data["educational_certificate"];
	$objection_remarks = $row_data["objection_remarks"];


	
############################################################### SAVE DATA ########################################
/*function for saving  vaccination data*/
if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='DOWNLOAD') {
    $user_id =isset($_REQUEST['user_id'])?$_REQUEST['user_id']:$user_id;
	$sql_sel = " SELECT * FROM tbl_spouse_data WHERE user_id=".$user_id;
	$rslt = $mysqli->query($sql_sel);
	$row_data = $rslt->fetch_assoc();
	if($_REQUEST["file"]==1)
	$filename = "spouse_data/".$row_data['marriage_cetificate'];
	else $filename = "spouse_data/".$row_data['educational_certificate'];

	$known_mime_types = array(
        "pdf" => "application/pdf",
        "txt" => "text/plain",
        "html" => "text/html",
        "htm" => "text/html",
        "exe" => "application/octet-stream",
        "zip" => "application/zip",
        "doc" => "application/msword",
        "DOC" => "application/msword",
        "docx" => "application/msword",
        "DOCX" => "application/msword",
        "xls" => "application/vnd.ms-excel",
        "ppt" => "application/vnd.ms-powerpoint",
        "gif" => "image/gif",
        "png" => "image/png",
        "jpeg" => "image/jpg",
        "jpg" => "image/jpg",
        "JPG" => "image/jpg",
        "php" => "text/plain"
    );





     $file = $dir.$doc_file;
    $path_parts = pathinfo($filename);
//print_r($path_parts);
    $extn = $path_parts['extension'];
//exit;

	ob_end_clean();
	$contenttype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
	header('Content-Description: File Transfer');
    header("Content-Type: ".$known_mime_types[$extn]."");
	header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
	readfile("your file uploaded path".$filename);
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	readfile($filename);
	exit;
}
if(isset($_REQUEST['submit_x'])) {
	$sql_sel = " SELECT id FROM tbl_spouse_data WHERE user_id=".$user_id;
	$rslt = $mysqli->query($sql_sel);
	$row_data = $rslt->fetch_assoc();
	$msg='N';
	if(!empty($row_data['id'])) {
		if(!empty($_REQUEST['marriage_date'])) {
			$arrDt1 = explode("-",$_REQUEST['marriage_date']);
			$_REQUEST['marriage_date'] = $arrDt1[2]."-".$arrDt1[1]."-".$arrDt1[0];
		}
		

		 $sql_upd = "UPDATE tbl_spouse_data
		SET married='".addslashes($_REQUEST['married'])."',
		allowance_objection='".addslashes($_REQUEST['allowance_objection'])."',
		spouse_name='".addslashes($_REQUEST['spouse_name'])."',
		marriage_date='".addslashes($_REQUEST['marriage_date'])."',
		education = '".addslashes($_REQUEST['education'])."',
		specialization = '".addslashes($_REQUEST['specialization'])."',
		current_work_status = '".addslashes($_REQUEST['current_work_status'])."',
		current_in_aries = '".addslashes($_REQUEST['current_in_aries'])."',
		curr_department = '".addslashes($_REQUEST['curr_department'])."',
		curr_company = '".addslashes($_REQUEST['curr_company'])."',
		curr_location = '".addslashes($_REQUEST['curr_location'])."',
		prev_work_status = '".addslashes($_REQUEST['prev_work_status'])."',
		leave_job_reason = '".addslashes($_REQUEST['leave_job_reason'])."',
		prev_in_aries = '".addslashes($_REQUEST['prev_in_aries'])."',
		prev_department = '".addslashes($_REQUEST['prev_department'])."',
		prev_company = '".addslashes($_REQUEST['prev_company'])."',
		prev_location = '".addslashes($_REQUEST['prev_location'])."',
		current_living_status = '".addslashes($_REQUEST['current_living_status'])."',
		current_holding_account = '".addslashes($_REQUEST['current_holding_account'])."',
		account_name = '".addslashes($_REQUEST['account_name'])."',
		current_living_status = '".addslashes($_REQUEST['current_living_status'])."',
		current_holding_account = '".addslashes($_REQUEST['current_holding_account'])."',
		account_name = '".addslashes($_REQUEST['account_name'])."',
		account_number = '".addslashes($_REQUEST['account_number'])."',
		account_type = '".addslashes($_REQUEST['account_type'])."',
		bank_name = '".addslashes($_REQUEST['bank_name'])."',
		branch_code = '".addslashes($_REQUEST['branch_code'])."',
		ifsc_code = '".addslashes($_REQUEST['ifsc_code'])."',
		swift_code = '".addslashes($_REQUEST['swift_code'])."',
		iban_no = '".addslashes($_REQUEST['iban_no'])."',
		country_id = '".addslashes($_REQUEST['country_id'])."',
		objection_remarks = '".addslashes($_REQUEST['objection_remarks'])."',
		updated_at = '".gmdate('Y-m-d H:i:s')."'
		WHERE user_id=".$user_id."";
		$mysqli->query($sql_upd);
		
		if(!empty($_FILES['marriage_cetificate']['name'])) {
			$extn = substr($_FILES['marriage_cetificate']['name'],strpos($_FILES['marriage_cetificate']['name'],'.'));
			$file = substr($_FILES['marriage_cetificate']['name'],0,strpos($_FILES['marriage_cetificate']['name'],'.'));
			$filename = "married_".$user_id."_".$file.$extn;
			$original_file = $_FILES['marriage_cetificate'.$i]['tmp_name'];
			$filepath = 'spouse_data/'.$filename;
			if(copy($original_file,$filepath)) {
				$sql_upd = "
				UPDATE tbl_spouse_data
				SET marriage_cetificate='".$filename."'
				WHERE user_id=".$user_id."";
				$mysqli->query($sql_upd);
			
			}
		}
		if(!empty($_FILES['educational_certificate']['name'])) {
			$extn = substr($_FILES['educational_certificate']['name'],strpos($_FILES['educational_certificate']['name'],'.'));
			$file = substr($_FILES['educational_certificate']['name'],0,strpos($_FILES['educational_certificate']['name'],'.'));
			$filename = "education_".$user_id."_".$file.$extn;
			$original_file = $_FILES['educational_certificate'.$i]['tmp_name'];
			$filepath = 'spouse_data/'.$filename;
			if(copy($original_file,$filepath)) {
				$sql_upd = "
				UPDATE tbl_spouse_data
				SET educational_certificate='".$filename."'
				WHERE user_id=".$user_id."";
				$mysqli->query($sql_upd);
			
			}
		}
		$msg='Y';
	}
	else {
		
		if(!empty($_REQUEST['marriage_date'])) {
			$arrDt1 = explode("-",$_REQUEST['marriage_date']);
			$_REQUEST['marriage_date'] = $arrDt1[2]."-".$arrDt1[1]."-".$arrDt1[0];
		}


		 $sql_ins = "INSERT INTO tbl_spouse_data(user_id,married,allowance_objection,spouse_name,marriage_date,education,specialization,
		current_work_status,current_in_aries,curr_department,curr_company,curr_location,prev_work_status,leave_job_reason,prev_in_aries,prev_department,prev_company,prev_location,current_living_status,
		current_holding_account,account_name,account_number,account_type,bank_name,branch_code,ifsc_code,swift_code,iban_no,country_id,objection_remarks,added_date,updated_at)
		VALUES('".$user_id."',
		'".addslashes($_REQUEST['married'])."',
		'".addslashes($_REQUEST['allowance_objection'])."',
		'".addslashes($_REQUEST['spouse_name'])."',
		'".addslashes($_REQUEST['marriage_date'])."',
		'".addslashes($_REQUEST['education'])."',
		'".addslashes($_REQUEST['specialization'])."',
		'".addslashes($_REQUEST['current_work_status'])."',
		'".addslashes($_REQUEST['current_in_aries'])."',
		'".addslashes($_REQUEST['curr_department'])."',
		'".addslashes($_REQUEST['curr_company'])."',
		'".addslashes($_REQUEST['curr_location'])."',
		'".addslashes($_REQUEST['prev_work_status'])."',
		'".addslashes($_REQUEST['leave_job_reason'])."',
		'".addslashes($_REQUEST['prev_in_aries'])."',
		'".addslashes($_REQUEST['prev_department'])."',
		'".addslashes($_REQUEST['prev_company'])."',
		'".addslashes($_REQUEST['prev_location'])."',
		'".addslashes($_REQUEST['current_living_status'])."',
		'".addslashes($_REQUEST['current_holding_account'])."',
		'".addslashes($_REQUEST['account_name'])."',
		'".addslashes($_REQUEST['account_number'])."',
		'".addslashes($_REQUEST['account_type'])."',
		'".addslashes($_REQUEST['bank_name'])."',
		'".addslashes($_REQUEST['branch_code'])."',
		'".addslashes($_REQUEST['ifsc_code'])."',
		'".addslashes($_REQUEST['swift_code'])."',
		'".addslashes($_REQUEST['iban_no'])."',
		'".addslashes($_REQUEST['country_id'])."',
		'".addslashes($_REQUEST['objection_remarks'])."',
		'".gmdate('Y-m-d H:i:s')."',
		'".gmdate('Y-m-d H:i:s')."'
		)";
		$mysqli->query($sql_ins);
		//exit;
		$vaccine_id = $mysqli->insert_id;
		if(!empty($_FILES['marriage_cetificate']['name'])) {
			$extn = substr($_FILES['marriage_cetificate']['name'],strpos($_FILES['marriage_cetificate']['name'],'.'));
			$file = substr($_FILES['marriage_cetificate']['name'],0,strpos($_FILES['marriage_cetificate']['name'],'.'));
			$filename = "married_".$user_id."_".$file.$extn;
			$original_file = $_FILES['marriage_cetificate'.$i]['tmp_name'];
			$filepath = 'spouse_data/'.$filename;
			if(copy($original_file,$filepath)) {
				$sql_upd = "
				UPDATE tbl_spouse_data
				SET marriage_cetificate='".$filename."'
				WHERE user_id=".$user_id."";
				$mysqli->query($sql_upd);
			
			}
		}
		if(!empty($_FILES['educational_certificate']['name'])) {
			$extn = substr($_FILES['educational_certificate']['name'],strpos($_FILES['educational_certificate']['name'],'.'));
			$file = substr($_FILES['educational_certificate']['name'],0,strpos($_FILES['educational_certificate']['name'],'.'));
			$filename = "education_".$user_id."_".$file.$extn;
			$original_file = $_FILES['educational_certificate'.$i]['tmp_name'];
			$filepath = 'spouse_data/'.$filename;
			if(copy($original_file,$filepath)) {
				$sql_upd = "
				UPDATE tbl_spouse_data
				SET educational_certificate='".$filename."'
				WHERE user_id=".$user_id."";
				$mysqli->query($sql_upd);
			
			}
		}
		$msg='Y';
		$_SESSION['spouse_data']=0;
	}
	header('location:spouse_data.php?msg='.$msg);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

    <head>

        <link rel="icon" href="images/favicon.ico" type="image/x-icon">

        <title>Online Job Diary - Aries Marine</title>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 <!--<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>-->
        <script type="text/javascript" src="javascript/jquery-1.10.2.min.js"></script>
        <link href="css/style.css" rel="stylesheet" type="text/css">
        <link href="css/calendarstyle.css" rel="stylesheet" type="text/css">
        
     
        
        <script type="text/javascript" src="javascript/calendar.js"></script>

        <script type="text/javascript" src="javascript/date-functions.js"></script>

        <!--<script type="text/javascript" src="javascript/js.js"></script>-->
<link rel="stylesheet" media="screen" href="css/screen.css">


    </head>

<?php if ($_POST['go_x']) {
    $args = $_POST['select_division'];
} else if ($_POST['delete_x']) {
    $args = $_POST['hide_divisoin'];
} else $args = '0'; ?>

<style>
	.block {
		display: block;
	}
	form.cmxform label.error {
		display: none;
	}
        select.error, textarea.error, input.error {
    color:#FF0000;
}
/** This is the style of our error messages */
.error {
  width  : 100%;
  padding: 0;

  font-size: 80%;
  color: red;
  box-sizing: border-box;
  text-align:left;
 
}

.error.active {
  padding: 0.3em;
}
/* This is our style for the invalid fields */
input:invalid:required{
  border-color: #900;
  background-color: #FDD;
}


label {
    /* Other styling... */
    text-align: right;
    clear: both;
    margin-right:15px;
}	</style>

    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">

<?php
include("includes/header.php");



$num = 1;
?>

            <tr>

                <td>



                    <form method="post" name="firstform" id="firstform" enctype="multipart/form-data">	



                        <table style="border-collapse: collapse;" width="90%" align="center" border="0" cellpadding="0" cellspacing="0">

                            <tr>

                                <td align="center"><br><br><font size="+1" color="#a6107b"><b>Spouse Data <?php echo get_username($employee_id) ?></b></font></td>

                            </tr>

                            <tr>

                                <td align="center"><div style="display: block;"><font color="#ff0000"><b></b></font></div></td>

                            </tr>
                        </table>
							<?php
							if($_REQUEST['msg']=='Y') {
								?><tr>

                                <td align="center"><center><br><br><font  color="green" style="font-size:20"><b>Thank you for updating the details.</center></b></font></td>

                            </tr><?php
							}
							else {
							?>

                        
                        <table style="border-collapse: collapse;" width="80%" align="center" border="0" cellpadding="0" cellspacing="0">

                            <tbody>

                                <tr>

                                    <td colspan="3" ><b><font size="2" color="#0000ff" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></b></td>

                                </tr>

                                <tr style="border: 1px solid rgb(0, 0, 0);" bgcolor="#e2f3fd" height="25">

                                    <td width="100%" style="border: 1px solid rgb(0, 0, 0);" align="center" colspan="2"><strong><font size="2">Name:<b style='color:red;'><?php print($row_user['full_name'])?></b>&nbsp;&nbsp;Employee Code:<b style='color:red;'><?php print($row_user['employee_code'])?></b></font></strong></td>
                               
                                </tr>
								<tr style="border: 1px solid rgb(166, 16, 123);" height="35">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Are you married?</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="radio" name="married" id="marriedY" value="Y"  <?php if($married=='Y') { ?>checked<?php }?>>Yes
                                                    <input  type="radio" name="married" id="marriedN"  value="N" <?php if($married=='N') { ?>checked<?php }?>>No
                                        </b></font>
                                     <label class="error" id="Errmarried" style="display: none;">Please select one of these options.</label>
									</td>
								  </tr>
								   <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($married) && $married=='Y') { ?> <?php }else { ?>display:none;<?php } ?>;" height="35" id="objection">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Do you have any objection for issuing allowance to wife?</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="radio" name="allowance_objection" id="objectionY" value="Y"  <?php if($allowance_objection=='Y') { ?>checked<?php }?>>Yes
                                                    <input  type="radio" name="allowance_objection" id="objectionN"  value="N" <?php if($allowance_objection=='N') { ?>checked<?php }?>>No
                                        </b></font>
                                     <label class="error" id="Errobjection" style="display: none;">Please select one of these options.</label>
									</td>
								  </tr>

								  <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($allowance_objection) && $allowance_objection=='N') { ?> <?php }else { ?>display:none;<?php } ?>" height="35" id="wife_name">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Wife Name:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="spouse_name" id="spouse_name" value="<?php print($spouse_name);?>"  size="50"/>                                        </b></font>
                                     <label class="error" id="Errwife_name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  
								  <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($allowance_objection) && $allowance_objection=='N') {?> <?php }else { ?> display:none; <?php } ?>" height="35" id="divmarriage_date">
									<td style="border: 1px solid rgb(0, 0, 0);padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Marriage Date:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="marriage_date"  id="marriage_date" value="<?php print$marriage_date;?>" onClick="return showCalendar('marriage_date', 'dd-mm-y');" placeholder='dd-mm-yyyy'/> <img title="Calendar" style="vertical-align:middle;" src="images/calendar0.gif" onClick="return showCalendar('marriage_date', 'dd-mm-y');" border="0"/>
                                     <label class="error" id="Errmarriage_date" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N') {?> <?php }else { ?> display:none; <?php } ?>" height="35" id="edu_qualification">
									<td style="border: 1px solid rgb(0, 0, 0);padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Educational Qualification:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <select   name="education" id="education"    >  
														<option value="" >Select Qualification</option><?
														foreach($arrEducation as $key => $educationq) {
														  ?><option value="<?php print($key);?>" <?php if($education==$key) { ?>selected<?php } ?> ><?php print($educationq);?></option><?php
														}
													 ?></select>
													</b></font>
                                     <label class="error" id="Erredu_qualification" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N') {?> <?php }else { ?> display:none; <?php } ?>" height="35" id="quali_specification">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Specialized / Main subject:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="specialization" id="specialization" value="<?php print($specialization);?>"  size="50"/>                                        </b></font>
                                     <label class="error" id="Errquali_specification" style="display: none;">This field is required.</label>
									</td>
								  </tr>

								  
								<tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($allowance_objection) && $allowance_objection=='N') {?> <?php }else { ?> display:none; <?php } ?>" height="35" id="wife_employeed">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Is your wife working currently?:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="radio" name="current_work_status" id="employeedY" value="Y" <?php if($current_work_status=='Y') {?> checked <?php } ?>>Yes
                                                    <input  type="radio" name="current_work_status" id="employeedN"  value="N" <?php if($current_work_status=='N') {?> checked <?php } ?>>No
                                        </b></font>

										<span style="<?php if(!empty($current_work_status) && $current_work_status=='Y') {?>  <?php } else { ?> display:none <?php } ?>" id="working_aries">
										<font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Is your wife being employed in Aries?:</b></font>
										
										<font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="radio" name="current_in_aries" id="work_ariesY" value="Y" <?php if($current_in_aries=='Y') {?> checked <?php } ?>>Yes
                                                    <input  type="radio" name="current_in_aries" id="work_ariesN"  value="N" <?php if($current_in_aries=='N') {?> checked <?php } ?>>No
                                        </b></font>

										</span> 
										
										<span style="padding-left:20px;<?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" id="prev_employeed">
										<font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Was previously employed?:</b></font>
										<font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="radio" name="prev_work_status" id="prev_employeedY" value="Y" <?php if($prev_work_status=='Y') {?> checked <?php } ?>>Yes
                                                    <input  type="radio" name="prev_work_status" id="prev_employeedN"  value="N" <?php if($prev_work_status=='N') {?> checked <?php } ?>>No
                                        </b></font>
                                     
										</span>
                                     <label class="error" id="Errwife_employeed" style="display: none;">Please select any one of these options.</label>
									</td>
								  </tr>

								  <tr style="border: 1px solid rgb(166, 16, 123);display:none;" height="35" >
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <label class="error" for="Married" style="display: none;">This field is required.</label>
									</td>
								  </tr>

								  <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($current_in_aries) && $current_in_aries=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id='aries_department'>
									<td style="border: 1px solid rgb(0, 0, 0);padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Department:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <select   name="curr_department" id="curr_department"    >  
														<option value="" >Select</option><?php
														while($row_dep = $div_result->fetch_assoc()) {
															?><option value="<?php print($row_dep['id']);?>" <?php if($curr_department==$row_dep['id'])  { ?> selected <?php } ?>><?php print($row_dep['short_name']);?></option><?php
														}
													 ?></select>
													</b></font>
                                     <label class="error" id="Erraries_department" style="display: none;">Please select department.</label>
									</td>
								  </tr>

									<tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($current_in_aries) && $current_in_aries=='N'  && $current_work_status=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="other_company">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Company Name:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="curr_company" id="curr_company" value="<?php print($curr_company);?>"  size="50"/>                                        </b></font>
									<span> <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Location:<input  type="text" name="curr_location" id="curr_location" value="<?php print($curr_location);?>"  size="20"/></b></font></span>
                                     <label class="error" id="Errother_company" style="display: none;">This field is required.</label>
									</td>
								  </tr>

								  
								  <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($prev_work_status) && $prev_work_status=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="reason">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Specify the reason for leaving the job:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <textarea  type="text" name="leave_job_reason" id="leave_job_reason"   cols="50" rows="5"><?php print$leave_job_reason;?></textarea>                                        </b></font>
                                     <label class="error" for="Errleave_job_reason" style="display: none;">This field is required.</label>
									</td>
								  </tr>

								  <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($prev_work_status) && $prev_work_status=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="was_working_aries">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Was wife working in aries?</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="radio" name="prev_in_aries" id="was_ariesY" value="Y" <?php if($prev_in_aries=='Y') {?> checked <?php } ?>>Yes
                                                    <input  type="radio" name="prev_in_aries" id="was_ariesN"  value="N" <?php if($prev_in_aries=='N') {?> checked <?php } ?>>No
                                        </b></font>
                                     <label class="error" id="Errwas_working_aries" style="display: none;">This field is required.</label>
									</td>
								  </tr>

								  <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($prev_in_aries) && $prev_in_aries=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id='was_aries_department'>
									<td style="border: 1px solid rgb(0, 0, 0);padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Department:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <select   name="prev_department" id="prev_department"    >  
														<option value="" >Select Department</option><?php
														$div_result->data_seek(0);
														while($row_dep = $div_result->fetch_assoc()) {
															?><option value="<?php print($row_dep['id']);?>" <?php if($prev_department==$row_dep['id']) { ?>selected<?php } ?>><?php print($row_dep['short_name']);?></option><?php
														}
													 ?>
													 </select>
													</b></font>
                                     <label class="error" id="Errwas_aries_department" style="display: none;">This field is required.</label>
									</td>
								  </tr>

									<tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($prev_in_aries) && $prev_in_aries=='N' && $prev_work_status=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="was_other_company">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Company Name:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="prev_company" id="prev_company" value="<?php print($prev_company);?>"  size="50"/></b></font>
													<span> <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Location:<input  type="text" name="prev_location" id="prev_location" value="<?php print($prev_location);?>"  size="20"/></b></font></span>
                                   
                                     <label class="error" id="Errwas_other_company" style="display: none;">This field is required.</label>
									</td>
								  </tr>


								
							<tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='Y') { ?> <?php }else { ?>display:none;<?php } ?>" height="35" id="reason_objection">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Specify the reason for objection:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <textarea  type="text" name="objection_remarks" id="objection_remarks"  cols="50" rows="5"><?php print($objection_remarks) ;?></textarea>                                        </b></font>
                                     <label class="error" id="Errreason_objection" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								<tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id='wife_stay'>
									<td style="border: 1px solid rgb(0, 0, 0);padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Is your Wife currently living in local or overseas:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									<span>
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <select   name="current_living_status" id="current_living_status"    >  
														<option value="" >Select</option>
														<option value="L" <?php if($current_living_status=='L') { ?>selected<?php } ?>>Local</option>
														<option value="O" <?php if($current_living_status=='O') { ?>selected<?php } ?>>Overseas</option>
													 </select>
													</b></font>
													</span>
                                     <span style="padding-left:20px;"> <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                   Bank Account in Overseas / Local:<select   name="current_holding_account" id="current_holding_account"    >  
														<option value="" >Select</option>
														<option value="L" <?php if($current_holding_account=='L') { ?>selected<?php } ?>>Local</option>
														<option value="O" <?php if($current_holding_account=='O') { ?>selected<?php } ?>>Overseas</option>
														<option value="N" <?php if($current_holding_account=='N') { ?>selected<?php } ?>>No Bank Account</option>
													 </select></b></font></span>
									 <label class="error" id="Errliving" style="display: none;">This field is required.</label>
									  <label class="error" id="Errholdingaccount" style="display: none;">This field is required.</label>
									</td> 
								  </tr>
								
								<tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details10">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="center" bgcolor="#ffcccc" colspan="2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>BANK DETAILS</b></font></td>
								  </tr>
								<tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details1">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Account Name:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="account_name" id="account_name" value="<?php print($account_name);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>

<tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details2">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Account Number:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="account_number" id="account_number" value="<?php print($account_number);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details3">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Account type:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="account_type" id="account_type" value="<?php print($account_type);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  <tr style="border: 1px solid rgb(166, 16, 123);<?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details4">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Bank Name:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="bank_name" id="bank_name" value="<?php print($bank_name);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details5">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Branch Code:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="branch_code" id="branch_code" value="<?php print($branch_code);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details6">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>IFSC Code:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="ifsc_code" id="ifsc_code" value="<?php print($ifsc_code);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details7">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>SWIFT Code</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="swift_code" id="swift_code" value="<?php print($swift_code);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details8">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>IBAN NO:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="text" name="iban_no" id="iban_no" value="<?php print($iban_no);?>"  size="50"/></b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($allowance_objection) && $allowance_objection=='N' && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details9">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#a3c2c2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Country:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#a3c2c2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                   <select   name="country_id" id="country_id"    >  
														<option value="" >Select Country</option><?php
														while($row_country = $country_result->fetch_assoc()) {
															?><option value="<?php print($row_country['id']);?>" <?php if($country_id==$row_country['id']) { ?>selected<?php } ?>><?php print($row_country['name']);?></option><?php
														}
													 ?>
													 </select>
													
													</b></font>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>
								  
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($married) && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="acc_details11">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="center" bgcolor="#ffcccc" colspan="2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>ATTACHMENTS</b></font></td>
								  </tr>
								  <tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($married) && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="m_certificate">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Marriage Certificate:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="file" name="marriage_cetificate" id="marriage_cetificate" value=""  size="50"/></b></font>
													<?php if(!empty($marriage_cetificate)) { ?><a style="float: left; " href="spouse_data.php?doAction=DOWNLOAD&file=1">download</a><input type="hidden" name="vh_attact1" id="vh_attact1" value="Y" /><?php }
									  else {?> <input type="hidden" name="vh_attact1" id="vh_attact1" value="N" /> <?php } ?>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>

								<tr style="border: 1px solid rgb(166, 16, 123); <?php if(!empty($married) && $married=='Y') {?>  <?php } else { ?> display:none <?php } ?>" height="35" id="ed_certificate">
									<td style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;" align="left" bgcolor="#f2f2f2"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>Educational Qualification Certificate:</b></font></td>
									<td align="left" style="border: 1px solid rgb(0, 0, 0); padding-left: 5px;"   bgcolor="#f2f2f2">
									 <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                    <input  type="file" name="educational_certificate" id="educational_certificate" value=""  size="50"/></b></font>
													
													<?php if(!empty($educational_certificate)) { ?><a style="float: left; " href="spouse_data.php?doAction=DOWNLOAD&file=2">download</a><input type="hidden" name="vh_attact1" id="vh_attact1" value="Y" /><?php } ?>
                                     <label class="error" for="name" style="display: none;">This field is required.</label>
									</td>
								  </tr>

                                <tr style="border: 1px solid rgb(166, 16, 123);" height="35">

                                    
                                    <td style="border: 1px solid rgb(0, 0, 0);padding-left: 5px; "  colspan="2" bgcolor="#f2f2f2" align="center">
                                        
                                        <input type="submit" name="submit_x" value="submit"  class='jobdiary_buttons '></td>

                                </tr>



                            </tbody>

                        </table>

                    </form><?php
							}

                ?></td>

            </tr>

        </table></body></html>



<?php

function get_username($user_id) {

    global $mysqli;

    if ($user_id > 0) {

        $assigned_query = $mysqli->query("select username from tbl_users where user_id=$user_id");

        $username = $assigned_query->fetch_assoc();
    }

    return strtoupper($username['username']);
}
?>
<script>
$(document).ready(function() {

	$('#marriedY').click(function(){
		if($(this).val()=='Y') {
			document.getElementById('wife_name').style.display="none";
			document.getElementById('divmarriage_date').style.display="none";
			document.getElementById('edu_qualification').style.display="none";
			document.getElementById('quali_specification').style.display="none";
			document.getElementById('wife_employeed').style.display="none";
			document.getElementById('m_certificate').style.display="";
			document.getElementById('ed_certificate').style.display="none";
			document.getElementById('prev_employeed').style.display="none";
			
			document.getElementById('wife_name').style.display="none";
			document.getElementById('divmarriage_date').style.display="none";
			document.getElementById('edu_qualification').style.display="none";
			document.getElementById('quali_specification').style.display="none";
			document.getElementById('wife_employeed').style.display="none";
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="none";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="none";
			document.getElementById('acc_details1').style.display="none";
			document.getElementById('acc_details2').style.display="none";
			document.getElementById('acc_details3').style.display="none";
			document.getElementById('acc_details4').style.display="none";
			document.getElementById('acc_details5').style.display="none";
			document.getElementById('acc_details6').style.display="none";
			document.getElementById('acc_details7').style.display="none";
			document.getElementById('acc_details8').style.display="none";
			document.getElementById('acc_details9').style.display="none";
			document.getElementById('acc_details10').style.display="none";
			document.getElementById('objection').style.display="";
			
			document.getElementById('acc_details11').style.display="";
			document.getElementById('m_certificate').style.display="";
			document.getElementById('ed_certificate').style.display="none";
		}
		else {
		//	alert('');
			document.getElementById('wife_name').style.display="none";
			document.getElementById('divmarriage_date').style.display="none";
			document.getElementById('edu_qualification').style.display="none";
			document.getElementById('quali_specification').style.display="none";
			document.getElementById('wife_employeed').style.display="none";
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="none";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="none";
			document.getElementById('acc_details1').style.display="none";
			document.getElementById('acc_details2').style.display="none";
			document.getElementById('acc_details3').style.display="none";
			document.getElementById('acc_details4').style.display="none";
			document.getElementById('acc_details5').style.display="none";
			document.getElementById('acc_details6').style.display="none";
			document.getElementById('acc_details7').style.display="none";
			document.getElementById('acc_details8').style.display="none";
			document.getElementById('acc_details9').style.display="none";
			document.getElementById('acc_details10').style.display="none";
		}
	});

	
	$('#marriedN').click(function(){
			document.getElementById('wife_name').style.display="none";
			document.getElementById('divmarriage_date').style.display="none";
			document.getElementById('edu_qualification').style.display="none";
			document.getElementById('quali_specification').style.display="none";
			document.getElementById('wife_employeed').style.display="none";
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="none";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="none";
			document.getElementById('acc_details1').style.display="none";
			document.getElementById('acc_details2').style.display="none";
			document.getElementById('acc_details3').style.display="none";
			document.getElementById('acc_details4').style.display="none";
			document.getElementById('acc_details5').style.display="none";
			document.getElementById('acc_details6').style.display="none";
			document.getElementById('acc_details7').style.display="none";
			document.getElementById('acc_details8').style.display="none";
			document.getElementById('acc_details9').style.display="none";
			document.getElementById('acc_details10').style.display="none";
			document.getElementById('acc_details11').style.display="none";
			document.getElementById('m_certificate').style.display="none";
			document.getElementById('ed_certificate').style.display="none";
			
		
	});



	
	$('#employeedY').click(function(){
			document.getElementById('working_aries').style.display="";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="none";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="none";
			document.getElementById('acc_details1').style.display="none";
			document.getElementById('acc_details2').style.display="none";
			document.getElementById('acc_details3').style.display="none";
			document.getElementById('acc_details4').style.display="none";
			document.getElementById('acc_details5').style.display="none";
			document.getElementById('acc_details6').style.display="none";
			document.getElementById('acc_details7').style.display="none";
			document.getElementById('acc_details8').style.display="none";
			document.getElementById('acc_details9').style.display="none";
			document.getElementById('acc_details10').style.display="none";
			document.getElementById('work_ariesY').checked=false;
			document.getElementById('work_ariesN').checked=false;

		
	});
	$('#work_ariesY').click(function(){
			document.getElementById('working_aries').style.display="";
			document.getElementById('aries_department').style.display="";
			
			document.getElementById('other_company').style.display="none";
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="none";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="";
			document.getElementById('acc_details1').style.display="";
			document.getElementById('acc_details2').style.display="";
			document.getElementById('acc_details3').style.display="";
			document.getElementById('acc_details4').style.display="";
			document.getElementById('acc_details5').style.display="";
			document.getElementById('acc_details6').style.display="";
			document.getElementById('acc_details7').style.display="";
			document.getElementById('acc_details8').style.display="";
			document.getElementById('acc_details9').style.display="";
			document.getElementById('acc_details10').style.display="";
	});
	
	$('#work_ariesN').click(function(){
			document.getElementById('working_aries').style.display="";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('other_company').style.display="";
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="none";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="";
			document.getElementById('acc_details1').style.display="";
			document.getElementById('acc_details2').style.display="";
			document.getElementById('acc_details3').style.display="";
			document.getElementById('acc_details4').style.display="";
			document.getElementById('acc_details5').style.display="";
			document.getElementById('acc_details6').style.display="";
			document.getElementById('acc_details7').style.display="";
			document.getElementById('acc_details8').style.display="";
			document.getElementById('acc_details9').style.display="";
			document.getElementById('acc_details10').style.display="";
		
	});
	$('#prev_employeedY').click(function(){
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('other_company').style.display="none";
			document.getElementById('prev_employeed').style.display="";
			document.getElementById('reason').style.display="";
			document.getElementById('was_working_aries').style.display="";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			
			document.getElementById('acc_details11').style.display="";
			
			document.getElementById('m_certificate').style.display="";
			document.getElementById('ed_certificate').style.display="";
		
	});
	$('#was_ariesY').click(function(){
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('other_company').style.display="none";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('prev_employeed').style.display="";
			document.getElementById('reason').style.display="";
			document.getElementById('was_working_aries').style.display="";
			document.getElementById('was_aries_department').style.display="";
		
	});
	
	$('#was_ariesN').click(function(){
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('other_company').style.display="none";
			document.getElementById('was_other_company').style.display="";
			document.getElementById('prev_employeed').style.display="";
			document.getElementById('reason').style.display="";
			document.getElementById('was_working_aries').style.display="";
			document.getElementById('reason_objection').style.display="none";
			
			document.getElementById('was_aries_department').style.display="none";
		
		
	});
	
	
	$('#prev_employeedN').click(function(){
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			document.getElementById('other_company').style.display="none";
			document.getElementById('prev_employeed').style.display="";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="";
			document.getElementById('acc_details1').style.display="";
			document.getElementById('acc_details2').style.display="";
			document.getElementById('acc_details3').style.display="";
			document.getElementById('acc_details4').style.display="";
			document.getElementById('acc_details5').style.display="";
			document.getElementById('acc_details6').style.display="";
			document.getElementById('acc_details7').style.display="";
			document.getElementById('acc_details8').style.display="";
			document.getElementById('acc_details9').style.display="";
			document.getElementById('acc_details10').style.display="";
			document.getElementById('acc_details11').style.display="";
			
			document.getElementById('m_certificate').style.display="";
			document.getElementById('ed_certificate').style.display="";
		
	});


	
	

	$('#employeedN').click(function(){
			//document.getElementById('wife_name').style.display="none";
			//document.getElementById('marriage_date').style.display="none";
			////document.getElementById('edu_qualification').style.display="none";
			//document.getElementById('quali_specification').style.display="none";
			//document.getElementById('wife_employeed').style.display="none";
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			
			
			document.getElementById('other_company').style.display="none"
			document.getElementById('prev_employeed').style.display="";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="";
			document.getElementById('acc_details1').style.display="";
			document.getElementById('acc_details2').style.display="";
			document.getElementById('acc_details3').style.display="";
			document.getElementById('acc_details4').style.display="";
			document.getElementById('acc_details5').style.display="";
			document.getElementById('acc_details6').style.display="";
			document.getElementById('acc_details7').style.display="";
			document.getElementById('acc_details8').style.display="";
			document.getElementById('acc_details9').style.display="";
			document.getElementById('acc_details10').style.display="";
			document.getElementById('acc_details11').style.display="";
			
			
			document.getElementById('m_certificate').style.display="";
			document.getElementById('ed_certificate').style.display="";
		
	});

	$('#objectionY').click(function(){
			document.getElementById('wife_name').style.display="none";
			document.getElementById('divmarriage_date').style.display="none";
			document.getElementById('edu_qualification').style.display="none";
			document.getElementById('quali_specification').style.display="none";
			document.getElementById('wife_employeed').style.display="none";
			document.getElementById('working_aries').style.display="none";
			document.getElementById('aries_department').style.display="none";
			
			
			document.getElementById('other_company').style.display="none"
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			document.getElementById('was_working_aries').style.display="none";
			document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="";
			document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="";
			document.getElementById('wife_stay').style.display="none";
			document.getElementById('acc_details1').style.display="none";
			document.getElementById('acc_details2').style.display="none";
			document.getElementById('acc_details3').style.display="none";
			document.getElementById('acc_details4').style.display="none";
			document.getElementById('acc_details5').style.display="none";
			document.getElementById('acc_details6').style.display="none";
			document.getElementById('acc_details7').style.display="none";
			document.getElementById('acc_details8').style.display="none";
			document.getElementById('acc_details9').style.display="none";
			document.getElementById('acc_details10').style.display="none";
			
			
			document.getElementById('m_certificate').style.display="none";
			document.getElementById('ed_certificate').style.display="none";
		
	});
	$('#objectionN').click(function(){
			document.getElementById('wife_name').style.display="";
			document.getElementById('divmarriage_date').style.display="";
			document.getElementById('edu_qualification').style.display="";
			document.getElementById('quali_specification').style.display="";
			document.getElementById('wife_employeed').style.display="";
			//document.getElementById('working_aries').style.display="none";
			//document.getElementById('aries_department').style.display="none";
			
			
			//document.getElementById('other_company').style.display="none"
			document.getElementById('prev_employeed').style.display="none";
			document.getElementById('reason').style.display="none";
			//document.getElementById('was_working_aries').style.display="none";
			//document.getElementById('was_aries_department').style.display="none";
			document.getElementById('objection').style.display="";
			//document.getElementById('was_other_company').style.display="none";
			document.getElementById('reason_objection').style.display="none";
			document.getElementById('wife_stay').style.display="";
			document.getElementById('acc_details1').style.display="";
			document.getElementById('acc_details2').style.display="";
			document.getElementById('acc_details3').style.display="";
			document.getElementById('acc_details4').style.display="";
			document.getElementById('acc_details5').style.display="";
			document.getElementById('acc_details6').style.display="";
			document.getElementById('acc_details7').style.display="";
			document.getElementById('acc_details8').style.display="";
			document.getElementById('acc_details9').style.display="";
			document.getElementById('acc_details10').style.display="";
			
			
			document.getElementById('m_certificate').style.display="";
			document.getElementById('ed_certificate').style.display="";
		
	});
   
   
    
$('#firstform').submit(function(){
	//alert(document.getElementById('firstform').married.value);
	////return false;
	if(document.getElementById('firstform').married.value=="") {
		document.getElementById('Errmarried').style.display="";
		return false;
	}
	else {
		document.getElementById('Errmarried').style.display="none";
		
	}
	if(document.getElementById('firstform').married.value=="Y") {
		//alert(document.getElementById('firstform').allowance_objection.value);
		//return false;
		if(document.getElementById('firstform').allowance_objection.value=="") {
		document.getElementById('Errobjection').style.display="";
		return false;
		}
		else {
			

			document.getElementById('Errobjection').style.display="none";
			
			if(document.getElementById('firstform').allowance_objection.value=="Y") {
				if(document.getElementById('firstform').objection_remarks.value=="") {
						document.getElementById('Errreason_objection').style.display="";
						return false;
				}
				else {
					document.getElementById('Errreason_objection').style.display="none";
					
				}
			}

			if(document.getElementById('firstform').allowance_objection.value=="N") {

				if(document.getElementById('firstform').spouse_name.value=="") {
						document.getElementById('Errwife_name').style.display="";
						return false;
				}
				else {
					document.getElementById('Errwife_name').style.display="none";
					
				}
				if(document.getElementById('firstform').marriage_date.value=="") {
					document.getElementById('Errmarriage_date').innerHTML="This field is required";
						document.getElementById('Errmarriage_date').style.display="";
						return false;
				}
				else {
					document.getElementById('Errmarriage_date').style.display="none";
					
				}
				if(document.getElementById('firstform').marriage_date.value!="") {
					var testDate = document.getElementById('firstform').marriage_date.value;
				var date_regex = /^(0[1-9]|1\d|2\d|3[01])\-(0[1-9]|1[0-2])\-(19|20)\d{2}$/;
					if (!(date_regex.test(testDate))) {
						document.getElementById('Errmarriage_date').innerHTML="Invalid date format, Please enter date in dd-mm-YYYY";
						document.getElementById('Errmarriage_date').style.display="";
						return false;
					}
				}
				else {
					document.getElementById('Errmarriage_date').style.display="none";
					
				}

				if(document.getElementById('firstform').education.value=="") {
						document.getElementById('Erredu_qualification').style.display="";
						return false;
				}
				else {
					document.getElementById('Erredu_qualification').style.display="none";
					
				}
				
				if(document.getElementById('firstform').current_work_status.value=="") {
						document.getElementById('Errwife_employeed').style.display="";
						return false;
				}
				else {
					document.getElementById('Errwife_employeed').style.display="none";
					
				}
				if(document.getElementById('firstform').current_work_status.value=="Y") {
				if(document.getElementById('firstform').current_in_aries.value=="") {
						document.getElementById('Errwife_employeed').style.display="";
						return false;
				}
				else {
					document.getElementById('Errwife_employeed').style.display="none";
					
				}
				}
				if(document.getElementById('firstform').current_work_status.value=="Y") {
					if(document.getElementById('firstform').current_in_aries.value=="N") {
						if(document.getElementById('firstform').curr_company.value=="") {
							//alert('');
							document.getElementById('Errother_company').style.display="";
							return false;
						}
						else {
							document.getElementById('Errother_company').style.display="none";
							
						}
					}
				}
				
				if(document.getElementById('firstform').current_work_status.value=="N") {
					if(document.getElementById('firstform').current_in_aries.value=="Y") {
						if(document.getElementById('firstform').curr_department.value=="") {
								document.getElementById('Erraries_department').style.display="";
								return false;
						}
						else {
							document.getElementById('Erraries_department').style.display="none";

							
							
						}
					}
}

				if(document.getElementById('firstform').current_work_status.value=="N") {
						if(document.getElementById('firstform').prev_work_status.value=="") {
							//alert('');
							document.getElementById('Errwife_employeed').style.display="";
							return false;
						}
						else {
							document.getElementById('Errwife_employeed').style.display="none";
							
						}
				}
				//alert('');
				if(document.getElementById('firstform').current_work_status.value=="N") {
				if(document.getElementById('firstform').current_living_status.value=="") {
					document.getElementById('Errliving').style.display="";
							return false;
					
				}
				else {
					document.getElementById('Errliving').style.display="none";
				}
				
				if(document.getElementById('firstform').current_holding_account.value=="") {
					document.getElementById('Errholdingaccount').style.display="";
							return false;
					
				}
				else {
					document.getElementById('Errholdingaccount').style.display="none";
				}
				}
			}

		}
		
	}
	//return false;
});
}
);
</script>