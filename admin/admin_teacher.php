<?php ob_start(); ?>
<?php include_once('../inc/admin_header.php') ?>

<?php  
    
    if(!isset($_SESSION['admin_id'])){
        header("Location:../signin.php");
    }

?>

      <div class="container-fluid">
        <h1 class="mt-4">Teacher Management</h1>
        
      </div>

<?php include_once "../inc/adnav.php"; ?>
<?php require_once ('../inc/connection.php');

//var_dump($data);

?>

<style>
 
.switch{
    position: relative;
    width: 65px;
    height: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.switch .slider{
    position: absolute;
    width: 100%;
    height: 100%;
    top:0;
    left: 0;
    bottom: 0;
    right: 0;
    background-color: #f48fb1;
    border-radius: 35px;
    transition: 0.5s;
    cursor: pointer;
}
.switch .slider:before{
    content: "";
    position: absolute;
    top: 2px;
    bottom: 2px;
    right: 2px;
    left: 2px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background-color: #eee;
    transition: 0.5s;
}
.switch input{
    display: none;
}
.switch input:checked + .slider{
    background-color: #616fc7;
}
.switch input:checked + .slider:before{
    transform: translateX(40px);
}

</style>

<!-- start Table -->
<table class="table table-hover mt-4">
    <thead>
    <tr>
        <th scope="col">id</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Skills</th>
        <th scope="col">Email</th>
        <th scope="col"></th>
        <th scope="col">Options</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody id="tbody">
        <!-- display in ajax -->
    </tbody>
</table>


<!--end Table-->
      
<?php include_once('../inc/admin_footer.php')?>
<script src="https://kit.fontawesome.com/4f6c585cf2.js" crossorigin="anonymous"></script><!-- font awsome script -->

<script>
    $(document).ready(function(){

        setInterval(function(){
            $.post('show_tc_table.php',{},function(data){
                $('#tbody').html(data);
            });
        },1000);
    });

    function rem_teacher(teacher_id){
        var teacher_id = teacher_id;

        if(true==confirm("Are You Sure?")){

            $.post('remove_teacher.php',{
                teacher_id:teacher_id
            });
        }

    }

    function frez_teacher(teacher_id){
        var teacher_id = teacher_id;
        var isck = event.target.getAttribute('checked');

        if(isck==null){

            $.post('teacher_freez.php',{
                teacher_id:teacher_id,
                freez:'false'
            },function(data){
                console.log(data);
            });
        }
        else{
            $.post('teacher_freez.php',{
                teacher_id:teacher_id,
                freez:'true'
            },function(data){
                console.log(data);
            });
        }

    }
</script>



