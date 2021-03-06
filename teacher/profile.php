<?php ob_start(); ?>
<?php session_start(); ?>
<?php require_once '../inc/connection.php';?>

<?php  

    if(!isset($_SESSION['teacher_id'])){
        header("Location:../signin.php");
    }
    else{
        $teacher_id = $_SESSION['teacher_id'];

        $first_name="";
        $last_name="";
        $email="";
        $bio="";
        $skills="";
        $phone_number="";

        $query = "SELECT * FROM teacher WHERE teacher_id = {$teacher_id} AND freez = 0 LIMIT 1";
        $result = mysqli_query($connection,$query);

        if($result){
            if(mysqli_num_rows($result) == 1){

                $prode = mysqli_fetch_assoc($result);
            }
            else{
                 header("Location:../signin.php");
            }
        }
        else{
            print_r(mysqli_error($connection));
        }

    }

?>

<?php  
    
    $error = array();

    //update profile goes in here
    if (isset($_POST['update'])) {
        
        $first_name = mysqli_real_escape_string($connection,$_POST['first_name']);
        $last_name = mysqli_real_escape_string($connection,$_POST['last_name']);
        $email = mysqli_real_escape_string($connection,$_POST['email']);
        $phone_number = mysqli_real_escape_string($connection,$_POST['phone_number']);

        if(!empty($_POST['sks'])){
            $skills = implode('/', $_POST['sks']);//convert array to string
        }
        else{
            $skills = "";
        }

        $bio = mysqli_real_escape_string($connection,$_POST['bio']);

        //form validation
        if(empty(trim($first_name))){
            $error[] = "First Name Field Is Empty";
        }
        if(empty(trim($last_name))){
            $error[] = "Last Name Field Is Empty";
        }
        if(empty(trim($email))){
            $error[] = "Email Field Is Empty";
        }

        $fields_len = array("first_name" => 100,"last_name"  => 100,"email"  => 100,"phone_number" => 12,"bio"  => 400);

        foreach ($fields_len as  $len => $length) {
            if(strlen($_POST[$len]) > $length){
                $error[] = $len . "Field Must Be Less Than " . $length . "Charters";
            }    
        }

        //email validation
        if(!filter_var($email,FILTER_VALIDATE_EMAIL) && !empty(trim($email))){
            $error[] = "Please Enter Valid Email";
        }

        //checking enter email is already used
        $query = "SELECT * FROM teacher WHERE email = '{$email}' AND teacher_id != {$teacher_id}";
        $result_set = mysqli_query($connection,$query);

        if($result_set){
            if(mysqli_num_rows($result_set) != 0){
                $error[] = "This Email Is already Entered";
            }
            else{
                $query = "SELECT * FROM student WHERE email = '{$email}'";
                 $result_set = mysqli_query($connection,$query);

                 if($result_set){
                    if(mysqli_num_rows($result_set) != 0){
                        $error[] = "This Email Is already Entered";
                    }
                    else{
                        $query = "SELECT * FROM admin WHERE email = '{$email}'";
                        $result_set = mysqli_query($connection,$query);

                         if($result_set){
                            if(mysqli_num_rows($result_set) != 0){
                                $error[] = "This Email Is already Entered";
                            }
                        }
                    }
                 }
            }
        }
        //uploading profile picture
        if ($_FILES['teacherpic']['name'] != "") {
            if ($_FILES['teacherpic']['error'] == 0) {

                if ($_FILES['teacherpic']['size'] / 1024 < 500) {

                    if ($_FILES['teacherpic']['type'] == 'image/jpeg') {

                        $file_name = $_FILES['teacherpic']['name'];
                        $file_type = $_FILES['teacherpic']['type'];
                        $temp_name = $_FILES['teacherpic']['tmp_name'];

                        $upload_to = "../img/teacher_pic/";

                        if (empty($error)) {

                            $isimg = move_uploaded_file($temp_name, $upload_to . $file_name);

                            if ($isimg) {
                                $is_tc_image = 1;
                                $query = "UPDATE teacher SET is_image = 1,image_name ='{$file_name}' WHERE teacher_id = {$teacher_id}";
                                $result = mysqli_query($connection,$query);
                            }

                        }

                    } else {
                        $error[] = "File Type Must Be jpg";
                    }


                } 
                else {
                    $error[] = "Image Must Be Less Than 500kb";
                }
            } 
            else {
                $error[] = "This Image Can Not Upload";
            }
        }




        if(empty($error)){

            $in_query = "UPDATE teacher SET first_name = '{$first_name}',last_name='{$last_name}',email='{$email}', phone_number = '{$phone_number}', skills = '{$skills}',bio ='{$bio}' WHERE teacher_id = {$teacher_id} ";
            $result_in = mysqli_query($connection,$in_query);

            if($result_in){
                echo "<script>";
                    echo "alert('Profile Updated!')";
                 echo "</script>";
                 header("Location:profile.php");
            }
        }
    }

?>

<?php  

    //changed Password Form Validation
    if(isset($_POST['save'])){

        if(empty(trim($_POST['cpassword']))){
            $error[] = "Current Password Field Is Empty";
        }
        if(empty(trim($_POST['npassword1']))){
            $error[] = "New Password Field Is Empty";
        }
        if(empty(trim($_POST['npassword2']))){
            $error[] = "Confirm Password Field Is Empty";
        }

        if(strlen($_POST['cpassword'])>12){
            $error[] = "Current Password Must Be Less Than 12 Characters";
        }
        if(strlen($_POST['npassword1'])>12){
            $error[] = "New Password Must Be Less Than 12 Characters";
        }
        if(strlen($_POST['npassword2'])>12){
            $error[] = "Confirm Password Must Be Less Than 12 Characters";
        }

        //checking current password is right 

        //if there is no errors
        if(!empty(trim($_POST['cpassword'])) && empty($error)){
            $password = mysqli_real_escape_string($connection,$_POST['cpassword']);
            $shaPassword = sha1($password);

            $query_pw = "SELECT * FROM teacher WHERE teacher_id={$teacher_id} AND password='{$shaPassword}' LIMIT 1";
            $result_pw = mysqli_query($connection,$query_pw);


            if(mysqli_num_rows($result_pw)==0){
                $error[] = "Current Password Is Invalid";
            }
        }

        //check new password and current password is same
        if($_POST['npassword1'] != $_POST['npassword2']){
            $error[] = 'Confirm password Is Invalid';
        }

        /*if thre is no errors*/
        if(empty($error)){
            $conpassword = mysqli_real_escape_string($connection,$_POST['npassword2']);
            $shhpassword = sha1($conpassword);

            $uppw_query = "UPDATE teacher SET password = '{$shhpassword}' WHERE teacher_id={$teacher_id} LIMIt 1";
            $result_uppw = mysqli_query($connection,$uppw_query);

            if($result_uppw){
                echo "<script>";
                    echo "alert('Password Changed')";
                echo "</script>";
            }
        }
    }

    //delete account

    if(isset($_POST['delete_account'])){
        if(empty(trim($_POST['cdlpassword']))){
            $error[] = "Password Field Is Empty";
        }
        if(strlen($_POST['cdlpassword'])>12){
            $error[] = "Password Must Be Less Than 12 Characters";
        }

        if(empty($error)){
            $password = mysqli_real_escape_string($connection,$_POST['cdlpassword']);
            $shapass =sha1($password);
            $query_dl_us = "DELETE FROM teacher WHERE teacher_id ={$teacher_id} AND password='{$shapass}' LIMIT 1";
            $result_dl_us = mysqli_query($connection,$query_dl_us);

            if($result_dl_us){
                echo "<script>";
                    echo "alert('Your Account Successfully Deleted')";
                echo "</script>";
                header("location:../index.php");
            }
        }
    }

?>

<?php include_once 'teacher_header.php';?>


<!-- styles goes in here --> 
<style>
    .container{
        position: relative;
    }
    .errors{
        position: absolute;
        width: 400px;
        border: 1px solid #ff4040;
        box-shadow: 1px 10px 12px 0px #ff9e9e4a;
        padding: 5px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #fffffff2;
        transition: 0.5s;
    }
    .errors .close,.err_content{
        width: 100%;
    }
    .errors .close{
        text-align: right;
    }
    .errors .close button{
        outline: none;
        cursor: pointer;
        font-size: 14px;
        border: none;
        background: none;
    }
    .errors .err_content{
        font-size: 14px;
        color: #8f2828;
    }
    .erhide{
        opacity: 0;
    }
    @media screen and (max-width: 500px) {
        .errors{
            width: 90%;
        }
    }
    .skiils{
        margin-bottom: 10px;

    }
    .pr_skills{
        display: flex;
        flex-wrap: wrap;
        width: 100%;
    }
    .sparent input{
        width: 100px;
        margin: 8px;
        border: none;
        outline: none;
        display: inline-block;
        font-size: 12px;
        padding: 5px;
        pointer-events: none;
    }
    .sparent button{
        border: none;
        outline: none;
        background-color: orange;
        color: #fff;
        font-size: 12px;
        padding: 5px;
        margin-left: -8px;
    }
    .sparent button i{
        pointer-events: none;
    }
    .sk{
        transition: 0.5s;
    }
    #recipient-skills{
        width: 90%;
        display: inline;
    }
    #add{
        display: inline;
        width: 7%;
        font-size: 14px;
    }
    #add:hover i{
        animation: 1s ro linear;
    }
    @keyframes ro{
        form{
            transform: rotateZ(0deg);
        }
        to{
            transform: rotateZ(360deg);
        }
    }
    .trh{
        transform: translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: 0.5s;
    }
    /* styling for change password and delete account */
    .drbtn{
        background: none;
        outline: none;
        margin-left: 15px;
        border: none;
        color: red;
        font-size: 18px;
    }
    .dl_drop{
        display: none;
    }
    .chnagepas,.deleteus{
        margin-bottom: 25px;
        font-size: 18px;
        color: #ff7979;
    }
</style>

<div class="container text-center">
    <?php  

        if(!empty($error)){
            echo ' <div class="errors" id="errors">';
                echo '<div class="close">';
                    echo '<button type="button" id="close"><i class="fas fa-times"></i></button>';
                echo '</div>';
                echo '<div class="err_content">';
                    foreach ($error as $value) {
                        echo "<p>";
                            echo $value;
                        echo "</p>";
                    }
                echo "</div>";
            echo "</div>";
        }

    ?>
    <div class="jumbotron">
        <h1 class="display-4"><?php echo $prode['first_name'] . " " . $prode['last_name']; ?></h1>
        <?php 
        //grtting image 
            if($prode['is_image'] != 0){
                if($prode['image_name'] != null){
                    echo "<img src='../img/teacher_pic/{$prode['image_name']}' alt='Profile pic' class='rounded-circle' style='width: 200px; height: 200px'>";
                }
                else{
                    echo '<img src="../img/defaultteacher.png" alt="Profile pic" class="rounded-circle" style="width: 200px; height: 200px">';
                }
            }
            else{
                    echo '<img src="../img/defaultteacher.png" alt="Profile pic" class="rounded-circle" style="width: 200px; height: 200px">';
            }

        ?>
        
        <p class="lead">profile id:- 0<?php echo $teacher_id; ?></p>
        <p class="lead">Skills</p>
        
            <?php /*dynamic skills*/ 

                $skill_color = array('badge badge-pill badge-primary','badge badge-pill badge-secondary','badge badge-pill badge-success','badge badge-pill badge-danger','badge badge-pill badge-warning');

                $pskills = explode('/', $prode['skills']);
                $i=0;

                foreach ($pskills as $value) {
                    if($i < sizeof($skill_color)){
                        echo "<span class=\" mr-2 {$skill_color[$i]}\">" . $value . "</span>";
                        $i++;
                    }
                    else{
                        $i=0;
                    }
                }

            ?>

        <hr class="my-4">
        <p><?php echo $prode['bio']; ?></p>
        <div class="row justify-content-center" >
            <div class="card border-info mb-3" style="max-width: 18rem; margin-right: 20px" >
                <div class="card-header">Courses I have</div>
                <div class="card-body text-info">
                    <h1 class="card-title">
                        <?php  
                            $query_cos_co = "SELECT count(course_id) AS count_course FROM course WHERE teacher_id = {$teacher_id}";
                            $result_cos_co = mysqli_query($connection,$query_cos_co);

                            $co_cos = mysqli_fetch_assoc($result_cos_co);

                            echo $co_cos['count_course'];
                        ?>
                    </h1>

                </div>
            </div>
            <div class="card border-info mb-3" style="max-width: 18rem; margin-right: 20px">
                <div class="card-header">Students Enrolled</div>
                <div class="card-body text-info">
                    <h1 class="card-title">
                        <?php  
                            $query_cos_stco = "SELECT count(student_id) AS course_students FROM course_enroll WHERE teacher_id = {$teacher_id}";
                            $result_cosst_co = mysqli_query($connection,$query_cos_stco);

                            $co_stcos = mysqli_fetch_assoc($result_cosst_co);

                            echo $co_stcos['course_students'];
                        ?>
                    </h1>

                </div>
            </div>
        </div>
        <p class="lead">
            <a class="btn btn-primary btn-lg" href="#" role="button" data-toggle="modal" data-target="#exampleModal">Edit Profile</a>
        </p>
    </div>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Profile Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">First Name</label>
                        <input type="text" class="form-control" id="recipient-name" name="first_name" value="<?php echo($prode['first_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Last Name</label>
                        <input type="text" class="form-control" id="recipient-name" name="last_name" value="<?php echo($prode['last_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="recipient-email" class="col-form-label">Email</label>
                        <input type="email" class="form-control" id="recipient-email" name="email" value="<?php echo($prode['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="recipient-email" class="col-form-label">Phone Number</label>
                        <input type="text" class="form-control" id="recipient-email" name="phone_number" value="<?php echo($prode['phone_number']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="recipient-email" class="col-form-label">Skills</label>
                        <div class="skiils">
                            <!-- dynamicaly added skills -->
                            <?php  
                                 echo "<div class='pr_skills'>";
                                if(!empty($prode['skills'])){
                                    $nskills = explode('/', $prode['skills']);
                                    foreach ($nskills as $value) {
                                        echo "<div class='sparent'>";
                                        echo "<input type='text' name='sks[]'  value='{$value}' class='sk'>" ;
                                        echo "<button type='button' class='trash'><i class=\"fas fa-trash\"></i></button>" ;
                                        echo "</div>";
                                    }
                                }
                                echo "</div>";
                            ?>
                        </div>
                        <input type="text" class="form-control" id="recipient-skills" placeholder="Add Your Skills">
                        <button type="button" id="add" class="form-control"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Change Profile Picture</label>
                        <input type="file" class="form-control" id="profile-pic" name="teacherpic">
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Profile Bio</label>
                        <textarea class="form-control" id="message-text" name="bio"><?php echo($prode['bio']) ?></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update">Update Profile</button>
                        <button type="button" class="btn btn-primary" name="update" data-toggle="modal" data-target="#password" data-dismiss="modal" style="float: left">Advanced</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Modal -->
    <div class="modal fade" id="password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Password and Account Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="profile.php" method="POST">
                        <label for="cp" class="chnagepas">Change Password</label>
                        <button type="button" class="drbtn" id="cp"><i class="fas fa-caret-down"></i></button>
                        
                        <div class="cp_drop">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Current Password</label>
                                <input type="password" class="form-control" id="recipient-name" name="cpassword" >
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">New Password</label>
                                <input type="password" class="form-control" id="recipient-name" name="npassword1" >

                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="recipient-name" name="npassword2" >

                            </div>
                            <button type="submit" class="btn btn-primary" name="save">Save changes</button>
                        </div>
                    </form>
                    <form action="profile.php" method="POST">
                        <label for="dl"  class="deleteus">Delete Account</label>
                        <button type="button" class="drbtn" id="dl"><i class="fas fa-caret-down"></i></button>

                        <div class="dl_drop">
                            <p>If You Want To Delete Account Permanently, Please Input Password And Click "Delete My Account" Button.</p>
                            <p>When You Delete Your Account You Can Not Recover This Account.</p>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Password</label>
                                <input type="password" class="form-control" id="recipient-name" name="cdlpassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="delete_account">Delete My Account</button>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>


<script src="https://kit.fontawesome.com/4f6c585cf2.js" crossorigin="anonymous"></script>

<script>
    const pr_skills = document.querySelector('.pr_skills');

    //remove skills
    pr_skills.addEventListener('click', function(event){
        const item = event.target;
        if(item.classList[0] === 'trash'){
            item.parentElement.classList.add('trh');
            item.parentElement.addEventListener('transitionend',function(){
                item.parentElement.remove();
            });
        }
    });
    //add skills
    const add = document.querySelector('#add');
    skillInput = document.querySelector('#recipient-skills');

    add.addEventListener('click',function(){

        if(skillInput.value !== ""){
            toadd = document.createElement('div');
            toadd.classList.add('sparent');

            //create input
            toinput = document.createElement('input');
            toinput.setAttribute('type','text');
            toinput.setAttribute('value',skillInput.value);
            toinput.setAttribute('name','sks[]');
            toinput.classList.add('sk');
            toadd.appendChild(toinput);
            skillInput.value = "";

            //create button
            tobutton = document.createElement('button');
            tobutton.setAttribute('type','button');
            tobutton.innerHTML = '<i class="fas fa-trash"></i>';
            tobutton.classList.add('trash');
            toadd.appendChild(tobutton);

            pr_skills.appendChild(toadd);
        }
    });

</script>
<script>
    //errors
    const close = document.querySelector('#close');
    const errors= document.querySelector('#errors');
    const body = document.querySelector('body');
    close.addEventListener('click',function(){
        errors.classList.add('erhide');

        errors.addEventListener('transitionend',function(){
            errors.style.display = "none";
        });
    });
    body.addEventListener('click',function(){
        errors.classList.add('erhide');
        errors.addEventListener('transitionend',function(){
            errors.style.display = "none";
        });
    });
</script>





<?php include_once 'teacher_footer.php';?>

<script>
    //slide change password delete account

    $(document).ready(function(){

        $('#cp').click(function(){
            $('.cp_drop').slideToggle();
            $('.dl_drop').slideToggle();;
        });
        $('#dl').click(function(){
            $('.cp_drop').slideToggle();
            $('.dl_drop').slideToggle();;
        });
    });

</script>