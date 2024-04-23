//////////////////////////////////
function delete_record(table,msg1,msg2) {
    var act = confirm(msg1);
    rows = $('#items').val();
    var del_rows = '';
    if (act && rows > 1) {
        del_rows = $('#delete_items').val() + $('#idDetail_' + rows).val() + ',';
        document.getElementById(table).deleteRow($('#items').val());
        $('#items').val(rows - 1);
    } else if (act && rows == 1) {
        alert(msg2);
        return false;
    } else {
        return false;
    }
    $('#delete_items').val(del_rows);
};

//////////////////////////////////
function add_record(table) {
    var lastRow = $('#'+table).closest('table').find("tbody tr:last-child");
    var cloned = lastRow.clone();
    cloned.find('input,span').each(function() {
        var id = $(this).attr('id');
        var splitId = id.split('_');
        var arrLength = splitId.length;
        var newId = splitId[0] + "_" + (splitId[arrLength - 1] * 1 + 1);
        var setNewId = (splitId[arrLength - 1] * 1 + 1);

        $(this).attr('id', newId);
        $(this).attr('name', newId);
        $(this).attr('alt', setNewId);
        if ($(this).attr('id') == 'num_' + setNewId) {
            $(this).html(setNewId);
        }
        $(this).val('');
        if ($(this).attr('id') == 'percentage_' + setNewId) {
            $(this).focus();
        }
        $('#items').val(setNewId);
        // $(this).attr('id', newId);
        // $(this).attr('name', newId);
    });

    cloned.insertAfter(lastRow);
};
//////////////////////////////////
function change_enabled(id) {
    if ($('#changeActualStock_' + id).val() == 0 && $('#calculateType_' + id).val() == 1) {
        $('#changeActualStock_' + id).attr('value', 1);
        $('#actualStock_' + id).attr('disabled', false).focus();
    } else {
        $('#changeActualStock_' + id).attr('value', 0).prop('checked', false);
        $('#actualStock_' + id).attr('disabled', true);
    }
}
//////////////////////////////////
function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/gi, '$1,');
};

//////////////////////////////////
function get_value(value) {
    if (value != "") str = value;
    else str = "";
    return str;
}
//////////////////////////////////
function calculate_formula(id, ajaxUrl) {
    var qty = get_value($('#quantity_' + id).val());
    var p = get_value($('#price_' + id).val());
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            act: 'calculate_qty',
            quantity: qty,
            price: p
        },
        url: ajaxUrl,
        success: function(data) {
            $('#quantity_' + id).val(data.quantity);
            $('#price_' + id).val(data.price);
            $('#total_' + id).val(data.total);
            amount = amount_price();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'total_amount',
                    amount: amount
                },
                url: ajaxUrl,
                success: function(data) {
                    $('#amount').val(data.amount);
                },
                error: function(xhr, status, error) {
                    alert('Lỗi : ' + error + '|Data' + status + ' | ' + xhr);
                }
            });
        },
        error: function(xhr, status, error) {
            alert('Lỗi : ' + error + '|Data' + status + ' | ' + xhr);
        }
    });
}

//////////////////////////////////
function check_product_stock(productId, rowId, ajaxUrl, warehouse, lang) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: { act: 'stock', product_id: productId, date: $('#date').val(), wh: warehouse, lang: lang },
        url: ajaxUrl,
        success: function(data) {
            if (data.calculateType == 0) {
                $('#stock_' + rowId).val(data.stock);
            } else if (data.calculateType == 1) {
                $('#actualStock_' + rowId).val(data.actual);
                $('#stock_' + rowId).val(data.stock);
            } else if (data.calculateType == 2) {
                $('#stock_' + rowId).val(data.stock);
            }
        },
        error: function(xhr, status, error) {
            alert('Lỗi : ' + error + '| Data' + status + ' | ' + xhr);
        }
    });
}

//////////////////////////////////
function setInputFilter(textbox, inputFilter) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
        textbox.addEventListener(event, function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    });
}

function amount_price() {
    var amount = 0;
    var rows = $('#items').val();
    for (i = 1; i <= rows; i++) {
        var total = $('#total_' + i).val();
        if (isNaN(total)) total = total.replace(/,/gi, '');
        amount += total * 1;
    }
    return amount;
}

function check_number(id) {
    value = $('#' + id).val();

    setInputFilter(document.getElementById(id), function(value) {
        return /^\d*\.?\d*$/.test(value);
    });
}
//////////////////////////////////