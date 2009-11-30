<?php require_once('../../../config.php'); ?>

function parentseve_teachersearch(val) {
	options = YAHOO.util.Dom.getChildren(YAHOO.util.Dom.get('addselect'));
    for (i = 0, j = options.length; i < j; i++) {
        var text = new RegExp(val, 'i');
        if(val == "" || text.test(options[i].innerHTML)) {
            YAHOO.util.Dom.setStyle(options[i], 'display', 'block');
        } else {
            YAHOO.util.Dom.setStyle(options[i], 'display', 'none');
        }
    }
}

function parentseve_addteachers() {
    var addselect = YAHOO.util.Dom.get('addselect');
    var removeselect = YAHOO.util.Dom.get('removeselect');
    var options = addselect.options;
    var selected = new Array();
    var idfield = YAHOO.util.Dom.get('id_teachers');
    var ids = idfield.value.split(',');

    for (var i = 0, j = ids.length; i < j; i++) {
    // Clear any empty values from the array
        if (ids[i] == '' || ids[i] == undefined) {
            ids.splice(i,1);
        }
    }

    for (var i = 0, j = options.length; i < j; i++) {
    // Add the selected options to an array
        if (options[i].selected) {
            selected.push(options[i]);
        }
    }

    if (removeselect.options[0] && removeselect.options[0].innerHTML == '') {
        // Remove any blank options from removeselect
        removeselect.removeChild(removeselect.options[0]);
    }

    for (i = 0, j = selected.length; i < j; i++) {
        // Move the selected options from the addselect to the removeselect, and add the ids to an array
        addselect.removeChild(selected[i]);
        removeselect.appendChild(selected[i]);
        ids.push(selected[i].value);
    }

    termreview_parentseve_sortselect(removeselect); // Sort the remove select
    idsvalue = ids.join(',')
    idfield.value = idsvalue; // Save the ids to a hidden field

}

function parentseve_removeteachers() {
    var addselect = YAHOO.util.Dom.get('addselect');
    var removeselect = YAHOO.util.Dom.get('removeselect');
    var options = removeselect.options;
    var selected = new Array();
    var idfield = YAHOO.util.Dom.get('id_teachers');
    var ids = idfield.value.split(',');

    for (var i = 0, j = options.length; i < j; i++) {
        if (options[i].selected) {
            selected.push(options[i]);
        }
    }

    for (var i = 0, j = selected.length; i < j; i++) {
        removeselect.removeChild(selected[i]);
        addselect.appendChild(selected[i]);
        for (var k = 0, l = ids.length; k < l; k++) {
            if (ids[k] == selected[i].value) {
                ids.splice(k, 1);
            }
        }
    }

    parentseve_sortselect(addselect);

    for (var i = 0, j = ids.length; i < j; i++) {
    // Clear any empty values from the array
        if (ids[i] == '' || ids[i] == undefined) {
            ids.splice(i,1);
        }
    }

    idsvalue = ids.join(',')
    idfield.value = idsvalue;

}

function parentseve_sortselect(select) {
    var items = select.childNodes;
    if (items.length) {
        var itemsArr = [];
        for (var i = 0, j = items.length; i < j; i++) {
            if (items[i].nodeType == 1) { // get rid of the whitespace text nodes
                itemsArr.push(items[i]);
            }
        }

        itemsArr.sort(function(a, b) {
          return a.innerHTML == b.innerHTML
                  ? 0
                  : (a.innerHTML > b.innerHTML ? 1 : -1);
        });

        for (i = 0; i < itemsArr.length; ++i) {
          select.appendChild(itemsArr[i]);
        }
    }
}