var province_style = new OpenLayers.StyleMap();
var lookup = {
    "0": {
        fillColor: "#E6E7E9",
        strokeColor: "red",
        strokeWidth: 0.8,
        fillOpacity: 0,
        cursor: "pointer"
    },
    "1": {
        fillColor: "grey",
        strokeWidth: 1,
        strokeColor: "black",
        strokeOpacity: 1,
        fillOpacity: 0,
        cursor: "pointer"
    }
}
province_style.addUniqueValueRules("default", "class", lookup);

var province_style_label = new OpenLayers.StyleMap();
var lookup = {
    "0": {
        fillColor: "#E6E7E9",
        strokeColor: "red",
        strokeWidth: 0.8,
        label: "${province_name}",
        fontSize: "11px",
        cursor: "pointer",
        fontWeight: "bold",
        fillOpacity: 0
    },
    "1": {
        fillColor: "grey",
        strokeWidth: 1,
        strokeColor: "black",
        strokeOpacity: 1,
        fillOpacity: 0,
        cursor: "pointer",
        label: "${province_name}",
        labelOutlineColor: "white",
        labelOutlineWidth: 0,
        fontColor: "black",
        fontSize: "11px",
        fontWeight: "bold"
    }
}
province_style_label.addUniqueValueRules("default", "class", lookup);

 var district_style = new OpenLayers.StyleMap({
    'default': {
        strokeColor: "grey",
        strokeWidth: 0.5,
        fillColor: "white",
        cursor: "pointer",
        fillOpacity: 0,
        fontColor: "black",
        fontSize: "11px",
        fontWeight: "bold"
    }
});

var district_style_label = new OpenLayers.StyleMap({
    'default': {
        strokeColor: "grey",
        strokeWidth: 0.5,
        fillColor: "white",
        cursor: "pointer",
        fillOpacity: 0,
        fontColor: "black",
        fontSize: "11px",
        fontWeight: "bold",
        label: "${district_name}"
    }
});

var style = OpenLayers.Util.applyDefaults({
        fillColor: "${color}",
        fontColor: "black",
        fontSize: "10px",
        fontFamily: "Courier New, monospace",
        fontWeight: "bold",
        labelOutlineColor: "white",
        labelOutlineWidth: 1,
        cursor: "pointer",
        strokeColor: "white",
        strokeWidth: 0.2,
        pointerEvents: "visiblePainted",
        fillOpacity: 1
}, OpenLayers.Feature.Vector.style['default']);


var style_select = OpenLayers.Util.applyDefaults({
        fillColor: "${color}",
        fillOpacity: 0.5,
        cursor: "pointer",
        strokeColor: "white",
        strokeWidth: 0.5
});

var clMIS_style = new OpenLayers.StyleMap({
        "default": style,
        "select": style_select
});


var bounds = new OpenLayers.Bounds(60.427490875,23.5796738614,79.8889460875,37.2591913474);
bounds.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

var restricted = new OpenLayers.Bounds(60.427490875,23.5796738614,79.8889460875,37.2591913474);
restricted.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

OpenLayers.CANVAS_SUPPORTED = true;
OpenLayers.Layer.Vector.prototype.renderers = ["Canvas"];

var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var NoData = '0';
var NoStockOut = '0';
var DataProblem = '0';
var StockOut = '0';
var UnderStock = '0';
var Satisfactory = '0';
var OverStock = '0';
var class1 = '0';
var class2 = '0';
var class3 = '0';
var class4 = '0';
var class5 = '0';

var classesArray = [];
var jsonData = [];
var pieArray = [];
var dataDownload = [];
var data = [];
var maxValue = [];
var minMaxArray = [];

var map, province, district, cLMIS, year, month, stk, product, prov, min, max, consumption_type, product_name, level, StkHolder, sector, amctype, downloadExtent, selectfeature, handler, date_from, date_to, maximum, month_year, prov_name,status,d_title,download;