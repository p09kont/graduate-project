


//$(document).ready(function () {
//    $('table.table').DataTable({
//        //"retrieve": true, // gia otan einai poly pinakes stin idia selida
//        "paging": false,
//        "searching": false,
//        "info": false
//    });
//});


$(document).ready(function () {
    
    
    //For working corectly the bootsrap tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
    });
    
    // Important variables
    var professorID = $('#theId').text();
    var professor2ID = $('#prof2Id').text();
    
    //Datatables with no row childs
     var affilCoAuthorsTable = $("#affiliatedCoAuthorsTable").DataTable({
                    /*"ajax": {
                     url: "./ajax/affilCoAuthors.php",
                     type: "POST",
                     data: {id: professorId}
                     },
                     "columns": [
                     {"data": "lastname"},
                     {"data": "plithos"}
                     ],*/
                    "order": [[1, 'desc']]
                });
                $("#noAffiliatedCoAuthorsTable").DataTable({
                    "order": [[1, 'desc']]
                });
                var allCoAuthtorsTable = $("#allCoAuthorsTable").DataTable({
                    "ajax": {
                        url: "./ajax/allCoAuthors.php",
                        type: "POST",
                        data: {id: professorID}
                    },
                    "columns": [
                        {"data": "name"},
                        {"data": "plithos"}
                    ],
                    "order": [[1, 'desc']]
                });
    
    
    
    
    // Datatables tbody needed for row childs
    var allPubsTableBody = '#all-pubs-table tbody';
    var journalArticlesTableBody = '#journal-articles-table tbody';
    var conferenceArticlesTableBody = '#conference-articles-table tbody';
    var bookChaptersTableBody = '#book-chapters-table tbody';
    var bookTableBody = '#books-table tbody';
    var affilMutualPubsTableBody = '#affil-mutual-pubs-table tbody';
    var noAffilMutualPubsTableBody = '#no-affil-mutual-pubs-table tbody';
    
    
    //Datatables with row childs
    var allPubsTable = $('#all-pubs-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchAll.php?id=" + professorID,
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
            {"data": "cited_by"}
        ],
        "order": [[2, 'desc']]
    });

    var affilMutualPublsTable = $('#affil-mutual-pubs-table').DataTable({
        "ajax": "./ajax/fetchAll.php?id=" + professorID +"&id2="+professor2ID+"&affil=true",
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
            {"data": "cited_by"}
        ],
        "order": [[2, 'desc']]
    });
    
    var noAffilMutualPublsTable = $('#no-affil-mutual-pubs-table').DataTable({
        "ajax": "./ajax/fetchAll.php?id=" + professorID +"&id2="+professor2ID+"&affil=false",
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
            {"data": "cited_by"}
        ],
        "order": [[2, 'desc']]
    });


    var journalArticlesTable = $('#journal-articles-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchJournalArticles.php?id=" + professorID,
        "columns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {"data": "title"},
            {"data": "year"},
            {"data": "cited_by"}
        ],
        "order": [[2, 'desc']]
    });


    var conferenceArticlesTable = $('#conference-articles-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchConferenceArticles.php?id=" + professorID,
        "columns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {"data": "title"},
            {"data": "year"},
            {"data": "cited_by"}
        ],
        "order": [[2, 'desc']]
    });


    var bookChaptersTable = $('#book-chapters-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchBookChapters.php?id=" + professorID,
        "columns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {"data": "title"},
            {"data": "year"},
            {"data": "cited_by"}
        ],
        "order": [[2, 'desc']]
    });

    var booksTable = $('#books-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchBooks.php?id=" + professorID,
        "columns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {"data": "title"},
            {"data": "year"},
            {"data": "cited_by"}
        ],
        "order": [[2, 'desc']]
    });


    // Call rowChilds function
    rowChilds(allPubsTableBody, allPubsTable, formatAll);
    rowChilds(journalArticlesTableBody, journalArticlesTable, formatJournalArticles);
    rowChilds(conferenceArticlesTableBody, conferenceArticlesTable, formatConferenceArticles);
    rowChilds(bookChaptersTableBody, bookChaptersTable, formatBookChapters);
    rowChilds(bookTableBody, booksTable, formatBooks);
    rowChilds(affilMutualPubsTableBody, affilMutualPublsTable, formatAll);
    rowChilds(noAffilMutualPubsTableBody, noAffilMutualPublsTable, formatAll);




});

