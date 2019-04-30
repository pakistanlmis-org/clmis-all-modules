var map, province, district, cLMIS, product_name, min, max, StkHolder, selectfeature, handler, year;
var data = [];
var classesArray = [];
var maxValue = [];
var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];


var province_style = new OpenLayers.StyleMap();
var lookup = {
    "0": {
        fillColor: "#E6E7E9",
        strokeColor: "red",
        strokeWidth: 0.5,
        label: "${province_name}",
        pointerEvents: "visiblePainted",
        fontSize: "9px",
        cursor: "pointer",
        fontWeight: "bold",
        fillOpacity: 0
    },
    "1": {
        fillColor: "grey",
        strokeWidth: 1.2,
        strokeColor: "black",
        strokeOpacity: 1,
        fillOpacity: 0,
        label: "${province_name}",
        fontColor: "black",
        fontSize: "9px",
        fontWeight: "bold"
    }
}
province_style.addUniqueValueRules("default", "class", lookup);

var district_style = new OpenLayers.StyleMap({
    'default': {
        strokeColor: "grey",
        strokeOpacity: 1,
        strokeWidth: 0.2,
        fillColor: "white",
        fillOpacity: 0,
        pointerEvents: "visiblePainted",
        fontColor: "black",
        fontSize: "10px",
        fontWeight: "bold"
    }
});

var district_style_label = new OpenLayers.StyleMap({
    'default': {
        strokeColor: "grey",
        strokeWidth: 0.8,
        fillColor: "white",
        fillOpacity: 0,
        fontColor: "black",
        fontSize: "8px",
        fontWeight: "bold",
        cursor: "pointer",
        pointerEvents: "visiblePainted",
        label: "${district_name}"
    }
});

var style = OpenLayers.Util.applyDefaults({
    fillColor: "${color}",
    fontColor: "black",
    fontSize: "9px",
    fontWeight: "bold",
    strokeColor: "white",
    strokeWidth: 0.6,
    pointerEvents: "visiblePainted",
    fillOpacity: 1
}, OpenLayers.Feature.Vector.style['default']);


var style_select = OpenLayers.Util.applyDefaults({
    fillColor: "${color}",
    fillOpacity: 0.5,
    cursor: "pointer",
    strokeColor: "white",
    strokeWidth: 2,
    fontSize: "14px",
    label: "${district}\n CYP:${value}"
});


var vlMIS_style = new OpenLayers.StyleMap({
    "default": style,
    "temporary": style_select
});

var bounds = new OpenLayers.Bounds(60.13, 23.45, 80.03, 37.33);
bounds.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

var restricted = new OpenLayers.Bounds(60.13, 23.45, 80.03, 37.33);
restricted.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
OpenLayers.CANVAS_SUPPORTED = true;

$(window).load(function() {

    map = new OpenLayers.Map('map', {
        projection: new OpenLayers.Projection("EPSG:900913"),
        displayProjection: new OpenLayers.Projection("EPSG:4326"),
        maxExtent: restricted,
        restrictedExtent: restricted,
        maxResolution: "auto",
        controls: [
            new OpenLayers.Control.Navigation({
                'zoomWheelEnabled': false,
                'defaultDblClick': function(event) {
                    return;
                }
            }),
            new OpenLayers.Control.Zoom()
        ]
    });


    province = new OpenLayers.Layer.Vector(
        "Province", {
            protocol: new OpenLayers.Protocol.HTTP({
                url: basePath+"js/maps/province.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")
                })
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: province_style,
            renderers: ["Canvas"]
        });

    district = new OpenLayers.Layer.Vector(
        "District Label", {
            protocol: new OpenLayers.Protocol.HTTP({
                url: basePath+"js/maps/district.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")
                })
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: district_style,
            renderers: ["Canvas"]
        });

    cLMIS = new OpenLayers.Layer.Vector("CYP", {
        styleMap: vlMIS_style,
        isBaseLayer: true,
        renderers: ["Canvas"]
    });
    map.addLayers([cLMIS, province, district]);
    district.setZIndex(900);
    province.setZIndex(1001);

    selectfeature = new OpenLayers.Control.SelectFeature([cLMIS], {
        hover: true,
        renderIntent: "temporary",
        highlightOnly: true
    });
    map.addControl(selectfeature);
    selectfeature.activate();
    selectfeature.handlers.feature.stopDown = false;

    map.zoomToExtent(bounds);
    handler = setInterval(readData, 2000);
});

function readData() {
    if (province.features.length == "9" && district.features.length == "147") {
        getData();
        clearInterval(handler);
    }
}

function getData() {
    $("#loader").css("display", "block");
    $("#legendDiv").css("display", "none");
    $("#mapTitle").html("");
    classesArray.length = 0;
    maxValue.length = 0;
    $("#legend").html("");

    year = $("#year").val();
    var stk = $("#stk_sel").val();
    var province = $("#prov_sel").val();
    var product = $("#prod_sel").val();
    StkHolder = $("#stk_sel option:selected").text();
    mapTitle();

    $.ajax({
        url: appPath+"maps/api/get-cyp-generated.php",
        type: "GET",
        data: {
            year: year,
            stk: stk,
            province: province,
            product: product
        },
        dataType: "json",
        success: callback,
        error: function(response) {
            $("#loader").css("display", "none");
            return;
        }
    });

    function callback(response) {
        data = response;

        for (var i = 0; i < data.length; i++) {
            maxValue.push(Number(data[i].cyp));
        }

        max = Math.max.apply(Math, maxValue);
        min = Math.min.apply(Math, maxValue);
        getLegend('4', max, min, 'CYP');
    }
}

function drawLayer() {
    if (cLMIS.features.length > 0) {
        cLMIS.removeAllFeatures();
    }
    Filter();
    if (data.length <= 0) {
        $("#loader").css("display", "none");
        return;
    }
    for (var i = 0; i < data.length; i++) {
        chkeArray(data[i].district_id, data[i].district_name, Number(data[i].cyp));
    }

}

function chkeArray(district_id, district_name, cyp) {
    for (var i = 0; i < district.features.length; i++) {
        if (district_id == district.features[i].attributes.district_id) {
            if (min == max) {
                cLMISMiniLayer(district.features[i].geometry, district.features[i].attributes.province_name, product_name, StkHolder, district_id, district_name, cyp);
                break;
            } else {
                cLMISLayer(district.features[i].geometry, district.features[i].attributes.province_name, product_name, StkHolder, district_id, district_name, cyp);
                break;
            }
        }
    }
}


function cLMISLayer(wkt, province, product, StkHolder, id, district, value) {
    feature = new OpenLayers.Feature.Vector(wkt);

    if (value < parseInt(classesArray[0].end_value)) {
        color = classesArray[0].color_code;
    }
    if (value == parseInt(classesArray[1].start_value) && value == parseInt(classesArray[1].end_value)) {
        color = classesArray[1].color_code;
    }
    if (value > parseInt(classesArray[2].start_value) && value <= parseInt(classesArray[2].end_value)) {
        color = classesArray[2].color_code;
    }
    if (value > parseInt(classesArray[3].start_value) && value <= parseInt(classesArray[3].end_value)) {
        color = classesArray[3].color_code;
    }
    if (value > parseInt(classesArray[4].start_value) && value <= parseInt(classesArray[4].end_value)) {
        color = classesArray[4].color_code;
    }
    if (value > parseInt(classesArray[5].start_value) && value <= parseInt(classesArray[5].end_value)) {
        color = classesArray[5].color_code;
    }
    if (value > parseInt(classesArray[6].start_value) && value <= parseInt(classesArray[6].end_value)) {
        color = classesArray[6].color_code;
    }

    feature.attributes = {
        district: district,
        province: province,
        product: product,
        StkHolder: StkHolder,
        value: value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        color: color
    };
    cLMIS.addFeatures(feature);
    $("#loader").css("display", "none");
}

function cLMISMiniLayer(wkt, province, product, StkHolder, id, district, value) {
    feature = new OpenLayers.Feature.Vector(wkt);
    if (value < parseInt(classesArray[0].end_value)) {
        color = classesArray[0].color_code;
    } else if (value == parseInt(classesArray[1].start_value) && value == parseInt(classesArray[1].end_value)) {
        color = classesArray[1].color_code;

    } else {
        color = classesArray[2].color_code;
    }

    feature.attributes = {
        id: id,
        district: district,
        province: province,
        product: product,
        StkHolder: StkHolder,
        value: value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        color: color
    };
    cLMIS.addFeatures(feature);
    $("#loader").css("display", "none");
}


function mapTitle() {

    var month_name = monthNames[month - 1];
    product_name = $("#prod_sel option:selected").text();
    StkHolder = $("#stk_sel option:selected").text();
    prov_name = $("#prov_sel option:selected").text();

    $("#mapTitle").html("<font color='green' size='3'><b>CYP (" + year + ")</b></font> <br/><font size='2'><b> " + StkHolder + " " + product_name + "</b></font>");
}


function Filter() {

    var prov = $("#prov_sel option:selected").text();

    var features = province.features;
    var districtfeatures = district.features;

    for (var i = 0; i < features.length; i++) {
        features[i].style = '';
    }
    province.redraw();

    for (var i = 0; i < districtfeatures.length; i++) {
        districtfeatures[i].style = '';
    }
    district.redraw();

    if (prov == "All") {
        map.events.register("zoomend", map, zoomRestrict);
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
        map.events.register("zoomend", map, zoomRestrict);

        for (var i = 0; i < features.length; i++) {
            if (features[i].attributes.province_name != prov) {
                features[i].style = {
                    display: 'none'
                };

            } else {

                downloadExtent = features[i].geometry.getBounds();
                map.zoomToExtent(features[i].geometry.getBounds());
            }
        }

        if (features[3].attributes.province_name == prov) {
            map.events.remove("zoomend", map, zoomRestrict);

            var isb = new OpenLayers.Bounds(72.570797, 33.286844, 73.641923, 33.90042);
            isb.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
            downloadExtent = isb;
            map.zoomToExtent(isb);
        }


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