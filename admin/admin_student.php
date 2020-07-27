<?php include('../inc/admin_header.php') ?>
<?php require_once ('../inc/connection.php');

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

      <div class="container-fluid">
        <h1 class="mt-4">Student Management</h1>
       
      </div>

        <div class="row">
            <div class="container">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Add Student</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Edit Student</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Remove Student</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#" >Student List</a>
                        </li>
                    </ul>
                </div>

    <!--Student Table-->

                <table class="table table-hover mt-4">
                    <thead>
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">User Name</th>
                        <th scope="col">Email</th>
                        <th scope="col"></th>
                        <th scope="col">Options</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody id="tbody">
                    
                        <!-- dynamically add in show_st_table.php using Ajax -->

                    </tbody>
                </table>

                <label for=""></label>

                <!--end Table-->
                <div class="col-md-2"></div>
            </div>
        </div>

        <script src="https://kit.fontawesome.com/4f6c585cf2.js" crossorigin="anonymous"></script><!-- font awsome script -->


  <?php include('../inc/admin_footer.php')?>
        <script>

            //loading table using ajax
            $(document).ready(function(){

                setInterval(function(){
                    $.post('show_st_table.php',{},
                        function(data){
                            $('#tbody').html(data);
                        });
                },2000);
            });

            //ajax remove students
            function rem_stident(st_no){
                var student_no = st_no;

                $.post('remove_students.php',{
                    student_no:student_no
                });
            }

            //ajax freez students
            function frez_student(st_no,event){
                var student_no = st_no;
                var isck = event.target.getAttribute('checked');

                if(isck==null){
                    $.post('student_freez.php',{
                        freez :'false',
                        student_id:student_no
                    },function(data){
                        console.log(data);
                    });
                }
                else{
                    $.post('student_freez.php',{
                        freez :'true',
                        student_id:student_no
                    },function(data){
                        console.log(data);
                    });
                }
            }

        </script>

