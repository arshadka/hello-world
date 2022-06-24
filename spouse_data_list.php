<?php
session_start();
//$_SESSION['user_id']=589;
//ini_set('display_errors',1);
if (!isset($_SESSION['user_id']))
    header("location:index.php");
include("includes/connect.inc.php");
include('pagination_class.php');


$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);

$current_month = date("n");
$current_year = date("Y");
$userid = $_SESSION['user_id'];




$select_year = isset($_REQUEST['select_year']) ? $_REQUEST['select_year'] : $current_year;
$division_id = isset($_REQUEST['division_id']) ? $_REQUEST['division_id'] : 0;
$sub_division_id = isset($_REQUEST['sub_division_id']) ? $_REQUEST['sub_division_id'] : 0;

$location_id = isset($_REQUEST['location_id']) ? $_REQUEST['location_id'] : 0;
$employee_id = isset($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : 0;

$sub = $_REQUEST['sub'];
$company_id = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;
$is_regular = isset($_REQUEST['is_regular']) ? $_REQUEST['is_regular'] : 0;


$div_result = $mysqli->query("SELECT * FROM  tbl_dimensions where dimension_type=2 and is_active=1");
$comp_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=1 and is_active=1");
$group_result = $mysqli->query("SELECT * FROM  tbl_emp_group");
$location_result = $mysqli->query("SELECT * FROM tbl_emp_workplace");




$previous_year = $select_year-1;
$next_year = $select_year+1;
$username = $_SESSION['user_name'];
//$mysqli->query("DROP TEMPORARY TABLE users;");
$sql= "SELECT group_concat(exclude_id) include_id FROM tbl_user_access_exclude ex , tbl_moduleaccess m WHERE  ex.module_id = m.id AND m.module_access='TIMESHEET' 
AND user_id=".$_SESSION['user_id']." AND status='I'"; 
 $result = $mysqli->query($sql);
 $row = $result->fetch_assoc();
$children=$_SESSION['USER_MODULE_ACCESS']['MAINMODULE']['children'];
 $children_values=$_SESSION['USER_MODULE_ACCESS']['MAINMODULE']['children'].",". $_SESSION['user_id'];
$children_values =  trim($children_values,',');
$row['include_id'] .= ",".$children_values;
$row['include_id'] = trim($row['include_id'],',');
$_SESSION['MODULE_ACCESS']['TIMESHEET']['INCLUDE'] = $row['include_id'];

$user_in = $_SESSION['USER_ACCESS']['MAINMODULE']; //implode(",",($user_array));

$sql = " SELECT ma.*
FROM tbl_user_module_access ma , tbl_moduleaccess m
WHERE ma.module_access_id = m.id 
AND m.module_access='MAINMODULE' AND ma.user_id=".$_SESSION['user_id'];

$result = $mysqli->query($sql);

if($result->num_rows >0) {
	$sql_where = " ";
	while($row = $result->fetch_assoc()) {
		
		$sql_qry = "  ";
		if($row['group_id']>0) {
			$sql_qry .= " AND #.group_id=".$row['group_id'];
		}

		if($row['location_id']>0) {
			$sql_qry .= " AND #.work_location =".$row['location_id'];
		}
		if($row['division_id']>0) {
			$sql_qry .= " AND #.emp_division_id =".$row['division_id'];
		}
		
		$sql_qry = trim($sql_qry, ' AND ');
		$sql_where .= " ( $sql_qry ) OR ";
	}
}
$sql_where = trim($sql_where, ' OR ');

if(!empty($_SESSION['MODULE_ACCESS']['TIMESHEET']['INCLUDE'])) {
	$sql_where.=" OR u.user_id IN(".$_SESSION['MODULE_ACCESS']['TIMESHEET']['INCLUDE'].")";
	}
$sql_where = trim($sql_where, ' OR '); 
$sql_where = "(".$sql_where.") AND (e.status IS NULL OR e.status='I') ";

 $_SESSION['MODULE_ACCESS']['TIMESHEET']['WHERE']  = $sql_where;

$sql_left_join = " LEFT JOIN tbl_user_access_exclude e 
				   ON e.exclude_id = #.user_id 
				   AND e.user_id= ".$_SESSION['user_id'];
$_SESSION['MODULE_ACCESS']['TIMESHEET']['LEFTJOIN']  = $sql_left_join;

//print_r($_SESSION['USER_ACCESS']); 
//exit;

$sql_where = str_replace('#','u',$_SESSION['MODULE_ACCESS']['TIMESHEET']['WHERE']);
$sql_left_join  = str_replace('#','u',$_SESSION['MODULE_ACCESS']['TIMESHEET']['LEFTJOIN']);





$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '1'";

$companies = $mysqli->query($sql);

$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '2'";

$divisions = $mysqli->query($sql);

$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '3'";

$sub_divisions = $mysqli->query($sql);

$sql = "SELECT emp_group_id id,emp_group_name short_name FROM tbl_emp_group";

$groups = $mysqli->query($sql);

$sql = "SELECT id,work_place,date_mode FROM tbl_emp_workplace ORDER BY sort_order";

$locations = $mysqli->query($sql);

$locations1 = $mysqli->query($sql);

while ($row_location = $locations1->fetch_assoc()) {

    $arr_mode[$row_location['id']] = $row_location['date_mode'];
}




/*$log_date = date('Y-m-d H:i:s');


$log_sql = "insert into tbl_employee_log(user_id,log_date,page_type,action,remarks,jobdiary_date) values('$userid','$log_date','Time Sheet','View Time Sheet','','')";

$mysqli->query($log_sql);*/






$current_day = date("d");


$batch = isset($_REQUEST['batch'])?$_REQUEST['batch']:-1;
$_REQUEST['admin_elgibility'] = isset($_REQUEST['admin_elgibility'])?$_REQUEST['admin_elgibility']:1;


$username = $_SESSION['user_name'];
$userid = $_SESSION['user_id'];

$sql_where = str_replace('#','u',$_SESSION['MODULE_ACCESS']['TIMESHEET']['WHERE']);
$sql_left_join  = str_replace('#','u',$_SESSION['MODULE_ACCESS']['TIMESHEET']['LEFTJOIN']);
//$mysqli->query("DROP TEMPORARY TABLE users;");
$sql1 = " select u.full_name as Name,u.employee_code,u2.full_name as Admin,
u.date_created as date_created,u.emp_type,e.name as emptype,
d.short_name as Division,d1.short_name subdivision, d2.short_name company, t.*, w.work_place, 
IF(t.marriage_date='0000-00-00','',DATE_FORMAT(t.marriage_date,'%d/%m/%Y')) marriage_date, t.user_id, t.verified

from tbl_spouse_data t 
left join tbl_users u
on t.user_id=u.user_id
left join tbl_users u2
on t.updated_by=u2.user_id
left join tbl_dimensions d
on u.emp_division_id=d.id
left join tbl_dimensions d1
on u.emp_subdivision_id=d1.id
left join tbl_dimensions d2
on u.emp_company_id=d2.id
LEFT JOIN tbl_emp_workplace w
ON u.work_location = w.id
LEFT JOIN tbl_emp_type e
ON u.emp_type = e.id
where  u.status='Active' and batch='$batch'
";

//$sql1 = "SELECT * FROM users u WHERE u.status='Active'  AND (u.exclude IS NULL OR u.exclude='I')";

if ($employee_id > 0)
    $sql1.=" and u.user_id=$employee_id";

if ($company_id > 0)
    $sql1.=" and u.emp_company_id=$company_id ";

if ($group_id > 0)
    $sql1.=" and u.group_id=$group_id ";

if ($location_id > 0)
    $sql1.=" and u.work_location=$location_id";

if ($division_id > 0)
    $sql1.=" and u.emp_division_id=$division_id";
if ($sub_division_id > 0)
    $sql1.=" and u.emp_subdivision_id=$sub_division_id";
if(!empty($_REQUEST['married'])) {
    if($_REQUEST['married']=="-1")
        $sql1.=" and t.married='' ";
    else
	 $sql1.=" and t.married='".$_REQUEST['married']."'";
}

if(!empty($_REQUEST['objection'])) {
	 $sql1.=" and t.allowance_objection='".$_REQUEST['objection']."'";
}

if(!empty($_REQUEST['emp_type'])) {
	 $sql1.=" and u.emp_type='".$_REQUEST['emp_type']."'";
}

if(!empty($_REQUEST['current_living_status'])) {
	 $sql1.=" and t.current_living_status='".$_REQUEST['current_living_status']."'";
}
//if(!empty($_REQUEST['batch'])) {
//	 $sql1.=" and t.batch='".$_REQUEST['batch']."'";
//}

 if(!empty($_REQUEST['rej_spec_reason'])){
     $sql1.=" and t.rej_spec_reason='".$_REQUEST['rej_spec_reason']."'"; 
 }
 if((isset($_REQUEST['admin_elgibility'])&&$_REQUEST['admin_elgibility']!="")||$_REQUEST['admin_elgibility']=="0"){
     $sql1.=" and t.admin_elgibility='".$_REQUEST['admin_elgibility']."'"; 
 }
 if((isset($_REQUEST['admin_verify'])&&$_REQUEST['admin_verify']!="")||$_REQUEST['admin_verify']=="0"){
     $sql1.=" and t.admin_verify='".$_REQUEST['admin_verify']."'"; 
 }

if(!empty($_REQUEST['certificate']) && $_REQUEST['certificate']=='Y') {
	 $sql1.=" and t.marriage_cetificate !=''";
}
if(!empty($_REQUEST['certificate']) && $_REQUEST['certificate']=='N') {
	 $sql1.=" and t.marriage_cetificate =''";
}
if(!empty($_REQUEST['verified']) && $_REQUEST['verified']=='Y') {
	 $sql1.=" and t.verified ='Y'";
}
if(!empty($_REQUEST['verified']) && $_REQUEST['verified']=='N') {
	 $sql1.=" and (t.verified ='N' OR t.verified ='' )";
}

if(!empty($_REQUEST['is_regular'])) {
	if($_REQUEST['is_regular']=='Y')
	 $sql1.=" and u.is_regular=1";
	if($_REQUEST['is_regular']=='N')
	 $sql1.=" and u.is_regular=0";
}


$married = $_REQUEST['married'];
$objection = $_REQUEST['objection'];
$current_living_status = $_REQUEST['current_living_status'];
//$batch = $_REQUEST['batch'];
$admin_elgibility = isset($_REQUEST['admin_elgibility'])?$_REQUEST['admin_elgibility']:1;
$admin_verify = isset($_REQUEST['admin_verify'])?$_REQUEST['admin_verify']:"";
$rej_spec_reason  = $_REQUEST['rej_spec_reason'];
$emp_type         = $_REQUEST['emp_type'];
$sql1.=" ORDER BY u.full_name ";
//print $sql_temp.="";
//print $sql1;
 /*$mysqli->query($sql1);
print $sql1;*/
//exit;

 $mysql1 = $mysqli->query($sql1);
$count_user = $mysql1->num_rows;
//print "sss".$count_user;
//$mysqli->query("DROP TEMPORARY TABLE users;");
//echo $sql1; 

if (isset($_GET['starting']) && !isset($_REQUEST['search'])) {
    $starting = $_GET['starting'];
} else {
    $starting = 0;
}
$recpage = 30; //number of records per page


$obj = new pagination_class($sql1,$starting,$recpage,$count_user);
$output = $obj->result;

$query = " SELECT u.level,u.full_name short_name,u.user_id,d.short_name as division ,d.id as emp_div_id 
FROM tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id 
$sql_left_join
where   u.status='Active' ";


$query.=" order by d.short_name,u.full_name";

$result_users = $mysqli->query($query);
$arrYesNo = array(""=>" ","Y"=>"Yes","N"=>"No");
$arrYesNoL = array("Y"=>"Yes","N"=>"No","-1"=>"No data");
$arrlive = array("L"=>"Local","O"=>"Overseas","N"=>"No Bank Account");
$arrbatch = array("0"=>"-","-1"=>"Proposed","1"=>"1st");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

    <head>

        <link rel="icon" href="images/favicon.ico" type="image/x-icon">

        <title>Online Job Diary - Aries Marine</title>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

        <link href="css/style.css" rel="stylesheet" type="text/css">

        <link href="css/calendarstyle.css" rel="stylesheet" type="text/css">


 <link rel='stylesheet' type='text/css' href='js/chosen/1.1.0/chosen.css'/>

        <script type="text/javascript" src="javascript/calendar.js"></script>

        <script type="text/javascript" src="javascript/js.js"></script>

        <script type="text/javascript" src="javascript/date-functions.js"></script>



    </head>

    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
        <div class="modal" id="ListModal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"  >
        <div class="modal-dialog" role="document" style="width:550px;margin: 0 auto;">
            <div class="modal-content">
                <div class="modal-header modal-header-success">
                    <h3 class="modal-title" id="ModalLabel"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='closeProdmodal()'>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-aqua-gradient" id="modalBody">
<form id="addjobno">
  <div class="form-group">
    <label for="exampleFormControlInput1">Job Number</label>
    <input type="text" class="form-control" id="job_no" name="job_no" placeholder="Add job number">
  </div>
    <div class="form-group">
    <label for="exampleFormControlInput1">Client</label>
    <input type="text" class="form-control" id="client" name="client" placeholder="Enter client name">
  </div>
  

  <div class="form-group">
    <label for="exampleFormControlTextarea1">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
  </div>
    
 
    <button type="button" class="btn btn-primary" onclick=" return saveJobnum()">Save</button>
</form>

                </div>

            </div>
        </div>
    </div>

        <table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse">

            <?php
            include("includes/header.php");

            $num = $starting;
            $sql_reason = "SELECT * from tbl_spouse_data_rejection_reasons where status=1";
            $arr_reason = $mysqli->query($sql_reason);
            
            $sql_type = "SELECT * from tbl_emp_type";
            $arr_type = $mysqli->query($sql_type);
            ?>

            <tr>

                <td>&nbsp;



                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">

                        <tr><td colspan="8">



                                <table width="95%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">



                                    <form action="spouse_data_list.php" name="frm" method="post">

                                        <tr>

                                            <td align="center" colspan="10">&nbsp;</td>

                                        </tr>

                                        <tr>

                                            <td colspan="20">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">







                                                    <tr height="30" style="border:#a6107b solid 1px;">

                                                        <td colspan="3" align="center" width="25%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;SPOUSE DATA</font></b></td>



                                                    </tr>

                                                    <?php
                                                    $count_row = 1;
                                                    ?>

                                                    <tr height="50" style="border:#a6107b solid 1px;">

                                                        <td  width="100%" align="center" style="border:#000000 solid 1px; padding-left:1px"  <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>>

                                                            <strong> </strong><?php
                                                            $month_names = array("1" => "Jan","2" => "Feb","3" => "Mar","4" => "Apr","5" => "May","6" => "Jun","7" => "Jul","8" => "Aug","9" => "Sep","10" => "Oct","11" => "Nov","12" => "Dec");

                                                            $year = date('Y');

                                                            $month = date('m');
                                                            $arrYear = range(2014,2030);
                                                            ?>
                                                
 
                                              
                                                        

                                              



                                                            <strong>Emp:</strong>

                                                            <select name='employee_id' id='employee_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Employee</option>

                                                                <?php
                                                                while ($row = $result_users->fetch_assoc()) {

                                                                    if ($previous_div_id != $row['emp_div_id'])
                                                                        echo "<optgroup label='".$row['division']."'>";

                                                                    $previous_div_id = $row['emp_div_id'];
                                                                    ?><option value='<?php print($row['user_id'])?>' <?php if ($row['user_id'] == $_REQUEST['employee_id']) {?> selected<?php }?>><?php print($row['short_name'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>

<strong>Certificate</strong>

                                                            <select name='certificate' id='married' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select</option>

                                                                <?php
                                                               foreach($arrYesNo as $key => $value) {
                                                                    ?><option value='<?php print($key)?>' <?php if ($key == $_REQUEST['certificate']) {?> selected<?php }?>><?php print($value)?></option><?php
                                                                }
                                                                ?>

                                                            </select>
														  <strong>	&nbsp;&nbsp;   &nbsp;Objection:&nbsp;</strong>

                                                            <select name='objection' id='objection' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">All</option>

                                                                <?php
                                                               foreach($arrYesNoL as $key => $value) {
                                                                    ?><option value='<?php print($key)?>' <?php if ($key == $_REQUEST['objection']) {?> selected<?php }?>><?php print($value)?></option><?php
                                                                }
                                                                ?>

                                                            </select>
                                                            





                                                            
															<strong>Married</strong>

                                                            <select name='married' id='married' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select</option>

                                                                <?php
                                                               foreach($arrYesNoL as $key => $value) {
                                                                    ?><option value='<?php print($key)?>' <?php if ($key == $_REQUEST['married']) {?> selected<?php }?>><?php print($value)?></option><?php
                                                                }
                                                                ?>

                                                            </select>
															 

															
                                                            <strong>	&nbsp;Batch:</strong>

                                                            <select name='batch' id='batch' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <!--<option value="">Select</option>-->
                                                                 <option <?php if($batch==1)echo "selected"; ?> value="1">Batch 1</option>
                                                              <option  <?php if($batch==-1)echo "selected"; ?> value="-1">Proposed</option>

                                                            </select>
                                                            
                                                               <strong>	&nbsp;Admin Status:</strong>

                                                            <select name='admin_elgibility' id='admin_elgibility' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="" <?php if($admin_elgibility=='') { ?>selected<?php } ?>>All</option>
                                                                <option <?php if ($admin_elgibility == "1") echo "selected"; ?> value="1">Eligible</option>
                                                                <option  <?php if ($admin_elgibility == "2") echo "selected"; ?> value="2">Not Eligible</option>
                                                                <option  <?php if ($admin_elgibility == "0") echo "selected"; ?> value="0">Under Process</option>
                                                            </select>
                                                            
                                                            <strong>	&nbsp;Admin Verified:</strong>

                                                            <select name='admin_verify' id='admin_verify' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="" <?php if($admin_verify=='') { ?>selected<?php } ?>>All</option>
                                                                <option <?php if ($admin_verify == "1") echo "selected"; ?> value="1">Yes</option>
                                                                <option  <?php if ($admin_verify == "2") echo "selected"; ?> value="2">No</option>
                                                              <option  <?php if ($admin_verify == "0") echo "selected"; ?> value="0">Under Process</option>
                                                            </select>
                                                            
                                                             
<!--                                                             <option value="" <?php if($rej_spec_reason=='') { ?>selected<?php } ?>>Select</option>
														<option value="Bachelor" <?php if($rej_spec_reason=='Bachelor') { ?>selected<?php } ?>>Bachelor</option>
                                                                                                                <option value="Project employee" <?php if($rej_spec_reason=='Project employee') { ?>selected<?php } ?>>Project employee</option>
                                                                                                                <option value="3 yrs not completed" <?php if($rej_spec_reason=='3 yrs not completed') { ?>selected<?php } ?>>3 yrs not completed</option>
                                                                                                                <option value="Hold" <?php if($rej_spec_reason=='Hold') { ?>selected<?php } ?>>Hold</option>
                                                                                                                <option value="Delegation not filled" <?php if($rej_spec_reason=='Delegation not filled') { ?>selected<?php } ?>>Delegation not filled</option>-->
													
                                                            

															
 <!-- <label style="color:#F00" for="sub"><strong><font style="font-size:14px">View My Tree</font></strong></label>
                                                           
                                                            <input onClick='this.form.submit()' type="checkbox" name="sub" value="1"
<?php if ($sub == 1) {?> checked<?php }?>>
                                                            <input name="go" style="cursor: pointer; vertical-align: top;" src="images/Go.gif" type="image"> -->

                                                        </td>

                                                        
                                                    </tr>



                                                    <tr height="40" style="border:#a6107b solid 1px;">



                                                        <td align="center" colspan="3" bgcolor="#e9e9e9">

                                                            <strong>Company:&nbsp;</strong>

                                                            <select name='company_id' id='company_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Company</option>

                                                                <?php
                                                                while ($row = $companies->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['id'])?>' <?php if ($row['id'] == $_REQUEST['company_id']) {?> selected<?php }?>><?php print($row['short_name'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>&nbsp;

                                                            <strong>Division:</strong>

                                                            <select name='division_id' id='division_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Division</option>

                                                                <?php
                                                                while ($row = $divisions->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['id'])?>' <?php if ($row['id'] == $_REQUEST['division_id']) {?> selected<?php }?>><?php print($row['short_name'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>&nbsp;





                                                            <strong>Sub Division:</strong>

                                                            <select name='sub_division_id' id='sub_division_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Subdivision</option>

                                                                <?php
                                                                while ($row = $sub_divisions->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['id'])?>' <?php if ($row['id'] == $_REQUEST['sub_division_id']) {?> selected<?php }?>><?php print($row['short_name'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>



                                                            <strong>Group:&nbsp;</strong>

                                                            <select  style="width:125px" name='group_id' id='group_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Group</option>

                                                                <?php
                                                                while ($row = $groups->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['id'])?>' <?php if ($row['id'] == $_REQUEST['group_id']) {?> selected<?php }?>><?php print($row['short_name'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>

                                                            <strong>Location:&nbsp;</strong>

                                                            <select name='location_id' id='location_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Location</option>

                                                                <?php
                                                                while ($row = $locations->fetch_assoc()) {

                                                                    $arr_mode[$row['id']] = $row['date_mode'];
                                                                    ?><option value='<?php print($row['id'])?>' <?php if ($row['id'] == $_REQUEST['location_id']) {?> selected<?php }?>><?php print($row['work_place'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>

															<strong>Effism User&nbsp;</strong>

                                                            <select name='is_regular' id='is_regular' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="-1">Select Option</option>

                                                                <option value="Y" <?php if($_REQUEST['is_regular']=='Y') { ?> selected <?php } ?> >Yes</option>
                                                                <option value="N" <?php if($_REQUEST['is_regular']=='N') { ?> selected <?php } ?> >No</option>


                                                            </select>                                                            
                                                             <strong>	&nbsp;&nbsp;   &nbsp;Employee Type :&nbsp;</strong>

                                                            <select name='emp_type' id='emp_type' class="chosen-select-deselect" onchange='this.form.submit()'>
                                                                <option value="" <?php if($emp_type=='') { ?>selected<?php } ?>>Select</option>
                                                                                                       <?php
														while($row_type = $arr_type->fetch_assoc()) {
															?><option value="<?php print($row_type['id']);?>" <?php if($emp_type==$row_type['id'])  { ?> selected <?php } ?>><?php print($row_type['name']);?></option><?php
														}?> </select>
                                                            
<!--														<strong>Verified&nbsp;</strong>

                                                            <select name='verified' id='verified' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="-1">Select Option</option>

                                                                <option value="Y" <?php if($_REQUEST['verified']=='Y') { ?> selected <?php } ?> >Verified</option>
                                                                <option value="N" <?php if($_REQUEST['verified']=='N') { ?> selected <?php } ?> >Not Verified</option>


                                                            </select>-->

                                                         
                                                         
                                                           </td>



                                                                                                <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><a href="mail.php?user_id=<?php echo $q['user_id']?>&date_from=<?php echo $datefrom?>&date_to=<?php echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->

                                                    </tr>

















                                                    <tr>
                                                     <td  align="right" colspan="17"><font size="3" style="background-color:#0099FF"></font></td>

                                                      



                                                    </tr>

                                                </table>




                                            </td>

                                        </tr>

                                    </form>
 <tr> <td style="border:#a6107b solid 1px;font-weight:bold" colspan="16" align="left">User Count: <?=$count_user?></td></tr>

                                    <tr>

                                        <td style="border:#a6107b solid 1px;" colspan="14" align="center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $obj->anchors;?></b></font></td>
										
<td style="border:#a6107b solid 1px;" align ="right"><b><font size="2" color="#0000ff" face="Verdana, Arial, Helvetica, sans-serif"><a href="spouse_data_excel.php?company_id=<?php echo $company_id?>&division_id=<?php echo $division_id?>&sub_division_id=<?php echo $sub_division_id?>&emp_type=<?php echo $emp_type?>&location_id=<?php echo $location_id?>&group_id=<?php echo $group_id?>&married=<?php echo $married?>&objection=<?php echo $objection?>&current_living_status=<?php echo $current_living_status?>&batch=<?php echo $batch?>&admin_elgibility=<?php echo $admin_elgibility?>&rej_spec_reason=<?php echo $rej_spec_reason?>&group_id=<?php echo $group_id?>&is_regular=<?php echo $is_regular?>&certificate=<?php print($_REQUEST['certificate'])?>&employee_id=<?php print($employee_id);?>&verified=<?php print($_REQUEST['verified']);?>"><strong> Report</strong><img src="images/page_excel.png"></a></font></b>
</td>

                                    </tr>

                                   

                                    <tr height="30" style="border:#a6107b solid 1px;">


                                        <td  style="border:#000000 solid 1px;" align="center" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;No:</font></b></td>

                                        <td  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Name</font></b></td>
										<td  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Emp Code</font></b></td>


                                         
                                        <td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Company</font></b></td>


                                        <td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Division</font></b></td>
										
										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Sub Division</font></b></td>
                                      <td  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Emp Type</font></b></td>

										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Married</font></b></td>
										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Objection </font></b></td>
										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Spouse Name</font></b></td>
										
										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Marriage Date</font></b></td>
										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Working </font></b></td>
										
										
										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Objection Remarks</font></b></td>
											<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Admin Status</font></b></td>
										 <td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Verify</font></b></td>
										
										<td align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;</font></b></td>

                        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->

                                    </tr>

                                    <?php

//	$result4 =mysql_fetch_assoc($mysql4);

                                    $count_row = 1;


                                    while ($result = $output->fetch_assoc()) {



                                        /* echo "<pre>";







                                          print_r($result);

                                          //print_r($result4);

                                          echo "</pre>";

                                         */



                                        $month = date('n',strtotime($result['date_created']));
                                        $year = date('Y',strtotime($result['date_created']));




                                        //echo $result4['user_id'],$q['user_id']."<br>";
                                        ?>



                                        <tr height="20" style="border:#a6107b solid 1px;">



                                            <td  style="border:#000000 solid 1px;" align="center" <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $num = $num + 1;?></b></font></td>


                                            <td style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $result['Name'];?></font></td>
											
                                            <td style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $result['employee_code'];?></font></td>

                                            
                                            <td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                        <?php echo $result['company'];?></font></td>

											 <td align="center" style="border:#000000 solid 1px; padding-left:5px;" <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                        <?php echo $result['Division'];?></font></td>
											 <td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                        <?php echo $result['subdivision'];?></font></td>
														<td style="border:#000000 solid 1px; padding-left:5px;" <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $result['emptype'];?></font></td>
											<td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                        <?php echo $arrYesNo[$result['married']];?></font></td>

														 <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $arrYesNo[$result['allowance_objection']];?></b></font></td>
														<?php 
													 ?>
														
													 <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                        <?php echo $result['spouse_name'];?></font></td>
														
													 <td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                        <?php echo $result['marriage_date'];?></font></td>
														
													 <td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                        <?php echo $arrYesNo[$result['current_work_status']];?></font></td>
													
														<td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">                                                        <?php echo $result['objection_remarks'];?></font></td><?php													 ?>
													
													<td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
                                                       
                                                       <?php if($result['admin_verify']==0){$ad_verify="Under Process";}
                                                        else if($result['admin_verify']==1){$ad_verify="  Verified";} 
                                                         else if($result['admin_verify']==2){$ad_verify="Not Verified";}?>
                                                       <?php echo $ad_verify;?>
                                                        </font> 
                                                        <?php if($result['admin_verify']==1)
                                                        
                                                        {?><br><font color="#669bc9"><?php echo "By: ". $result['Admin'];?><br><?php echo "On: ". date('d-m-Y',strtotime($result['updated_date']));} ?></font>
                                                        
                                                        
                                                        
                                                        </td>
														<td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
														
													
                                                         
                                                        <?php if( $result['batch']==-1){?>
                                                         <a href="spouse_data_admin.php?user_id=<?=$result['user_id']?>&verification_process=1"><img src="images/edit.gif" width="12" height="12" border="0" title="UPDATE">

                                            </a>   
                                                     <?php   }?>
                                                         
                                                         </td>
													
													
													
														<td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($result['verified']=='Y') {?> bgcolor="#fcba03"<?php } else if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif">
														<a href="javascript: openWindow('spouse_info.php?employee_id=<?php print($result['user_id']);?>','spouse_info');"><img src="images/view.gif" width="12" height="12" border="0" title="View this Employee">

                                            </a>
														</font></td>
														
														


                                        </tr>

                                        <?php
                                        $count_row++;
                                    }
                                    ?>
                                    <tr>

                                        <td style="border:#a6107b solid 1px;" colspan="17" align="center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $obj->anchors;?></b></font></td>

                                    </tr>

                                    <tr>

                                        <td colspan="13">&nbsp;</td>

                                    </tr>

                                </table>



                            </td>

                        </tr>



                        <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
                        <?php include("includes/footer.php");
                        ?>


                    </table>

                    <script>
                        function pagination(page)
                        {
                            window.location = "spouse_data_list.php?&starting=" + page + "&select_year=<?php echo $select_year?>" + "&company_id=<?php echo $company_id?>"+ "&emp_type=<?php echo $emp_type?>" + "&division_id=<?php echo $division_id?>" +"&sub_division_id=<?php echo $sub_division_id?>" + "&location_id=<?php echo $location_id?>" + "&group_id=<?php echo $group_id?>&married=<?php print($married)?>&objection=<?php print($objection); ?>&batch=<?php print($batch);?>&admin_elgibility=<?php print($admin_elgibility);?>&rej_spec_reason=<?php print($rej_spec_reason);?>&current_living_status=<?php print($current_living_status);?>&is_regular=<?php print($_REQUEST['is_regular'])?>&certificate=<?php print($_REQUEST['certificate'])?>&verified=<?php print($_REQUEST['verified'])?>";
                        }

                    </script>
                    
<script type="text/javascript" src="js/chosen/1.7.0/chosen.jquery.js"></script>

                                <script type="text/javascript">

                                    var config = {'.chosen-select': {},
                                        '.chosen-select-deselect': {allow_single_deselect: true},
                                        '.chosen-select-no-single': {disable_search_threshold: 10},
                                        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                                        '.chosen-select-width': {width: "95%"}

                                    }

                                    for (var selector in config) {

                                        $(selector).chosen(config[selector]);





                                    }





                                    ;



            function openWindow(url, title)

            {



                var left = (screen.width - 900) / 2;

                var top = (screen.height - 500) / 2;



                var href;



                if (typeof (url) == 'string')
                    href = url;



                else
                    href = url.href;

                if (window.wind && !wind.closed)

                {

                    //alert("Test");

                    wind.close();

                    wind = window.open(href, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');

                } else {

                    wind = window.open(href, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');

                }

            }







                                </script>



                    </body>

                    </html>