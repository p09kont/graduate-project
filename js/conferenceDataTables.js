/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    var thisConferenceID = $('#theId').text();
    console.log(thisConferenceID);
    var artciclesOfThisConferenceTableBody = '#this-conference-articles-table tbody';
    var articlesOfThisConferenceTable = $('#this-conference-articles-table').DataTable({
        ajax: {
            url: "./ajax/fetchConferenceArticles.php",
            type: "POST",
            data: {confId: thisConferenceID}
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
    rowChilds(artciclesOfThisConferenceTableBody, articlesOfThisConferenceTable, formatConferenceArticles);
});
