var $collectionHolder;

// setup an "add a member" link
var $addMemberLink = $('<td colspan="6"><a href="#" class="add_member_link">Add a member</a></td>');
var $newLinkLi = $('<tr></tr>').append($addMemberLink);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of members
    $collectionHolder = $('tbody.members');

    // add a delete link to all of the existing member form li elements
    $collectionHolder.find('tr').each(function() {
        addMemberFormDeleteLink($(this));
    });

    // add the "add a member" anchor and li to the members ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addMemberLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new member form (see next code block)
        addMemberForm($collectionHolder, $newLinkLi);
    });
});



function addMemberForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a member" link li
    var $newFormLi = $('<tr></tr>').append(newForm);
    $newLinkLi.before($newFormLi);
    addMemberFormDeleteLink($newFormLi);
}



function addMemberFormDeleteLink($memberFormLi) {
    var $removeFormA = $('<td><a href="#">X</a></td>');
    $memberFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the member form
        $memberFormLi.remove();
    });
}