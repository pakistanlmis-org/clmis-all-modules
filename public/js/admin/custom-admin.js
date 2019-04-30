// JavaScript Document

$(function() {
    $("#go").click(function() {
        $("#de-admin").validate({
            rules: {
                wharehouse_id: 'required',
                month: 'required',
                year: 'required'
            },
            messages: {
                wharehouse_id: 'Select Wharehouse',
                month: 'Select Month',
                year: 'Select Year'
            }

        });
    });

});