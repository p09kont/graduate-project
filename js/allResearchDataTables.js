
$(document).ready(function(){
    //For working corectly the bootsrap tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
    });
    
    var allPubsTableBody = '#all-research-pubs-table tbody';
    var journalArticlesTableBody = '#all-research-journal-articles-table tbody';
    var conferenceArticlesTableBody = '#all-research-conference-articles-table tbody';
    var bookChaptersTableBody = '#all-research-book-chapters-table tbody';
    var bookTableBody = '#all-research-books-table tbody';
    
    
    
    //Datatables with row childs
    var allPubsTable = $('#all-research-pubs-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchAll.php",
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

    


    var journalArticlesTable = $('#all-research-journal-articles-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchJournalArticles.php",
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


    var conferenceArticlesTable = $('#all-research-conference-articles-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchConferenceArticles.php",
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


    var bookChaptersTable = $('#all-research-book-chapters-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchBookChapters.php",
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

    var booksTable = $('#all-research-books-table').DataTable({
        //responsive: true,
        "ajax": "./ajax/fetchBooks.php",
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
    
});

