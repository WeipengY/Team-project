####Register page

<?php
$page_title = 'register'
include ('header.php');   
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    require ('mysqli_connect.php');
    
    $errors = array();

	if (empty($_POST['first_name'])){
		$errors[] = 'Please enter your first name.';
    }else{
        $fn =mysqli_real_escape_string($dbc,trim($_POST['first_name']));
    }
    if (empty($_POST['last_name'])){
		$errors[] = 'Please enter your last name.';
    }else{
        $ln =mysqli_real_escape_string($dbc,trim($_POST['last_name']));
    }
    if (empty($_POST['email'])){
		$errors[] = 'Please enter your email.';
    }else{
        $e =mysqli_real_escape_string($dbc,trim($_POST['email']));
    }
    if (！empty($_POST['pass1'])){
        if($_POST['pass1'] !=$_POST['pass2']) {
		$errors[] = 'Your password did not match the confiremed password.';
    }else{
        $p =mysqli_real_escape_string($dbc,trim($_POST['pass1']));
    }
}else{
    $errors[] = 'You forgot enter your password'.
}
if(empty($errors)){
    $q = "INERT INTO user(first_name,last_name,email,pass,registration_data) VALUES ('$fn','$ln','$e',SHA2('$p',256),NOW())";
    $r = mysqli_query ($dbc,$q);
    if ($r){
        echo '<h1> Thank you.</h1>
        <p>You are registered now!</p><p><br /></p>';
    }else{
        echo '<h1> Syetem Error.</h1>
        <p class="error">You could not be registered due to a system error. We apologize for any inconvenience!</P>';
        echo '<p>'.mysqli_error($abc). '<br /><br />Query: '.$q.'</p>';
    }
    mysqli_close($abc);
    include('footer.php');
    exit();
}else{
    echo '<h1>Error!</h1>
    <p class="error">The following error(s) occurred:<br />';
    foreach ($errors as $msg){
        echo "-$msg<br />\n";
    }
    echo '<p>Please try again!</p><p><br /></p>';
}
     mysqli_close($abc);   
}    
?>
<h1>Register</h1>
<form action="register.php" method="post">
	<p>First Name: <input type="text" name="first_name" size="15" maxlength="20" value="<?php if(isset($_POST['first_name'])) echo $_POST['first_name'];?>" /></p>
	<p>Last Name: <input type="text" name="last_name" size="15" maxlength="40" value="<?php if(isset($_POST['last_name'])) echo $_POST['last_name'];?>" /></p>
	<p>Email Address: <input type="text" name="email" size="20" maxlength="60" value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>"  /> </p>
	<p>Password: <input type="password" name="pass1" size="10" maxlength="20" value="<?php if(isset($_POST['pass1'])) echo $_POST['pass1'];?>"  /></p>
	<p>Confirm Password: <input type="password" name="pass2" size="10" maxlength="20" value="<?php if(isset($_POST['pass2'])) echo $_POST['pass2'];?>"  /></p>
	<p><input type="submit" name="submit" value="Register" /></p>
</form>
<?php include ('footer.php'); ?>

####Login page
<?php
$page_title ='Login';
include ('header.php'); 

if (isset($errors) && !empty($errors)){
    echo '<h1>Error!</h1>
    <p class="error">The following error(s) occurred:<br />';
    foreach ($errors as $msg){
        echo "-$msg<br />\n";
    }
    echo '</p><p>Please try again!</p>';
}
?><h1>Login</h1>
<form action="login.php" metchod="past">
    <p>Email Address: <input type="text" name="email" size="20" maxlength="60" /></p>
    <p>Password: <input type="password" name="pass1" size="10" maxlength="20" /></p>
    <p><input type="submit" name="submit" value="Login" /></p>
</form>
<?php include ('footer.php'); ?>

#### loggedin data

<?php
session_start();
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] !=md5($_SERVER['HTTP_USER_AGENT']))){
    require('login_functions.inc.php');
    redirect_user();
}
$page_tittle ='Logged In!';
include ('header.php');

echo"<h1>Logged In!</h1>
<p>You are now logged in,{$_SESSION['first_name']}!"</p>
<p><a href=\"logout.php\">Logout</a></p>";

include ('footer.php'); 
?>

<?php
if ($_SERVER['REQUESR_METHOD']=='POST'){
    require('login_functions.inc.php');
    require ('mysqli_connect.php');
    
    list ($check,$data)= check_login($dbc,$_POST['email'],$_POST['pass']);
    
    if($check){
        
        session_start();
        $_SESSION['user_id']=$data['user_id'];
        $_SESSION['first_name']=$data['first_name']
        $_SESSION['agent']=md5($_SERVER['HTTP_USER_AGENT']);
        
        require_user('loggedin.php');
    }else {
        $errors=$data;
    }
           
        }
    mysqli_close($abc);    
    }
include ('login_page.inc.php');
?>

####Logged out
<?php
session_start();
if (!isset($_SESSION['user_id'])){
    
    require('login_functions.inc.php');
    redirect_user();
    
}else{
    $_SESSION = array();
    ession_destory();
    setcookie ('PHPSESSID','',time()-3600,'/','',0,0);
}

$page_tittle ='Logged Out!';
include ('header.php');

echo"<h1>Logged Out!</h1>
<p>You are now logged out.</p>";

include ('footer.php'); 
?>

#### View User page
<?php 

$page_title = 'View the Current User';
include ('header.php');

echo'<h1>Registerd User</h1>';

require('mysqli_connect.php');

$q = "SELECT CONCAT(last_name, ',',first_name) AS name,DATE_FORMAT(registration_date,'%M %D,%Y') AS dr FROM user ORDER BY registration_date ASC";

$r = mysqli_query ($dbc,$q);
$num = mysqli_num_rows ($r);

if ($num > 0){
    echo"<p>There are currently $num registered user.</p>\n";
    
    echo '<table align="center" cellspacing="3" cellpadding="3" width="75%">
    <tr><td align="left"><b>Name</b></td><td align="left"><b>Date Registered</b></td></tr>';
    
    while ($row = mysqli_fetch_array($r,MYSQLI_ASSOC)){
        echo '<tr><td align="left">' . $row['name'] .'</td><td align="left">'. $row['dr'] . '</td></tr>';
    }
    echo'</table>';
    
    mysqli_free_result ($r);
}else{
    
    echo'<p class = "error">There are currently no registered user.</p>';
}
mysqli_close($dbc);

include ('footer.php'); 
?>

<?php
function redirect_user($page = 'index.php'){
    $url= 'http://'. $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
    $url= rtrim($url,'/\\');
    $url.='/'.$page;
    header("Location:$url")
    exit();
    
}
function chech_login($abc,$email='',$pass=''){
    $error=array();
    if (empty($email)){
		$errors[] = 'You forgot your email address.';
    }else{
        $e =mysqli_real_escape_string($dbc,trim($email));
    }
    if (empty($pass1)){
        $errors[] = 'You forgot your password.';
    }else{
        $p =mysqli_real_escape_string($dbc,trim($pass));      
}
    if(empty($errors)){
    $q = "SELECT
    user_id,first_name FROM user WHERE email='$e' AND pass=SHA2('$p',256)";
    $r = mysqli_query ($dbc,$q);
    if (mysqli_num_row($r)==1){
        $row = mysqli_fetch_array($r,MYSQLI_ASSOC);
        
        return array(ture,$row);
    }else{
        $errors[]'The eamil address and password don not match those on file.';
        }
    }
    return array(flase,$errors);
    
####footer data   
<!DOCTYPE html>
<html>
<head>
<title>Data Analysis Group project</title>
</head>
<body>

<br/><br/> Designed by SOIS - Copyright © 2021 
</body>
</html>
    
###header data
<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $page_title; ?></title>
</head>
<body>
    <div id="navigation">
        <ul>
            <li><a href="index.php">Home Page</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="view_users.php">View users</a></li>
            <li><a href="password.php">Change Password</a></li>
            <li><?php
if ((isset($_SESSION['user_id'])) && (basename($_SERVER['PHP_SELF']) !='logout.php')){
    echo '<a href="logout.php">Logout</a>';
}else{
    echo '<a href="login.php">Login</a>';
}
?></li> 
            <li><a href="contact_us.php">Contact us</a></li>
            <li><a href="data.php">Data analysis</a></li>
        </ul>
    </div>
    <div id="content">
    </div>
</body>
</html>

####Change Your Password page
<?php
$page_tittle ='Change Your Password';
include ('header.php');
?>

<from action="contact us.php" method="post">
First Name: <input type="text" name="First Name"><br>
Last Name: <input type="text" name="Last Name"><br>
E-mail: <input type="text" name="email"><br>
<label for="Comments:">Comments:</label>
    <textarea id="Comments:" rows="4" cols="30">Comments:...</textarea><br>
<input type="submit">   
</from>
<?php include ('footer.php'); ?>

####Data analysis page
<?php 
$page_title = 'Data analysis';
include ('header.php');
?>

<form action="Data.php" method="post">
<link href="project-data.css" type="text/css" rel="stylesheet" />
    
<h2>data analysis page</h2>

<button class="accordion">Projected population growth rate from 2019 to 2030</button>
<div class="panel">
  <img src="1.png" width="500" height="500" broder="0"/>
</div>
<button class="accordion">Projected population from 2019 to 2030</button>
<div class="panel">
    <img src="2.png" width="500" height="500" broder="0"/>
</div>
<button class="accordion">Projected population and growth rate from 2019 to 2030</button>
<div class="panel">
    <img src="3.png" width="500" height="500" broder="0"/>
</div>   
<script>
var acc = document.getElementsByClassName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}
</script>
</form>

<?php include ('footer.php'); ?>

#### index data
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><br />
<b>Warning</b>:  Undefined variable $page_title in <b>/home/weipeng/public_html/project/header.php</b> on line <b>7</b><br />
</title>
</head>
<body>
    <div id="navigation">
        <ul>
            <li><a href="index.php">Home Page</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="password.php">Change Password</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="contact_us.php">Contact us</a></li>
            <li><a href="data.php">Data analysis</a></li>
        </ul>
    </div>
    <div id="content">
    </div>
</body>
</html><!DOCTYPE html>
<html>
<head>
<title>Data Analysis Group project</title>
</head>
<body>

<br/><br/> Designed by SOIS - Copyright © 2021
</body>
</html>
####Contact Us page
<?php 
$page_title = 'Contact Us';
include ('header.php');
?><h1>Contact Us</h1>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<link href="project-data.css" type="text/css" rel="stylesheet" />
    
<form action="contact us.php" method="post">
First Name: <input ="accordion" ="text" name="First Name"><br>
Last Name: <input type="text" name="Last Name"><br>
E-mail: <input type="text" name="email"><br>
<label for="Comments:">Comments:</label>
<textarea id="Comments:" rows="4" cols="30">
Comments:...
</textarea><br>
<input type="submit">

</form>

<?php include ('footer.php'); ?>
####scc

.accordion {
  background-color: cyan;
  color: indianred;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align:left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: orange; 
}
p{
  font-family: "Times New Roman", Times, serif; 
  font-size: 10px;
}
h1{
    font-family: 'Spicy Rice', cursive;
    font-size: 15px;
} 
#all {
	border:1px solid red;
	border-radius:25px;
	position: absolute;
	width: 1000px;
	height:1100px;
	box-shadow:4px 4px 8px #999999;
    background: #999;
}

###sql for longin database
<?php
DEFINE('DB_HOST','localhost');
DEFINE('DB_USER','weipeng');
DEFINE('DB_PASSWORD','Yangwp3.14');
DEFINE('DB_NAME','weipeng_login');

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
OR die('Connection failed to SOIS MySQL server with error:'.mysqli_connect_error());

?>


