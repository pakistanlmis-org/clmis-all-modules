
var map,province,district,vLMIS,product_name;

var bounds =  new OpenLayers.Bounds(60.87860,37.08942,79.30735,23.69468);
bounds.transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("EPSG:900913"));

var restricted = new OpenLayers.Bounds(57.71,23.49,85.08,37.26);
restricted.transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("EPSG:900913"));

$(document).ready(function() {

   map = new OpenLayers.Map('map', {
        projection: new OpenLayers.Projection("EPSG:900913"),
        displayProjection: new OpenLayers.Projection("EPSG:4326"),
        maxExtent : restricted,
        restrictedExtent: restricted,
        maxResolution: "auto",
        controls: [
            new OpenLayers.Control.Navigation({'zoomWheelEnabled': false,'defaultDblClick': function (event) {return;}}),
            new OpenLayers.Control.MousePosition(),
            new OpenLayers.Control.Zoom(),
            new OpenLayers.Control.ScaleLine(),
            new OpenLayers.Control.LayerSwitcher(),
            new OpenLayers.Control.OverviewMap()
        ]
    });
    
   var earth = new OpenLayers.Layer.XYZ(
                "Map Guest",
                [
                    "http://tiles.naqsha.net/Tiles/${z}/${x}/${y}.png"
                ], {
                    sphericalMercator: true,
                    wrapDateLine: false,
                    isBaseLayer: true,
                    renderers: ["Canvas"]
                }
            );

    province = new OpenLayers.Layer.Vector(
        "Provinces",{
            protocol: new OpenLayers.Protocol.HTTP({
                url: basePath+"js/maps/province.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")})
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: province_style,
            isBaseLayer: false,
            renderers:["Canvas"]
        });

	district = new OpenLayers.Layer.Vector(
        "Districts",{
            protocol: new OpenLayers.Protocol.HTTP({
                url: basePath+"js/maps/district.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")})
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: district_style,
            isBaseLayer: false,
            renderers:["Canvas"]
        });

     vLMIS = new OpenLayers.Layer.Vector("Month Of Stock",{styleMap:vlMIS_style,isBaseLayer: false,renderers: ["Canvas"]});
     map.addLayers([earth,province,district,vLMIS]);

     var selectfeature  = new OpenLayers.Control.SelectFeature([vLMIS],{ hover: false,highlightOnly : false,renderIntent: "temporary"});
     map.addControl(selectfeature);
     selectfeature.activate();
     
     nav = new OpenLayers.Control.NavigationHistory({displayClass: 'olControlNavHistory'});
     map.addControl(nav);
   
     editPanel = new OpenLayers.Control.Panel({div:controls});
     editPanel.addControls([
        cMeasureLine = new OpenLayers.Control.DynamicMeasure(OpenLayers.Handler.Path ,{displayClass: 'olControlMeasure',title: 'Measure Distance'}), 
        cMeasureArea = new OpenLayers.Control.DynamicMeasure(OpenLayers.Handler.Polygon ,{displayClass: 'olControlMeasureArea',title: 'Measure Area'}), 
        nav.next,nav.previous,
        zb = new OpenLayers.Control.ZoomBox({displayClass: 'olControlZoomBox', title: 'Zoom Box'}),
        navigation = new OpenLayers.Control.Navigation({title:'Pan',displayClass:'olControlNavigation'})
      ]);

      map.addControl(editPanel);

    vLMIS.events.on({
        "featureselected": onFeatureSelect,
        "featureunselected": onFeatureUnselect
    });

    map.zoomToExtent(bounds);

    map.events.register("zoomend", map, zoomChanged);
    map.events.register("zoomend", map, zoomRestrict);
    province.setZIndex(800);
});

        $("#submit").click(function(){
            getData();
        });

        function getData()
        {
            var year     = $("#year").val();
            var month    = $("#month").val();
            var product  = $("#product").val();

            product_name = $("#product option:selected").text();
			
            var url = "year="+year+"&month="+month+"&product="+product;

            $.ajax({
                url: appPath+"maps/api/geo/get-mos-map-data",
                type:"GET",
                data : url,
                dataType:"json",
                success:callback
            });

            function callback(response)
            {
               var data = [];
               data = response;

            if(vLMIS1.features.length>0){vLMIS1.removeAllFeatures();}
            if(vLMIS2.features.length>0){vLMIS2.removeAllFeatures();}
            
                  for(var i=0;i<data.length;i++)
                  {
                      if(parseInt(matchArray[i].district_id) == parseInt(data[i].district_id))
                      {
                             vLMISdistrictLayer(matchArray[i].wkt,matchArray[i].province,matchArray[i].product,data[i].district_id,data[i].district_name,Math.round(data[i].mos));     
                             vLMISfieldLayer(matchArray[i].wkt,matchArray[i].province,matchArray[i].product,data[i].district_id,data[i].district_name,Math.round(data[i].fieldMOS));    
                      }
                  }
                   vLMIS.setVisibility(true);
                   vLMIS.setVisibility(true);
            }
        }



        function vLMISdistrictLayer(wkt,province,product,district_id,district_name,value)
        {
            feature = new OpenLayers.Feature.Vector(parser.read(wkt).geometry);

            if(value < districtMOSArray[0].end_value){
                color = districtMOSArray[0].color_code;
            }
            if(value == districtMOSArray[1].start_value && value == districtMOSArray[1].end_value ){
                color = districtMOSArray[1].color_code;
            }
            if(value > districtMOSArray[2].start_value && value <= districtMOSArray[2].end_value){
                color = districtMOSArray[2].color_code;
            }
            if(value > districtMOSArray[3].start_value && value <= districtMOSArray[3].end_value){
                color = districtMOSArray[3].color_code;
            }
            if(value > districtMOSArray[4].start_value && value <= districtMOSArray[4].end_value){
                color = districtMOSArray[4].color_code;
            }
            if(value > districtMOSArray[5].start_value){
                color = districtMOSArray[5].color_code;
            }

            feature.attributes = {district_id:district_id,district:district_name,province:province,product:product,value:value,color:color};
            vLMIS.addFeatures(feature);

        }




/////////////////////// PopUp Window function ////////////////////////////////////

        function onFeatureSelect(e)
        {
             var popuphtml="<div align='center' style='font-size: 10px'><table border='0'> <tr> <td align='left'><b>Province:</b></td><td colspan='2' align='center'><span>"+ e.feature.attributes['province']+"</td>  </tr> <tr> <td align='left'><b>District:</b></td><td colspan='2' align='center'><span>"+ e.feature.attributes['district']+"</td></tr> <tr> <td align='left' ><b>Product:</b></td><td align='center'>"+ e.feature.attributes['product']+"</td> </tr>  <tr> <td align='left' ><b>MOS:</b></td><td align='center'>"+ e.feature.attributes['value']+"</td></tr></table></div>";
             $("#info2").html(popuphtml);
        }

        function onFeatureUnselect(e)
        {
             $("#info2").html("");
        }

/////////////////////// End of PopUp Window functions ////////////////////////////////////

