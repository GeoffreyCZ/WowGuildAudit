var $collectionHolder;

var $addMemberLink = $('<td colspan="6" class="add_member_link"><a href="#" class="add_member_link">Add a member</a></td>');
var $newLinkTr = $('<tr class="add_member_row"></tr>').append($addMemberLink);

function hasClass(target, className) {
    return new RegExp('(\\s|^)' + className + '(\\s|$)').test(target.className);
}

jQuery(document).ready(function () {

    $("#dialog").dialog({
        autoOpen: false,
        modal: true,
        dialogClass: "dlg-no-title"
    });
    $collectionHolder = $('tbody.members');

    $collectionHolder.find('tr').each(function () {
        addMemberFormDeleteLink($(this));
    });

    $collectionHolder.append($newLinkTr);

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addMemberLink.on('click', function (e) {

        e.preventDefault();
        var rowCount = $('tbody tr').length;
        if (rowCount <= 40) {
            addMemberForm($collectionHolder, $newLinkTr);
        } else {
            $("#dialog").dialog("open");
        }
        var table = document.getElementsByTagName('tbody')[0],
            rows = table.getElementsByTagName('tr'),
            text = 'textContent' in document ? 'textContent' : 'innerText';
        var j = 1;
        for (var i = 0, len = rows.length; i < len - 1; i++) {
            if (!(hasClass(rows[i], 'not-count'))) {
                rows[i].children[0][text] = j;
                j++;
            }
        }
    });
    addNumberOfRows();
});


function addMemberForm($collectionHolder, $newLinkTr) {
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);

    var $newFormTr = $('<tr></tr>').append(newForm);
    $newLinkTr.before($newFormTr);
    addMemberFormDeleteLink($newFormTr);

}

function addMemberFormDeleteLink($memberFormTr) {
    var $removeFormA = $('<td class="remove-form"><a class="remove-link" href="#"><i class="glyphicon glyphicon-trash"></i></a></td>');
    var $removedFormTr = $('<tr class="not-count"><td colspan="6">Member was marked for removal, save changes for confirmation. <a href="#">Undo</a></td></tr>');
    $memberFormTr.append($removeFormA);

    $removeFormA.on('click', function (e) {
        e.preventDefault();
        $memberFormTr.children().last().remove();
        $removedFormTr.data('backup', $memberFormTr);
        $memberFormTr.replaceWith($removedFormTr);
        addNumberOfRows();
    });

    $removedFormTr.on('click', function (e) {
        e.preventDefault();
        $removedFormTr.replaceWith($removedFormTr.data('backup'));
        addNumberOfRows();
        addMemberFormDeleteLink($memberFormTr);
    })
}

function addNumberOfRows() {
    var table = document.getElementsByTagName('tbody')[0],
        rows = table.getElementsByTagName('tr'),
        text = 'textContent' in document ? 'textContent' : 'innerText';
    var j = 1;
    for (var i = 0, len = rows.length; i < len - 1; i++) {
        if (!(hasClass(rows[i], 'not-count'))) {
            rows[i].children[0][text] = j;
            j++;
        }
    }
}


