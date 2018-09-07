<!DOCTYPE html>

<html>
    <?php
    include("../Database.php");
    $id = $_GET["id"];
    $dbc = connectToDB();
    $sql = "SELECT * FROM di_professors WHERE id = $id";
    $query = mysqli_query($dbc, $sql);
    $res = mysqli_fetch_assoc($query);
    $firstname = $res["firstname"];
    $lastname = $res["lastname"];
    
    ?>
    <head>
        <meta charset="UTF-8">
        <?php include("../include/head.php") ?>
        <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
        <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>Publications</title>
        <style type="text/css">
            td.details-control {
                background: url('../resources/details_open.png') no-repeat center center;
                cursor: pointer;
            }
            tr.shown td.details-control {
                background: url('../resources/details_close.png') no-repeat center center;
            }

            #theId{
                display: none;
            }
            .container{
                margin-top: 80px;
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
            <h1 align="center">Publicatins of <?php echo $firstname ." " . $lastname; ?></h1><br>
            <div class="table-responsive">
                <!-- The table with pulications -->
                <table id="publicationsTable" border="0" class="table table-striped  table-bordered"  cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Type</th>
                            <th>Cited by</th>
                            <th><!--<input type="checkbox" id="select_all">--></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <br />
            <div align="right">
                <button type="button" name="btn_delete" id="btn_delete" class="btn btn-danger">Delete</button>
            </div>
            <br />
        </div>

        <script>
            $(document).ready(function () {

                $('#btn_delete').click(function () {

                    var id = [];
                    var rows_selected = dataTable.column(5).checkboxes.selected();
                    //console.log(rows_selected);
                    var i;
                    for (i = 0; i < rows_selected.length; i++) {
                        id[i] = rows_selected[i];
                        console.log(id[i]);
                    }

                    if (id.length === 0) {
                        alert("Please Select at least one checkbox");
                    } else {
                        if (confirm("Are you sure you want to delete these " + id.length + " entries ?")) {
                            $.ajax({
                                url: './aj/delete_pubs.php',
                                method: "POST",
                                data: {id: id},
                                success: function (response) {
                                    alert(response);
                                    dataTable.ajax.reload();
                                }
                            });
                        }
                    }
                });



                var professorID = $('#theId').text();
                var dataTableBody = '#publicationsTable tbody';

                var dataTable = $('#publicationsTable').DataTable({
                    //"processing": true,
                    //"serverSide": true,
                    "ajax": "../ajax/fetchAll.php?id=" + professorID + "&a=1",
                    "columns": [
                        {
                            "className": 'details-control',
                            "orderable": false,
                            "data": null,
                            "defaultContent": ''
                        },
                        {"data": "title"},
                        {"data": "year"},
                        {"data": "type"},
                        {"data": "cited_by"},
                        {

                            /*"orderable": false,
                             "data": "checkbox"*/
                            "data": "id",
                            "checkboxes": {"selectRow": true} // me false den allazei hroma stin grami otan epilegete kai den miazei
                            //o diktis tou pontikiou san na epilegete link
                        }

                    ],
                    "select": {
                        "style": "multi",
                        "selector": "td:last-child" //"td:not(:first-child)" patontas pano se oli tin grami tsekarete to checkbox
                    },
                    "order": [[2, 'desc']]
                });
                //var detailRows = [];
                rowChilds(dataTableBody, dataTable, formatAll);
                



            });



            

        </script>
        <script type="text/javascript" src="../js/DataTablesFormatFunctions.js"></script>
    </body>
</html>
