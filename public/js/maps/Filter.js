 function FilterData() {

     var prov = $("#prov_sel option:selected").text();

     var features = province.features;
     var districtfeatures = district.features;

     for (var i = 0; i < features.length; i++) {
         features[i].style = '';
     }
     for (var i = 0; i < districtfeatures.length; i++) {
         districtfeatures[i].style = '';
     }

     if (prov == "All") {
         map.events.register("zoomend", map, zoomChanged);
         map.events.register("zoomend", map, zoomRestrict);
         map.events.register("move", map, UpdateExtent);
         map.setOptions({
             maxExtent: restricted
         });
         map.setOptions({
             restrictedExtent: restricted
         });
         downloadExtent = bounds;
         map.zoomToExtent(bounds);
     } else {

         map.setOptions({
             maxExtent: null
         });
         map.setOptions({
             restrictedExtent: null
         });
         map.events.register("zoomend", map, zoomChanged);
         map.events.register("zoomend", map, zoomRestrict);
         map.events.register("move", map, UpdateExtent);

         for (var i = 0; i < features.length; i++) {
             if (features[i].attributes.province_name != prov) {
                 features[i].style = {
                     display: 'none'
                 };

             } else {
                 if (features[3].attributes.province_name == prov) {
                        map.events.remove("zoomend", map, zoomChanged);
                        map.events.remove("zoomend", map, zoomRestrict);
                        map.events.remove("move", map, UpdateExtent);
                        zoomExtent =  new OpenLayers.Bounds(8104088.7510095,3940971.3350609,8171789.9786361,4008017.0874545);
                 }
                 else{
                        zoomExtent = features[i].geometry.getBounds();
                 }
                        downloadExtent = zoomExtent;
                        map.zoomToExtent(zoomExtent);
             }
         }
         
//         alert(features[3].attributes.province_name+"=="+prov);
//         if (features[3].attributes.province_name == prov) {
//             map.events.remove("zoomend", map, zoomChanged);
//             map.events.remove("zoomend", map, zoomRestrict);
//             map.events.remove("move", map, UpdateExtent);
//
//             var isb = new OpenLayers.Bounds(8104088.7510095,3940971.3350609,8171789.9786361,4008017.0874545);
//             //isb.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
//             downloadExtent = isb;
//             map.zoomToExtent(isb);
//         }


         province.redraw();

         for (var i = 0; i < districtfeatures.length; i++) {
             if (districtfeatures[i].attributes.province_name != prov) {
                 districtfeatures[i].style = {
                     display: 'none'
                 };
             }
         }
        district.redraw();
     }

 }


 function zoomChanged() {
     var zoom = map.getZoom();
     if (zoom >= 1) {
         province.styleMap = province_style;
         district.styleMap = district_style_label;
         province.redraw();
         district.redraw();
     }
     if (zoom == "0") {
         province.styleMap = province_style_label;
         district.styleMap = district_style;
         province.redraw();
         district.redraw();
     }
 }

 var ext;

 function UpdateExtent() {
     ext = map.getExtent();
 }

 function zoomRestrict() {
     var x = map.getZoom();
     if (x == 3) {
         ext = map.getExtent();
     }
     if (x >= 3) {
         map.zoomToExtent(ext);
     }
 }

 $(function() {
     
     var date = new Date();
     var d = date.getDate();
     var day = (d < 10) ? '0' + d : d;
     
     if (day > 10) {  
             date.SubtractMonth(1);
             mon = date.getMonth() + 1 ;
     }else{
            date.SubtractMonth(2);
            mon = date.getMonth() + 1;
     }
     
     $("#slider").slider({
             value: mon,
             min: 1,
             max: 12,
             step: 1,
             change: function(event, ui) {
                 getData();
             }
         })
         .each(function() {

             // Get the options for this slider
             var opt = $(this).data().uiSlider.options;

             // Get the number of possible values
             var vals = opt.max - opt.min;

             // Space out values
             for (var i = 0; i <= vals; i++) {
                 var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                 var el = $('<label>' + monthNames[i] + '</label>').css('left', (i / vals * 100) + '%');

                 $("#slider").append(el);
             }

         });

     $('#sector').change(function(e) {
         var val = $('#sector').val();
         getStakeholder(val, '');
     });
     getStakeholder($('#sector').val());
     
     $('#stk_sel').change(function(e) {
         var val = $('#stk_sel').val();
         getProductByStk(val, '');
     });
 });


 function getStakeholder(val) {

     $.ajax({
         url: "api/ajax_stk.php",
         data: {
             type: val,
             
         },
         type: 'GET',
         success: function(data) {
             $('#stk_sel').html(data);
             getProductByStk($('#stk_sel').val());
             var pageTitle = $(".page-title").html();pageTitle = pageTitle.split("Map");var type = pageTitle[0].replace(/\s+/g,"");if(type == "StockOutFrequency"){$("#stk_sel option[value='all']").remove();} 
         }
     });
 }
 
 function getProductByStk(stk) {
    var pageTitle = $(".page-title").html();
    pageTitle = pageTitle.split("Map");
    var type = pageTitle[0].replace(/\s+/g,"");
     $.ajax({
         url: "api/ajax_Prod.php",
         data: {
             stk: stk,
             type:type
         },
         type: 'GET',
         success: function(data) {
             $('#prod_sel').html(data);
         }
     });
 }


 function CalculatePercent(value, total) {
     return 100 * (value / total);
 }
 
 Date.prototype.SubtractMonth = function(numberOfMonths) {
            var d = this;
            d.setMonth(d.getMonth() - numberOfMonths);
            d.setDate(1);
            return d;
 }