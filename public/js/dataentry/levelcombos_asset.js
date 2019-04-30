$(function(){
    $('#office').change(function(){
        $('#combo1div').remove();
        $('#combo2div').remove();
        $('#warehousediv').remove();

        $('#loader').show();
        $.ajax({
            type: "POST",
            url: basePath+"js/dataentry/levelcombos_asset_action.php",
            data: {office: $(this).val()},
            dataType: 'html',
            success: function(data){
                $('#loader').hide();
                $('#officecombo').after(data);
            }
        });
    });
});