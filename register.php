<?php
session_start();//session variables hold information about one user across multiple pages of one applicatin until closed
$con = mysqli_connect("localhost", "root", "root", "social");

if(mysqli_connect_errno()){
  echo "Failed to connect: " . mysqli_connect_errno();
}

//Declaring variables to prevent errors
$fname = ""; //first name
$lname = ""; //lLast name
$em = ""; //email
$em2 = ""; //email2
$password = "";//password
$password2 = "";//password2
$date = "";//date
$error_array = array();//error messages

if(isset($_POST['register_button'])){ //if register button is pressed

  //registration form values

  //firstname
  $fname = strip_tags($_POST['reg_fname']);// strip any html
  $fname = str_replace(' ', '', $fname);//remove space
  $fname = ucfirst(strtolower($fname));//lowercase all letters and capitalize
  $_SESSION['reg_fname'] = $fname;//Stores firstname into session variable

  //lastname
  $lname = strip_tags($_POST['reg_lname']);// strip any html
  $lname = str_replace(' ', '', $lname);//remove space
  $lname = ucfirst(strtolower($lname));//lowercase all letters and capitalize
  $_SESSION['reg_lname'] = $lname;//Stores lastname into session variable

  //email
  $em = strip_tags($_POST['reg_email']);// strip any html
  $em = str_replace(' ', '', $em);//remove space
  $em = ucfirst(strtolower($em));//lowercase all letters and capitalize
  $_SESSION['reg_email'] = $em;//Stores email into session variable

  //email2
  $em2 = strip_tags($_POST['reg_email2']);// strip any html
  $em2 = str_replace(' ', '', $em2);//remove space
  $em2 = ucfirst(strtolower($em2));//lowercase all letters and capitalize
  $_SESSION['reg_email2'] = $em2;//Stores email2 into session variable

  //password
  $password = strip_tags($_POST['reg_password']);// strip any html but no further modification to string
  $password2 = strip_tags($_POST['reg_password2']);// strip any html but no further modification to string

  $date = date("Y-m-d");

  if($em == $em2){
    //check email is in valid format
    if(filter_var($em, FILTER_VALIDATE_EMAIL)) {
      $em = (filter_var($em, FILTER_VALIDATE_EMAIL));

      //check if email already exists
      $e_check = mysqli_query($con, "SELECT email FROM users WHERE email = '$em'");

      //count rows returned
      $num_rows = mysqli_num_rows($e_check);

      if($num_rows > 0){
        array_push($error_array, "Email already in use<br>");
      }
    }
    else{
      array_push($error_array, "Invalid email format<br>");
    }
  }
  else{
    array_push($error_array, "Emails don't match<br>");
  }

  if(strlen($fname) > 25 || strlen($fname) < 2){
    array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
  }

  if(strlen($lname) > 25 || strlen($lname) < 2){
    array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
  }

  if($password != $password2){
      array_push($error_array, "Your passwords don't match<br>");
  }
  else {
    if(preg_match('/[^A-Za-z0-9]/', $password)) {//pw can only contain lower and uppercase letters numbers
      array_push($error_array, "Your password can only contain english characters or numbers<br>");
    }
  }
  if(strlen($password) > 30 || strlen($password) < 5){
    array_push($error_array, "Your password must contain between 5 and 30 characters<br>");
  }

  if(empty($error_array)){
    $password = md5($password); //encrypt passwword

    //Generate username by concatenating first name and last name
    $username = strtolower($fname) . '_' . strtolower($lname);
    $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username = '$username'");

    $i = 0;
    //if username exists add number to username
    while(mysqli_num_rows($check_username_query) != 0 ){
      $i++;
      $username = $username . "_" . $i;
      $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username = '$username'");
    }

    //Profile picture assignment
    $rand = rand(1, 4);
    if($rand = 1){
      $profile_pic = "assets/images/profile_pics_defaults/head_alizarin.png";
    }
    else if($rand = 2){
      $profile_pic = "assets/images/profile_pics_defaults/head_amethyst.png";
    }
    else if($rand = 2){
      $profile_pic = "assets/images/profile_pics_defaults/head_belize_hole.png";
    }
    else{
      $profile_pic = "assets/images/profile_pics_defaults/head_carrot.png";
    }

    $query = mysqli_query($con, "INSERT INTO users VALUES (NULL, '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',' )");

    array_push($error_array, "<span style='color:#14c800'>You're all set, go ahead and login</span><br>");

    //Clear session variables
    $_SESSION['reg_fname'] = "";
    $_SESSION['reg_lname'] = "";
    $_SESSION['reg_email'] = "";
    $_SESSION['reg_email2'] = "";
  }
}
?>

<html>
<head>
  <title>Swirlfeed</title>
</head>
<body>
  <form action="register.php" method="POST">
    <input type="text" name="reg_fname" placeholder="First Name" value="<?php
    if(isset($_SESSION['reg_fname'])){
      echo $_SESSION['reg_fname'];
    }
    ?>" required>
    <br>
    <?php if(in_array("Your first name must be between 2 and 25 characters<br>", $error_array)) echo "Your first name must be between 2 and 25 characters<br>" ?>

    <input type="text" name="reg_lname" placeholder="Last Name" value="<?php
    if(isset($_SESSION['reg_lname'])){
      echo $_SESSION['reg_lname'];
    }
    ?>"
    required>
    <br>
    <?php if(in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>" ?>

    <input type="email" name="reg_email" placeholder="Email" value="<?php
    if(isset($_SESSION['reg_email'])){
       echo $_SESSION['reg_email'];
    }
    ?>"required>
    <br>

    <input type="email2" name="reg_email2" placeholder="Confirm Email" value="<?php
    if(isset($_SESSION['reg_email2'])){
      echo $_SESSION['reg_email2'];
    }
    ?>" required>
    <br>
    <?php if(in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
    else if(in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
    else if(in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>"; ?>

    <input type="password" name="reg_password" placeholder="password" required>
    <br>
    <input type="password2" name="reg_password2" placeholder="Confirm password" required>
    <br>
    <?php if(in_array("Your passwords don't match<br>", $error_array)) echo "Your passwords don't match<br>";
    else if(in_array("Your password can only contain english characters or numbers<br>", $error_array)) echo "Your password can only contain english characters or numbers<br>";
    else if(in_array("Your password must contain between 5 and 30 characters<br>", $error_array)) echo "Your password must contain between 5 and 30 characters<br>"; ?>

    <input type="submit" name="register_button" value="Register">
    <br>
    <?php if(in_array("<span style='color:#14c800'>You're all set, go ahead and login</span><br>", $error_array)) echo "<span style='color:#14c800'>You're all set, go ahead and login</span><br>"; ?>
  </form>
</body>
</html>
