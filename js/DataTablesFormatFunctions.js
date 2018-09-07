/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//Datatables format functions
function formatAll(d) {
    // `d` is the original data object for the row
    var type = d.type;
    var result = '';
    switch (type) {
        case "Journal Article":
            result = formatJournalArticles(d);
            break;
        case "Conference Article":
            result = formatConferenceArticles(d);
            break;
        case "Book Chapter":
            result = formatBookChapters(d);
            break;
        case "Book":
            result = formatBooks(d);
            break;
        default:

            break;
    }

    return result;
}

function rowChilds(selector, table, func) {
    $(selector).on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(func(row.data())).show();
            tr.addClass('shown');
        }
    });
}

function formatJournalArticles(d) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
            '<td><b>Authors:</b></td>' +
            '<td>' + d.authors + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Type:</b></td>' +
            '<td>Journal Article</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Journal:</b></td>' +
            '<td>' + d.journal + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Volume:</b></td>' +
            '<td>' + d.volume + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Issue:</b></td>' +
            '<td>' + d.issue + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Pages:</b></td>' +
            '<td>' + d.page_start + ' - ' + d.page_end + '</td>' +
            '</tr>' +
            '</table>';
}

function formatConferenceArticles(d) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
            '<td><b>Authors:</b></td>' +
            '<td>' + d.authors + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Type:</b></td>' +
            '<td>Conference Article</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Conference:</b></td>' +
            '<td>' + d.conference + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Date:</b></td>' +
            '<td>' + d.date + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Location:</b></td>' +
            '<td>' + d.location + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Proccendings:</b></td>' +
            '<td>' + d.proc + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Pages:</b></td>' +
            '<td>' + d.page_start + ' - ' + d.page_end + '</td>' +
            '</tr>' +
            '</table>';
}

function formatBookChapters(d) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
            '<td><b>Authors:</b></td>' +
            '<td>' + d.authors + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Type:</b></td>' +
            '<td>Book Chapter</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Pages:</b></td>' +
            '<td>' + d.page_start + ' - ' + d.page_end + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Book:</b></td>' +
            '<td>' + d.book + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>ISBN:</b></td>' +
            '<td>' + d.isbn + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Publisher:</b></td>' +
            '<td>' + d.publisher + '</td>' +
            '</tr>' +
            '</table>';
}

function formatBooks(d) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
            '<td><b>Authors:</b></td>' +
            '<td>' + d.authors + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Type:</b></td>' +
            '<td>Book</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Pages:</b></td>' +
            '<td>' + d.page_start + ' - ' + d.page_end + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>ISBN:</b></td>' +
            '<td>' + d.isbn + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Publisher:</b></td>' +
            '<td>' + d.publisher + '</td>' +
            '</tr>' +
            '</table>';
}

function formatConferences(d) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
            '<td><b>Date:</b></td>' +
            '<td>' + d.date + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td><b>Location:</b></td>' +
            '<td>' + d.location + '</td>' +
            '</tr>' +
            '</table>';
}