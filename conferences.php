<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <?php
        include("./include/head.php");
        ?>

        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Conferences</title>
    </head>
    <body>
        <?php
        $pageName = 'Conferences';
        include("./include/nav.php");
        ?>
        <div class="container">
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Conferences</h2>
                <div class="card-body">
                    <table id="conferences-table" border="0" class="table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Conference</th>
                                <th>Articles</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Conference</th>
                                <th>Articles</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php include("./include/footer.php");?>
        <script>
            $(document).ready(function () {
                var conferencesTableBody = '#conferences-table tbody';

                var conferencesTable = $("#conferences-table").DataTable({
                    "ajax": {
                        url: "./ajax/getConferences.php",
                        type: "POST"
                    },
                    "columns": [
                        {
                            "className": 'details-control',
                            "orderable": false,
                            "data": null,
                            "defaultContent": ''
                        },
                        {"data": "name"},
                        {"data": "articles"}
                    ],
                    "order": [[2, 'desc']]
                });
                
                rowChilds(conferencesTableBody, conferencesTable, formatConferences);
            });
        </script>
        <script type="text/javascript" src="js/DataTablesFormatFunctions.js"></script>
    </body>
</html>
