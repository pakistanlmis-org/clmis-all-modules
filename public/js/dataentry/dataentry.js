var form_clean;
$(document).ready(function () {

    form_clean = $("#frmF7").serialize();

    // Auto Save function call
    //setInterval('autoSave()', 20000);

    $('input[type="text"]').each(function () {
        if ($(this).val() == '')
        {
            $(this).val(0);
        }
    });

    $('input[type="text"]').change(function (e) {
        if ($(this).val() == '')
        {
            $(this).val('0');
        }
    });
    $('input[type="text"]').focus(function (e) {
        if ($(this).val() == '0')
        {
            $(this).val('');
        }
    });
    $('input[type="text"]').focusout(function (e) {
        if ($(this).val() == '')
        {
            $(this).val('0');
        }
    });
    $('input[type="text"]').keydown(function (e) {
        if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
            e.preventDefault();         // Prevent character input
        } else {
            var n = e.keyCode;
            if (!((n == 8)              // backspace
                    || (n == 9)                // Tab
                    || (n == 46)                // delete
                    || (n >= 35 && n <= 40)     // arrow keys/home/end
                    || (n >= 48 && n <= 57)     // numbers on keyboard
                    || (n >= 96 && n <= 105))   // number on keypad
                    ) {
                e.preventDefault();     // Prevent character input
            }
        }
    });
});

function formvalidate1()
{
    $('#saveBtn').attr('disabled', false);
    $('#errMsg').hide();
    var itmLength = $("input[name^='flitmrec_id']").length;
    var itmArr = $("input[name^='flitmrec_id']");
    var FLDOBLAArr = $("input[name^='FLDOBLA']");
    var FLDRecvArr = $("input[name^='FLDRecv']");
    var FLDIsuueUPArr = $("input[name^='FLDIsuueUP']");
    var FLDCBLAArr = $("input[name^='FLDCBLA']");
    var FLDReturnToArr = $("input[name^='FLDReturnTo']");
    var FLDUnusableArr = $("input[name^='FLDUnusable']");
    /*
     var fieldval = document.frmaddF7.itmrec_id[i].value;
     fieldconcat = fieldval.split('-');
     var whobla = 'WHOBLA'+fieldconcat[1];
     var whrecv = 'WHRecv'+fieldconcat[1];
     var whissue = 'IsuueUP'+fieldconcat[1];
     var fldobla = 'FLDOBLA'+fieldconcat[1];
     var fldrecv = 'FLDRecv'+fieldconcat[1];
     var fldissue = 'FLDIsuueUP'+fieldconcat[1];
     */
    for (i = 0; i < itmLength; i++)
    {
        itm = itmArr.eq(i).val();
        var itmInfo = itm.split('-');
        itmId = itmInfo[1];
        var FLDOBLA = parseInt(FLDOBLAArr.eq(i).val());
        var FLDRecv = parseInt(FLDRecvArr.eq(i).val());
        var FLDIsuueUP = parseInt(FLDIsuueUPArr.eq(i).val());
        var FLDCBLA = parseInt(FLDCBLAArr.eq(i).val());
        var FLDReturnTo = parseInt(FLDReturnToArr.eq(i).val());
        var FLDUnusable = parseInt(FLDUnusableArr.eq(i).val());



        if ((FLDIsuueUP + FLDUnusable) > (FLDOBLA + FLDRecv + FLDReturnTo))
        {
            alert('Invalid Closing Balance.\nClosing Balance = Opening Balance + Received + Adjustment(+) - Issued -  Adjustment(-)');
            FLDOBLAArr.eq(i).css('background', '#F45B5C');
            FLDRecvArr.eq(i).css('background', '#F45B5C');
            FLDIsuueUPArr.eq(i).css('background', '#F45B5C');
            FLDCBLAArr.eq(i).css('background', '#F45B5C');
            FLDReturnToArr.eq(i).css('background', '#F45B5C');
            FLDUnusableArr.eq(i).css('background', '#F45B5C');
            return false;
        }
    }
    $('#saveBtn').attr('disabled', true);
    $("#eMsg").html('Saving...');
    $('body').addClass("loading");
    $.ajax({
        url: 'data_entry_hf_action.php',
        data: $('#frmF7').serialize(),
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            $('body').removeClass("loading");
            if (data.resp == 'err')
            {
                $('#errMsg').html(data.msg).show();
            } else if (data.resp == 'ok')
            {
                function RefreshParent() {
                    if (window.opener != null && !window.opener.closed) {
                        window.opener.location.reload();
                    }
                }
                window.close();
                RefreshParent();
            }
        }
    })
}
function roundNumber(num, dec)
{
    var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
    return result;
}

function cal_balance(itemId)
{
    var y = document.getElementsByClassName('FLDSrcs' + itemId);
    var i;
    var total = 0;
    var totalvar = false;
    for (i = 0; i < y.length; i++) {
        total += parseInt(y[i].value) || 0;
        totalvar = true;
    }

    if (totalvar) {
        document.getElementById('FLDRecv' + itemId).value = (total);
    }

    if (document.getElementById('WHOBLA' + itemId))
        var wholba = (document.getElementById('WHOBLA' + itemId).value == "") ? 0 : parseInt(document.getElementById('WHOBLA' + itemId).value);
    else
        var wholba = 0;
    if (document.getElementById('WHRecv' + itemId))
        var WHRecv = (document.getElementById('WHRecv' + itemId).value == "") ? 0 : parseInt(document.getElementById('WHRecv' + itemId).value);
    else
        var WHRecv = 0;
    if (document.getElementById('IsuueUP' + itemId))
        var IsuueUP = (document.getElementById('IsuueUP' + itemId).value == "") ? 0 : parseInt(document.getElementById('IsuueUP' + itemId).value);
    else
        var IsuueUP = 0;
    //WH adj+
    if (document.getElementById('ReturnTo' + itemId))
        var ReturnTo = (document.getElementById('ReturnTo' + itemId).value == "") ? 0 : parseInt(document.getElementById('ReturnTo' + itemId).value);
    else
        var ReturnTo = 0;
    //WH adj-
    if (document.getElementById('Unusable' + itemId))
        var Unusable = (document.getElementById('Unusable' + itemId).value == "") ? 0 : parseInt(document.getElementById('Unusable' + itemId).value);
    else
        var Unusable = 0;
    if (document.getElementById('FLDOBLA' + itemId))
        var fldolba = (document.getElementById('FLDOBLA' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDOBLA' + itemId).value);
    else
        var fldolba = 0;
    if (document.getElementById('FLDRecv' + itemId))
        var FLDRecv = (document.getElementById('FLDRecv' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDRecv' + itemId).value);
    else
        var FLDRecv = 0;
    if (document.getElementById('FLDIsuueUP' + itemId))
        var FLDIsuueUP = (document.getElementById('FLDIsuueUP' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDIsuueUP' + itemId).value);
    else
        var FLDIsuueUP = 0;
    /*if(document.getElementById('FLDmyavg'+itemId))	
     var FLDmyavg = (document.getElementById('FLDmyavg'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDmyavg'+itemId).value);
     else
     var FLDmyavg = 0;*/
    //Fld adj+
    if (document.getElementById('FLDReturnTo' + itemId))
        var FLDReturnTo = (document.getElementById('FLDReturnTo' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDReturnTo' + itemId).value);
    else
        var FLDReturnTo = 0;
    //Fld adj-
    if (document.getElementById('FLDUnusable' + itemId))
        var FLDUnusable = (document.getElementById('FLDUnusable' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDUnusable' + itemId).value);
    else
        var FLDUnusable = 0;
    /*if(document.getElementById('FLDmyavg'+itemId))
     {
     var myavg = document.getElementById('FLDmyavg'+itemId).value;
     }
     else {
     var myavg = document.getElementById('myavg'+itemId).value;
     }
     var mycalavg = myavg.split('-');
     if(document.getElementById('FLDIsuueUP'+itemId))
     var divisible = parseInt(mycalavg[1]+FLDIsuueUP);
     else
     var divisible = parseInt(mycalavg[1]+IsuueUP);
     var divider = parseInt(mycalavg[0]+1);
     if(parseInt(divider)>0)
     {
     var myactualavg = parseInt(divisible)/parseInt(divider);
     }
     else {
     var myactualavg = parseInt(divisible)/1;
     }*/
    if (document.getElementById('WHCBLA' + itemId))
        document.getElementById('WHCBLA' + itemId).value = (wholba + WHRecv + ReturnTo) - (IsuueUP + Unusable);
    if (document.getElementById('MOS' + itemId) && document.getElementById('WHCBLA' + itemId))
    {
        if (parseInt(myactualavg) > 0)
        {
            document.getElementById('MOS' + itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA' + itemId).value) / parseInt(myactualavg), 1);
        } else {
            document.getElementById('MOS' + itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA' + itemId).value) / 1, 1);
        }
    }
    if (document.getElementById('FLDCBLA' + itemId))
        document.getElementById('FLDCBLA' + itemId).value = (fldolba + FLDRecv + FLDReturnTo) - (FLDIsuueUP + FLDUnusable);
    if (document.getElementById('FLDMOS' + itemId) && document.getElementById('FLDCBLA' + itemId))
    {
        if (parseInt(myactualavg) > 0)
        {
            document.getElementById('FLDMOS' + itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA' + itemId).value) / parseInt(myactualavg), 1);
        } else {
            document.getElementById('FLDMOS' + itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA' + itemId).value) / 1, 1);
        }
    }
}