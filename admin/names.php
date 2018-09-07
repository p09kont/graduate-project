<!DOCTYPE html>
<html>
    <?php
    $id = $_GET["id"];
    include("../Database.php");
    $dbc = connectToDB();
    $selectName = "SELECT firstname, lastname FROM di_professors WHERE id = $id";
    $result = mysqli_query($dbc, $selectName);
    $row = mysqli_fetch_assoc($result);
    $firstName = $row["firstname"];
    $lastName = $row["lastname"];
    ?>
    <head>
        <meta charset="UTF-8">
        <?php include("../include/head.php"); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <title>Scopus names</title>
        <style type="text/css">
            #table_div{
                margin: auto;
                width: 60%;
                padding-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div id="theId"><?php echo $id; ?></div>
        <nav class="navbar navbar-expand-md bg-primary navbar-dark fixed-top">
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="collapsibleNavbar">
                <div class="navbar-nav">
                    <strong><a class="nav-item nav-link" href="home.php">Home</a></strong>
                </div>
                <div class="navbar-nav">
                    <strong><a class="nav-item nav-link" href="logout.php">Logout &nbsp;<span><i class="fa fa-sign-out" style="font-size:16px"></i></span></a></strong>
                </div>
            </div>    
        </nav>
        <div class="container">
            <h1 align="center">Manage Scopus names of <?php echo $firstName . " " . $lastName ?> </h1>
            <br />
            <div class="table-responsive">
                <br />

                <br /><br />
                <div id="table_div">
                    <div align="right">
                        <button type="button" id="add_button" data-toggle="modal" data-target="#userModal" class="btn btn-info btn-lg">Add</button>
                    </div>
                    <br /><br />
                    <table id="user_scopus_names" class="table table-bordered table-striped" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="80%">Name</th>
                                <th width="10%">Edit</th>
                                <th width="10%">Delete</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div id="userModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="user_form" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Name</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <label>Enter Name</label>
                            <input type="text" name="name" id="name" class="form-control" />

                            <div class="modal-footer">
                                <input type="hidden" name="prof_id" id="prof_id" value="" />
                                <input type="hidden" name="operation" id="operation" />
                                <input type="submit" name="action" id="action" class="btn btn-success" value="Add" />
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                var profId = $('#theId').text();

                $('#add_button').click(function () {
                    $('#user_form')[0].reset();
                    $('.modal-title').text("Add name");
                    $('#action').val("Add");
                    $('#operation').val("Add");
                    $('#prof_id').val(profId);
                });



                var dataTable = $('#user_scopus_names').DataTable({
                    "processing": true,
                    "serverSide": true,
                    //"order": [],
                    "ajax": {
                        url: "./aj/fetch_names.php?id=" + profId,
                        type: "POST"
                    },
                    "columns": [
                        {"data": "name"},
                        {
                            "orderable": false,
                            "data": "edit"

                        },
                        {
                            "orderable": false,
                            "data": "delete"
                        }
                    ],
                    "order": [[0, 'asc']]

                });

                $(document).on('submit', '#user_form', function (event) {
                    event.preventDefault();
                    var name = $('#name').val();

                    if (name !== '') {
                        $.ajax({
                            url: "./aj/insert_name.php",
                            method: "POST",
                            data: new FormData(this),
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                alert(response);
                                $('#user_form')[0].reset();
                                $('#userModal').modal('hide');
                                dataTable.ajax.reload();
                            }
                        });
                    } else {
                        alert("Name must not be empty !!");
                    }

                });

                $(document).on('click', '.edit', function () {
                    var id = $(this).attr("id");
                    $.ajax({
                        url: "./aj/fetch_name_single.php",
                        method: "POST",
                        data: {id: id},
                        dataType: "json",
                        success: function (response) {
                            $('#userModal').modal('show');
                            $('#name').val(response.name);
                            $('.modal-title').text("Edit name");
                            $('#prof_id').val(id);
                            $('#action').val("Edit");
                            $('#operation').val("Edit");
                        }
                    });
                });

                $(document).on('click', '.delete', function () {
                    var id = $(this).attr("id");
                    if (confirm("Are you sure you want to delete this record ?")) {
                        $.ajax({
                            url: "./aj/delete_name.php",
                            method: "POST",
                            data: {id: id},
                            success: function (responce) {
                                alert(responce);
                                dataTable.ajax.reload();
                            }
                        });
                    }
                });

            });
        </script>
    </body>
</html>
