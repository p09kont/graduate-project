<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <?php
        include("./include/head.php");
        ?>

        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Journals</title>
    </head>
    <body>
        <?php
        $pageName = 'Journals';
        include("./include/nav.php");
        ?>
        <div class="container">
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Journals</h2>
                <div class="card-body">
                    <table id="journals-table" border="0" class="table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Journal name</th>
                                <th>Articles</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Journal name</th>
                                <th>Articles</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php include("./include/footer.php"); ?>
        <script>
            $(document).ready(function () {
                $("#journals-table").DataTable({
                    "ajax": {
                        url: "./ajax/getJournals.php",
                        type: "POST"
                    },
                    "columns": [
                        {"data": "name"},
                        {"data": "articles"}
                    ],
                    "order": [[1, 'desc']]
                });
            });
        </script>
    </body>
</html>
