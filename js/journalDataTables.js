/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    var thisJournalID = $('#theId').text();
    //console.log(thisJournalID);
    var artciclesOfThisJournalTableBody = '#this-journal-articles-table tbody';
    var articlesOfThisJournalTable = $('#this-journal-articles-table').DataTable({
        ajax: {
            url: "./ajax/fetchJournalArticles.php",
            type: "POST",
            data: {journalId: thisJournalID}
        },
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
        "order": [[3, 'desc']]
    });
    rowChilds(artciclesOfThisJournalTableBody, articlesOfThisJournalTable, formatJournalArticles);
});
