//////////////////////////////////////
function staff_autocomplete(elementId, projects) {
  $('#' + elementId)
    .autocomplete({
      minLength: 2,
      source: projects,
      focus: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        return false
      },
      select: function (event, ui) {
        $('#' + elementId + '_id').val(ui.item.id)
        $('#' + elementId).val(ui.item.label)
        $('#name').val(ui.item.name)
        $('#email').val(ui.item.email)
        $('#phone').val(ui.item.phone)
        return false
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li>')
      .append('<div>' + item.label + ' (' + item.name + ')</div>')
      .appendTo(ul)
  }
}

//////////////////////////////////////
function staffInOut_autocomplete(elementId, projects) {
  $('#' + elementId)
    .autocomplete({
      minLength: 2,
      source: projects,
      focus: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        return false
      },
      select: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        $('#' + elementId + '_id').val(ui.item.value)
        return false
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    if (item.desc) {
      return $('<li>')
        .append('<div>' + item.label + '</div>')
        .appendTo(ul)
    } else {
      return $('<li>')
        .append('<div>' + item.label + '</div>')
        .appendTo(ul)
    }
  }
}


//////////////////////////////////////
function materialDetail_autocomplete(materialsList, rowId) {
  $('#materialCode_' + rowId)
    .autocomplete({
      minLength: 2,
      source: materialsList,
      focus: function (event, ui) {
        $('#materialCode_' + rowId).val(ui.item.label)
        return false
      },
      select: function (event, ui) {
        $('#materialCode_' + rowId).val(ui.item.label)
        $('#materialId_' + rowId).val(ui.item.value)
        $('#unit_' + rowId).val(ui.item.unit)
        $('#type_' + rowId).val(ui.item.type)
        
        return false
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li>')
    .append('<div>' + item.label  + ' ('+ item.pcode + ')' + ' (' + item.unit + ')</div>')
      .appendTo(ul)
  }
}

//////////////////////////////////////
function product_autocomplete(elementId, projects) {
  $('#' + elementId)
    .autocomplete({
      minLength: 2,
      source: projects,
      focus: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        return false
      },
      select: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        $('#productName_vi').val(ui.item.desc)
        $('#unit_vi').val(ui.item.unit)
        
        return false
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li>')
      .append('<div>' + item.label +' ('+ item.desc + ')' + ' (' + item.unit + ')</div>')
      .appendTo(ul)
  }
}

//////////////////////////////////////
function material_autocomplete(elementId, projects) {
  $('#' + elementId)
    .autocomplete({
      minLength: 2,
      source: projects,
      focus: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        return false
      },
      select: function (event, ui) {
        $('#' + elementId + '_id').val(ui.item.id)
        $('#' + elementId).val(ui.item.label)
        $('#producerCode').val(ui.item.pcode)
        $('#crayolaName_vi').val(ui.item.desc)
        $('#producerName_vi').val(ui.item.pname)
        $('#unit_vi').val(ui.item.unit)
        
        return false
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li>')
      .append('<div>' + item.label  +' / '+ item.pcode + ' ('+ item.desc +' / '+ item.pname + ')' + ' (' + item.unit + ')</div>')
      .appendTo(ul)
  }
}

//////////////////////////////////////
function customer_autocomplete(elementId, projects) {
  $('#' + elementId)
    .autocomplete({
      minLength: 2,
      source: projects,
      focus: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        return false
      },
      select: function (event, ui) {
        $('#' + elementId).val(ui.item.label)
        $('#customer_id').val(ui.item.value)
        $('#address').val(ui.item.add)
        return false
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    if (item.add == '') {
      return $('<li>')
        .append('<div>' + item.label + '</div>')
        .appendTo(ul)
    } else {
      return $('<li>')
        .append(
          '<div>' +
            item.label +
            "<br><span style='margin-left:30px; color:gray;'>(" +
            item.add +
            ')</span></div>',
        )
        .appendTo(ul)
    }
  }
}

//////////////////////////////////////
function extends_autocomplete(elementId, projects) {
  var availableTags = projects
  function split(val) {
    return val.split(/,\s*/)
  }
  function extractLast(term) {
    return split(term).pop()
  }

  $('#' + elementId)
    // don't navigate away from the field on tab when selecting an item
    .on('keydown', function (event) {
      if (
        event.keyCode === $.ui.keyCode.TAB &&
        $(this).autocomplete('instance').menu.active
      ) {
        event.preventDefault()
      }
    })
    .autocomplete({
      minLength: 0,
      source: function (request, response) {
        // delegate back to autocomplete, but extract the last term
        response(
          $.ui.autocomplete.filter(availableTags, extractLast(request.term)),
        )
      },
      focus: function () {
        // prevent value inserted on focus
        return false
      },
      select: function (event, ui) {
        var terms = split(this.value);
        // remove the current input
        terms.pop();
        // add the selected item
        terms.push(ui.item.name);
        // add placeholder to get the comma-and-space at the end
        terms.push('');
        this.value = terms.join(', ');
        return false;
      },
    })
}
