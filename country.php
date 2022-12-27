<!-- Data toggle-->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
<?php 
session_start();
error_reporting(0);
include_once 'DB/DBMain.php';
$dbFunc = new DBMain();

// $view = $dbFunc->partner->fetchSingle("country");
foreach($dbFunc->partner->fetch("country") as $viewCountry) {
// $res = $view['country_name'];

// echo $res = $res . '
// 		<li><a href="#' .$view['id']. '"  data-toggle="collapse">
//             ' .$view['country_name']. '</a>
//             <ul id="' .$view['id']. '" class="collapse">
//                 <li> Name: ' .$view['country_name']. '</li>
//             </ul>
// 		</li>		
// ';
// }

?>
<li>
    <a href="#getstate<?php echo $viewCountry['id'];?>"  data-toggle="collapse">Country:- <?php echo $viewCountry['id']. " ".$viewCountry['country_name'];?></a>
    <ul id="getstate<?php echo $viewCountry['id'];?>" class="collapse">
    <?php foreach($dbFunc->partner->fetch("state", array("countryid"=> $viewCountry['id'])) as $viewState) {?>
        <li>
            <a href="#getcityid<?php echo $viewState['id'];?>"  data-toggle="collapse"> State:- <?php echo $viewState['state_name'];?></a>
                <ul id="getcityid<?php echo $viewState['id'];?>" class="collapse">
                <?php foreach($dbFunc->partner->fetch("cities", array("countryid"=> $viewCountry['id'], "stateid"=> $viewState['id'])) as $viewCity) {?>
                    <li>City:- <?php echo $viewCity['city'];?></li>
                <?php }?>
                </ul>
    
        </li>
    <?php }?>
    </ul>
</li>
<?php }?>