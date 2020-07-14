<?php ob_start() ?>
<?php session_start(); ?>

<?php include('inc/header.php')?>
<?php include ('inc/nav.php')?>
<?php require_once("inc/connection.php"); ?>

<?php	
	$errors = array();

	if(isset($_POST['submit'])){

		$email = mysqli_real_escape_string($connection,$_POST['email']);
		$password = mysqli_real_escape_string($connection,$_POST['password']);

		//checking fields are empty or not
		if(empty(trim($_POST['email']))){
			$errors[] = "Email Field Is Required";
		}
		if(empty(trim($_POST['password']))){
			$errors[] = "Password Field Is Required";
		}

		//checking fields limit
		if(strlen($email) > 200){
			$errors[] = "Email Field Must Be Less Than 20 Characters";
		}
		if(strlen($password) > 12){
			$errors[] = "Password Field Must Be Less Than 12 Characters";
		}

		//checking valid email entered
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !empty(trim($_POST['email']))){
			$errors[] = "Please Enter Valid Email";
		}
		


		if(empty($errors)){

			$enc_password = sha1($password);

			//checking student or admin or teacher
			$query = "SELECT * FROM student WHERE email = '{$email}' AND password = '{$enc_password}' AND freez = 0 LIMIT 1";
			$result = mysqli_query($connection,$query);

			if($result){
				//executing student
				if(mysqli_num_rows($result)==1){
					$student = mysqli_fetch_assoc($result);

					$_SESSION['student_name'] = $student['user_name'];
					$_SESSION['student_id'] = $student['st_id'];
					header("Location:student_dashboard.php");
				}
				else{
					$query = "SELECT * FROM teacher WHERE email = '{$email}' AND password = '{$enc_password}' AND freez = 0 LIMIT 1";
					$result = mysqli_query($connection,$query);

					if($result){
						if(mysqli_num_rows($result)==1){
							$teacher = mysqli_fetch_assoc($result);

							$_SESSION['teacher_name'] = $teacher['first_name'] . " " . $teacher['last_name'];
							$_SESSION['teacher_id'] = $teacher['teacher_id'];

							header("Location:teacher/teacher_dashboard.php");
							
						}
						else{
							
							$query = "SELECT * FROM admin WHERE email = '{$email}' AND password = '{$enc_password}' LIMIT 1";
							$result = mysqli_query($connection,$query);

							if($result){
								if(mysqli_num_rows($result)==1){

									$admin = mysqli_fetch_assoc($result);

									$_SESSION['admin_name'] = $admin['admin_name'];
									$_SESSION['admin_id'] = $admin['admin_id'];

									header('Location:admin/admin_dashboard.php');
								}
								else{
									$errors[] = "Email Or Password Is Invalid";
								}
							}
							else{
								print_r(mysqli_error($connection));
							}
						}
					}
					else{
						print_r(mysqli_error($connection));
					}

				}

			}
			else{
				print_r(mysqli_error($connection));
				
			}

		}


	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>LankaE learn</title>
	<link rel="stylesheet" href="css/signin.css">
</head>
<body>

	<div class="main_container">
		<h2>Sign In</h2>

		<!-- display errors -->
		<?php 
			echo "<div class ='errors'>";
				if(!empty($errors)){
					foreach ($errors as $value) {
						echo "<p>" . $value . "</p>";
					}
				}
			echo "</div>";

		?>

		<form action="signin.php" method="POST" autocomplete="false">
			<p>
				<label for="email">Email Address:</label>
				<input type="text" name="email" id="email" autofocus>
			</p>
			<p>
				<label for="password">Password:</label>
				<input type="password" name="password" id="password">
			</p>
				<input type="submit" value="Sign In" name="submit">
		</form>
	</div>
	
</body>
<?php mysqli_close($connection); ?>
</html>

<?php include('inc/footer.php')?>