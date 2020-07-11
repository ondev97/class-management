<?php require_once('inc/connection.php'); ?>

<?php

  $errors = array();

  $first_name ="";
  $last_name ="";
  $email = "";
  $phone_number ="";

  if(isset($_POST['submit'])){
    // if form is submit

    $first_name = $_POST["firstname"];
    $last_name = $_POST["lastname"];
    $email = $_POST["email"];
    $phone_number = $_POST["phonenumber"];


      // checking fields are empty or not
    if(empty(trim($_POST['firstname']))){
      $errors[] = "First Name Field Is Required";
    }
    if(empty(trim($_POST['lastname']))){
      $errors[] = "Last Name Field Is Required";
    }
    if(empty(trim($_POST['email']))){
      $errors[] = "Email Field Is Required";
    }
    if(empty(trim($_POST['phonenumber']))){
      $errors[] = "Phone Number Field Is Required";
    }
    if(empty(trim($_POST['password']))){
      $errors[] = "Password Field Is Required";
    }
    if(empty(trim($_POST['cpassword']))){
      $errors[] = "Confirm Password Field Is Required";
    }

    //checking input fields length

    $length = array("firstname"=>30,"lastname"=>50,"email"=>100,"phonenumber"=>12,"password"=>12,"cpassword"=>12);

    foreach ($length as $field => $value) {
      if(strlen($_POST[$field]) > $value){
        $errors[] = $field . "Must Be Less Than " . $value . " Charcters";
      }
    }

    //checking confirm password and password are same
    if($_POST['password'] != $_POST['cpassword']){
      $errors[] = "Confirm Password Is Invalid";
    }

    // checking user entered valid email or not
    if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
      $errors[] = "Please Input Valid Email Address";
    }

    //checking email is already Exists
    $email = $_POST['email'];

    $checkEquery = "SELECT * FROM teacher WHERE email = '{$email}' LIMIT 1";
    $email_result = mysqli_query($connection,$checkEquery);

    if($email_result){
      if(mysqli_num_rows($email_result)!=0){
        $errors[] = "Email Is Already Exists";
      }
      else{
          //checking student table emails 
          $checkSTquery = "SELECT * FROM student WHERE email = '{$email}' LIMIT 1";
          $email_result = mysqli_query($connection,$checkSTquery);

          if($email_result){
            if(mysqli_num_rows($email_result)!=0){
              $errors[] = "Email Is Already Exists";
            }
          }

      }
    }

    //if empty errors

      if(empty($errors)){
        
        $first_name = mysqli_real_escape_string($connection,$_POST['firstname']);
        $last_name = mysqli_real_escape_string($connection,$_POST['lastname']);
        $email = mysqli_real_escape_string($connection,$_POST['email']);
        $phone_number = mysqli_real_escape_string($connection,$_POST['phonenumber']);
        $password = mysqli_real_escape_string($connection,$_POST['password']);

        $enc_password = sha1($password);

        //insert value to database
        $insert_query = "INSERT INTO teacher(first_name,last_name,email,phone_number,password,freez) VALUES('{$first_name}','{$last_name}','{$email}','{$phone_number}','{$enc_password}',0)";
        $result = mysqli_query($connection,$insert_query);

        if($result){
          echo "<script>";
            echo "alert('{$first_name} Registered')";
          echo "</script>";
          /* 
            ?email can send in here
          */

        }
        else{
          print_r(mysqli_error($connection));
        }
      }

  }

?>

<?php include('inc/admin_header.php') ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LankaE Learn</title>
</head>
<body>

      <div class="container-fluid">
        <h1 class="mt-4">Add Teacher</h1>
        
          <!-- show eroors  -->
          <?php
            if(!empty($errors)){

              echo "<div class='errors'>";
                foreach ($errors as $err){
                  echo "<p>";
                    echo $err;
                  echo "</p>";
                }
              echo "</div>";

            }
            
          ?>

          <!-- teacher add form -->
          <form action="teacherSignUp.php" method="POST">

              <p>
                <label for="fname">First Name:</label>
                <input type="text" name="firstname" id="fname" value ="<?php echo $first_name; ?>">
              </p>
              <p>
                <label for="lname">Last Name:</label>
                <input type="text" name="lastname" ilnamed="" value="<?php echo $last_name; ?>">
              </p>
              <p>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo $email; ?>">
              </p>
              <p>
                <label for="phonenumber">Phone Number:</label>
                <input type="text" name="phonenumber" id="phonenumber" value ="<?php echo $phone_number; ?>">
              </p>
              <p>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password">
              </p>
              <p>
                <label for="cpassword">Confirm Password:</label>
                <input type="password" name="cpassword" id="cpassword">
              </p>

              <input type="submit" value="Add Teacher" name="submit">

          </form>

      </div>

</body>
</html>

     
      
  <?php include('inc/admin_footer.php')?>
