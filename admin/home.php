<?php
session_start();
if (!isset($_SESSION["admin_session"])) {
    header("Location: index.php");
}
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <?php include("../include/head.php"); ?>
        <!-- Link to css file-->
        <!--<link rel="stylesheet" type="text/css" href="../css/style.css">-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>Admin</title>
        <style type="text/css">
            td.details-control {
                background: url('../resources/details_open.png') no-repeat center center;
                cursor: pointer;
            }
            tr.details td.details-control {
                background: url('../resources/details_close.png') no-repeat center center;
            }
            .table > tbody > tr > td {
                vertical-align: middle;
            }
            .container{
                margin-top: 80px;
            }
            /*.active{
                border: solid 2px;
                border-color: #26488d;
                border-radius: 5px;
                color: white;
                background-color: #26488d;
                padding: 5px;
            }*/
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-md bg-primary navbar-dark fixed-top">
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="collapsibleNavbar">
                <div class="navbar-nav">
                    <strong><a class="nav-item nav-link active" href="home.php">Home</a></strong>
                </div>
                <div class="navbar-nav">
                    <strong><a class="nav-item nav-link" href="logout.php">Logout &nbsp;<span><i class="fa fa-sign-out" style="font-size:16px"></i></span></a></strong>
                </div>
            </div>    
        </nav>
        <div class="container box">
            <h1 align="center">Database Management</h1>
            <br />
            <div class="table-responsive">
                <br />
                <div align="right">
                    <button type="button" id="add_button" data-toggle="modal" data-target="#userModal" class="btn btn-info btn-lg">Add</button>
                </div>
                <br /><br />
                <table id="user_data" class="table table-bordered table-striped" cellspacing="0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Image</th>
                            <th>Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Property</th>
                            <!--<th>Scopus Id</th>-->
                            <th>Data</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
        <div id="userModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="user_form" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add User</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <label>Enter First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" />
                            <br />
                            <label>Enter Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" />
                            <br />
                            <label>Enter Property</label>
                            <input type="text" name="property" id="property" class="form-control" />
                            <br />
                            <label>Upload Person's CSV file</label>
                            <br />
                            <input type="file" name="user_csv" id="user_csv" />
                            <span id="user_uploaded_csv"></span>
                            <br />
                            <br />
                            <label>Select Person's Image</label>
                            <br />
                            <input type="file" name="user_image" id="user_image" />
                            <br />
                            <span id="user_uploaded_image"></span>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="user_id" id="user_id" />
                            <input type="hidden" name="operation" id="operation" />
                            <input type="submit" name="action" id="action" class="btn btn-success" value="Add" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="insertToDBModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">INSERT</h5>
                    </div>
                    <div class="modal-body">
                        <div class="progress" style="height:20px">
                            <div id="bar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%;height:20px">0%</div>
                        </div>
                        <div id="progressInfo">&nbsp;</div>
                        <div id="message">&nbsp;</div>
                        <div id="inserted">&nbsp;</div>
                        <div id="updated">&nbsp;</div>
                    </div>
                    <div class="modal-footer">

                        <button id="insbtn" type="button" class="btn btn-primary" value="">YES</button>
                        <button id="insertToDBModalCloseBtn" type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">

            function format(d) {
                return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                        '<tr>' +
                        '<td><b>CSV:</b></td>' +
                        '<td>' + d.csv + '</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td><b>Scopus names:</b></td>' +
                        '<td>' + d.otherNames + '</td>' +
                        '</tr>' +
                        '<td><b>Last update:</b></td>' +
                        '<td>' + d.lastUpdate + '</td>' +
                        '</tr>' +
                        '</table>';
            }

            var timer;

            function refreshProgress() {
                //$('.progress').show();
                $.ajax({
                    url: "./checker.php?file=<?php echo session_id() ?>",
                    success: function (responce) {
                        if (responce.check) {
                            $('#bar').css({"width": responce.percent + "%"});
                            $('#bar').text(responce.percent + "%");
                            //$('#progressInfo').text(responce.percent);
                            //$('#message').html(responce.message);
                            $('#inserted').text("New publications inserted: " + responce.inserted);
                            $('#updated').text("Updated citations to " + responce.updated + " publications.");
                            // If the process is completed, we should stop the checking process.
                            if (responce.percent === 100) {
                                window.clearInterval(timer);
                                timer = window.setInterval(completed, 1000);
                            }
                        } else {

                            window.clearInterval(timer);
                            timer = window.setInterval(noNames(responce), 1000);
                            //$('.progress').hide();
                            //$('#message').html(responce.message);
                        }
                    }
                });
            }

            function completed() {
                $('.progress').hide();
                $('#message').html("Procces completed.");
                $('#insertToDBModalCloseBtn').prop('disabled', false);
                window.clearInterval(timer);
            }

            function noNames(r) {
                $('.progress').hide();
                $('#progressInfo').text("");
                $('#message').html(r.message);
                $('#inserted').text("");
                $('#updated').text("");
                $('#insertToDBModalCloseBtn').prop('disabled', false);
                window.clearInterval(timer);
            }

            $(document).ready(function () {
                $('#add_button').click(function () {
                    $('#user_form')[0].reset();
                    $('.modal-title').text("Add User");
                    $('#action').val("Add");
                    $('#operation').val("Add");
                    $('#user_uploaded_csv').html('');
                    $('#user_uploaded_image').html('');
                });

                var dataTable = $('#user_data').DataTable({
                    "processing": true,
                    "serverSide": true,
                    //"order": [],
                    "ajax": {
                        url: "./aj/fetch.php",
                        type: "POST"
                    },
                    //"columnDefs":[
                    //{
                    //"targets":[0,1, 7, 8],
                    //"orderable":false,
                    //},
                    //],

                    "columns": [
                        {
                            "className": 'details-control',
                            "orderable": false,
                            "data": null,
                            "defaultContent": ''
                        },
                        {

                            "orderable": false,
                            "data": "image",
                            "defaultContent": ''
                        },
                        {"data": "id"},
                        {"data": "firstname"},
                        {"data": "lastname"},
                        {"data": "property"},
                        /*{"data": "scopusId"},*/
                        {

                            "orderable": false,
                            "data": "data"

                        },
                        {

                            "orderable": false,
                            "data": "manage"

                        }
                    ],
                    "order": [[2, 'asc']]
                });
                // Array to track the ids of the details displayed rows
                var detailRows = [];

                $('#user_data tbody').on('click', 'tr td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = dataTable.row(tr);
                    var idx = $.inArray(tr.attr('id'), detailRows);


                    if (row.child.isShown()) {
                        tr.removeClass('details');
                        row.child.hide();

                        // Remove from the 'open' array
                        detailRows.splice(idx, 1);
                    } else {
                        tr.addClass('details');
                        row.child(format(row.data())).show();

                        // Add to the 'open' array
                        if (idx === -1) {
                            detailRows.push(tr.attr('id'));
                        }
                    }
                });

                // On each draw, loop over the `detailRows` array and show any child rows
                dataTable.on('draw', function () {
                    $.each(detailRows, function (i, id) {
                        $('#' + id + ' td.details-control').trigger('click');
                        //console.log(detailRows);
                    });
                });


                $(document).on('submit', '#user_form', function (event) {
                    event.preventDefault();
                    var firstName = $('#first_name').val();
                    var lastName = $('#last_name').val();
                    var property = $('#property').val();
                    var csv = $('#user_csv').val();
                    var operation = $('#operation').val();
                    var csvExtension = $('#user_csv').val().split('.').pop().toLowerCase();
                    var imageExtension = $('#user_image').val().split('.').pop().toLowerCase();

                    if (imageExtension !== '') {
                        if (jQuery.inArray(imageExtension, ['gif', 'png', 'jpg', 'jpeg']) === -1) {
                            alert("Invalid Image File");
                            $('#user_image').val('');
                            return false;
                        }
                    }

                    if (csvExtension !== '') {
                        if (jQuery.inArray(csvExtension, ['csv']) === -1) {
                            alert("Invalid file type. You must select only .csv file");
                            $('#user_csv').val('');
                            return false;
                        }
                    }
                    if (operation === "Add") {
                        if (firstName !== '' && lastName !== '' && csv !== '') {
                            $.ajax({
                                url: "./aj/insert.php",
                                method: 'POST',
                                data: new FormData(this),
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    alert(data);
                                    $('#user_form')[0].reset();
                                    $('#userModal').modal('hide');
                                    dataTable.ajax.reload();
                                }
                            });
                        } else {
                            alert("Fields First Name, Last Name and CSV file are Required");
                        }
                    }

                    if (operation === "Edit") {
                        if (firstName !== '' && lastName !== '') {
                            $.ajax({
                                url: "./aj/insert.php",
                                method: 'POST',
                                data: new FormData(this),
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    alert(data);
                                    $('#user_form')[0].reset();
                                    $('#userModal').modal('hide');
                                    dataTable.ajax.reload();
                                }
                            });
                        } else {
                            alert("Fields First Name, Last Name and CSV file are Required");
                        }
                    }

                });

                $(document).on('click', '.edit', function () {
                    var user_id = $(this).attr("id");
                    //alert(user_id);
                    $.ajax({
                        url: "./aj/fetch_single.php",
                        method: "POST",
                        data: {user_id: user_id},
                        dataType: "json",
                        success: function (data) {
                            //alert(data);
                            $('#userModal').modal('show');
                            $('#first_name').val(data.firstname);
                            $('#last_name').val(data.lastname);
                            $('#property').val(data.property);
                            //$('#user_csv').val(data.user_csv);
                            $('#user_uploaded_csv').html(data.user_csv);
                            //$('#user_csv').val(data.user_csv);
                            $('.modal-title').text("Edit User");
                            $('#user_id').val(user_id);
                            $('#user_uploaded_image').html(data.user_image);
                            $('#action').val("Edit");
                            $('#operation').val("Edit");
                        }
                    });
                });

                $(document).on('click', '.delete', function () {
                    var user_id = $(this).attr("id");
                    //alert(user_id);
                    if (confirm("Are you sure you want to delete this record ?")) {
                        $.ajax({
                            url: "./aj/delete.php",
                            method: "POST",
                            data: {user_id: user_id},
                            success: function (data) {
                                alert(data);
                                dataTable.ajax.reload();
                            }
                        });
                    } else
                    {
                        return false;
                    }
                });

                $(document).on('click', '.insert', function () {
                    var user_id = $(this).attr("id");
                    var fullName = $(this).attr("value");

                    $('#insertToDBModal').modal({backdrop: "static"});
                    $('#insbtn').val(user_id);

                    $('#insertToDBModal .modal-title').text("Insert or update data for " + fullName);

                    $('#bar').css({"width": "0%"});
                    $('#bar').text("0%");
                    $('#progressInfo').text(" ");
                    $('#message').html("Do you want to insert or update data for <b>" + fullName + "</b> ?");
                    $('#inserted').text(" ");
                    $('#updated').text(" ");
                    $('#insbtn').show();
                    $('#insertToDBModalCloseBtn').text("NO");

                    $('.progress').hide();

                });
                $(document).on('click', '#insbtn', function () {
                    var user_id = $(this).val();

                    $('.progress').show();
                    $('#progressInfo').html("&nbsp;");
                    $('#message').html(" ");
                    $('#inserted').html("&nbsp;");
                    $('#updated').html("&nbsp;");
                    $('#insbtn').hide();
                    $('#insertToDBModalCloseBtn').text("Close");
                    $('#insertToDBModalCloseBtn').prop('disabled', true);

                    $.ajax({
                        url: "./parseCSV.php",
                        method: "POST",
                        data: {user_id: user_id}

                    });
                    timer = window.setInterval(refreshProgress, 1000);
                });

            });
        </script>
    </body>
</html>
