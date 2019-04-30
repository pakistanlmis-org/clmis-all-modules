$(window).load(function() {

    map = new OpenLayers.Map('map', {
        projection: new OpenLayers.Projection("EPSG:900913"),
        displayProjection: new OpenLayers.Projection("EPSG:4326"),
        maxExtent: restricted,
        restrictedExtent: restricted,
        maxResolution: "auto",
        allOverlays: true,
        controls: [
            new OpenLayers.Control.Navigation({
                'zoomWheelEnabled': false
            }),
            new OpenLayers.Control.MousePosition({
                prefix: 'coordinates: ',
                numDigits: 2,
                separator: ' | '
            }),
            new OpenLayers.Control.Zoom({
                zoomInId: "customZoomIn",
                zoomOutId: "customZoomOut"
            }),
            new OpenLayers.Control.ScaleLine()
        ]
    });

    province = new OpenLayers.Layer.Vector(
        "Provinces", {
            protocol: new OpenLayers.Protocol.HTTP({
                url: basePath+"js/maps/province.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")
                })
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: province_style_label,
            isBaseLayer: true
        });

    district = new OpenLayers.Layer.Vector(
        "District", {
            protocol: new OpenLayers.Protocol.HTTP({
                url: basePath+"js/maps/district.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:900913"),
                    externalProjection: new OpenLayers.Projection("EPSG:900913")
                })
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: district_style
        });

    cLMIS = new OpenLayers.Layer.Vector("Consumption", {
        styleMap: clMIS_style
    });
    map.addLayers([province, district, cLMIS]);
    district.setZIndex(900);
    province.setZIndex(1001);

    selectfeature = new OpenLayers.Control.SelectFeature([cLMIS]);
    map.addControl(selectfeature);
    selectfeature.activate();
    selectfeature.handlers.feature.stopDown = false;

    cLMIS.events.on({
        "featureselected": onFeatureSelect,
        "featureunselected": onFeatureUnselect
    });

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

    clearData();
    mapTitle();
    onFeatureUnselect();
    year = $("#year_sel").val();
    month = $('#slider').slider("option", "value");
    sector = $("#sector").val();
    stk = $("#stk_sel").val();
    var province = $("#prov_sel").val();
    product = $("#prod_sel").val();
    consumption_type = $("#type_sel").val();

    $.ajax({
        url: appPath+"maps/api/get-c-amc-map-data.php",
        type: "GET",
        data: {
            year: year,
            month: month,
            sector: sector,
            stk: stk,
            province: province,
            product: product,
            type: consumption_type
        },
        dataType: "json",
        success: callback,
        error: function(response) {
            alert("No Data Available");
            $("#loader").css("display", "none");
            return;
        }
    });

    function callback(response) {
        data = response;
        for (var i = 0; i < data.length; i++) {
            maxValue.push(Number(data[i].consumption));
        }
        max = Math.max.apply(Math, maxValue);
        min = Math.min.apply(Math, maxValue);
        getLegend('3', max, min, type_name);
    }
}




function drawLayer() {
    if (cLMIS.features.length > 0) {
        cLMIS.removeAllFeatures();
    }
    FilterData();
    if (data.length <= 0) {
        alert("No Data Available");
        $("#loader").css("display", "none");
        return;
    }
    data.sort(SortByID);
    for (var i = 0; i < data.length; i++) {
        chkeArray(data[i].district_id, data[i].mapping_id, Number(data[i].consumption));
    }
    drawGrid();
    districtCountGraph();
}

function chkeArray(district_id, mapping_id, value) {
    for (var i = 0; i < district.features.length; i++) {
        if (district_id == district.features[i].attributes.district_id) {
            if (min == max) {
                cLMISMiniLayer(district.features[i].geometry, district.features[i].attributes.province_id, district.features[i].attributes.province_name, product_name, StkHolder, mapping_id, district.features[i].attributes.district_name, value);
                break;
            } else {
                cLMISLayer(district.features[i].geometry, district.features[i].attributes.province_id, district.features[i].attributes.province_name, product_name, StkHolder, mapping_id, district.features[i].attributes.district_name, value);
                break;
            }
        }
    }
}

function cLMISLayer(wkt, province_id, province, product, StkHolder, district_id, district, value) {

    feature = new OpenLayers.Feature.Vector(wkt);
  
    if (value == parseInt(classesArray[0].start_value) && value == parseInt(classesArray[0].end_value)) {
        color = classesArray[0].color_code;
        NoData = Number(NoData) + 1;
        status = classesArray[0].description;

    }
    if (value > parseInt(classesArray[1].start_value) && value <= parseInt(classesArray[1].end_value)) {
        color = classesArray[1].color_code;
        class1 = Number(class1) + 1;
        status = classesArray[1].description;
    }
    if (value > parseInt(classesArray[2].start_value) && value <= parseInt(classesArray[2].end_value)) {
        color = classesArray[2].color_code;
        class2 = Number(class2) + 1;
        status = classesArray[2].description;
    }
    if (value > parseInt(classesArray[3].start_value) && value <= parseInt(classesArray[3].end_value)) {
        color = classesArray[3].color_code;
        class3 = Number(class3) + 1;
        status = classesArray[3].description;
    }
    if (value > parseInt(classesArray[4].start_value) && value <= parseInt(classesArray[4].end_value)) {
        color = classesArray[4].color_code;
        class4 = Number(class4) + 1;
        status = classesArray[4].description;
    }
    if (value > parseInt(classesArray[5].start_value) && value <= parseInt(classesArray[5].end_value)) {
        color = classesArray[5].color_code;
        class5 = Number(class5) + 1;
        status = classesArray[5].description;
    }
    feature.attributes = {
        district_id: district_id,
        province_id: province_id,
        district: district,
        province: province,
        product: product,
        StkHolder: StkHolder,
        status:status,
        value: value,
        color: color
    };
    cLMIS.addFeatures(feature);
    $("#loader").css("display", "none");
}

function cLMISMiniLayer(wkt, province_id, province, product, StkHolder, district_id, district, value) {
    feature = new OpenLayers.Feature.Vector(wkt);
    if (value == parseInt(classesArray[0].start_value) && value == parseInt(classesArray[0].end_value)) {
        color = classesArray[0].color_code;
        NoData = Number(NoData) + 1;
    } else {
        color = classesArray[1].color_code;
        class1 = Number(class1) + 1;
    }
       feature.attributes = {
        district_id: district_id,
        province_id: province_id,
        district: district,
        province: province,
        product: product,
        StkHolder: StkHolder,
        status:status,
        value: value,
        color: color
    };
        cLMIS.addFeatures(feature);
        $("#loader").css("display", "none");
}


function onFeatureSelect(e) {
    $("#c_title").html($("#type_sel option:selected").text());
    $("#prov").html(e.feature.attributes['province']);
    $("#district").html(e.feature.attributes['district']);
    $("#stakeholder").html(e.feature.attributes['StkHolder']);
    $("#product").html(e.feature.attributes['product']);
    $("#amc").html(e.feature.attributes['value'].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    lastMonthsStats(e.feature.attributes['district_id'], e.feature.attributes['district']);
}

function onFeatureUnselect(e) {
    $("#prov").html("");
    $("#district").html("");
    $("#stakeholder").html("");
    $("#product").html("");
    $("#amc").html("");
}

function mapTitle() {

    product_name = $("#prod_sel option:selected").text();
    StkHolder = $("#stk_sel option:selected").text();
    if (StkHolder == "All" && sector == "0") {
        StkHolder = $("#sector option:selected").text();
    } else if (StkHolder == "All" && sector == "1") {
        StkHolder = $("#sector option:selected").text();
    } else {}
    prov_name = $("#prov_sel option:selected").text();
    year_name = $("#year_sel option:selected").text();
    month_value = ($('#slider').slider("option", "value")) - 1;
    type_name = $("#type_sel option:selected").text();
    var month_name = monthNames[month_value];
    month_year = month_name + " " + year_name;
    if(prov_name == "All"){ prov_name = "Pakistan";}
    download = StkHolder+"->"+product_name+"->"+month_year;
    if(type_name == "Consumption"){
        $("#mapTitle").html("<font color='green' size='4'><b>" + type_name + " (" + month_name + " " + year_name + ")</b></font> <br/> " + StkHolder + " " + product_name);
    }
    else{
        $("#mapTitle").html("<font color='green' size='4'><b>" + type_name + "<br/> (" + month_name + " " + year_name + ")</b></font> <br/> " + StkHolder + " " + product_name);
    }
   
    var date = new Date();
    var d = date.getDate();
    var day = (d < 10) ? '0' + d : d;
    var m = date.getMonth() + 1;
    var month = (m < 10) ? '0' + m : m;
    var yy = date.getYear();
    var year = (yy < 1000) ? yy + 1900 : yy;

    var printdate = "Printed Date: " + day + "/" + month + "/" + year;
    $("#printedDate").html("<b>" + printdate + "</b>");
}


function drawGrid() {
    $("#attributeGrid").html("");
    $("#districtRanking").html("");
    dataDownload.length = 0;
    jsonData.length = 0;
    var features = cLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th align='center'>StakeHolder</th><th align='center'>" + amctype + "</th><th></th></thead>";
    for (var i = 0; i < features.length; i++) {
        table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td align='center'>" + features[i].attributes.StkHolder + "</td><td align='right'>" + features[i].attributes.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
        jsonData.push({
            label: features[i].attributes.district,
            value: features[i].attributes.value,
            color: features[i].attributes.color
        });
        if (amctype == "Consumption") {
            dataDownload.push({
                province: features[i].attributes.province,
                district_name: features[i].attributes.district,
                Stakeholder: features[i].attributes.StkHolder,
                product: features[i].attributes.product,
                Status: features[i].attributes.status,
                Consumption: features[i].attributes.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            });
        } else {
            dataDownload.push({
                province: features[i].attributes.province,
                district_name: features[i].attributes.district,
                Stakeholder: features[i].attributes.StkHolder,
                product: features[i].attributes.product,
                Status: features[i].attributes.status,
                Avg_monthly_consumption: features[i].attributes.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            });
        }

    }
    table += "</table>";
    $("#attributeGrid").append(table);
    maximum = cLMIS.features.length;
    districtRanking(jsonData,"");
}

function districtRanking(Data,title) {
    
    Data.sort(SortByRankingID);
    if (Data.length > 100) {
        width = '480%';
    } else {
        width = '180%';
    }

    var revenueChart = new FusionCharts({
        type: 'column2D',
        renderAt: 'chart-container',
        width: width,
        height: '100%',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - District wise " + amctype + " Ranking "+title,
                "subcaption": download,
                "yAxisName": amctype,
                "slantLabels": '1',            
                "showValues": '1',
                "rotateValues": '1',
                "placeValuesInside": '1',
                "adjustDiv":'0',
                "numDivLines":'3',
                "xAxisName": "",
                "exportEnabled": "1",
                "theme": "fint"
            },
            "data": Data
        }
    });
    revenueChart.render("districtRanking");
}


function districtCountGraph() {

    var ND = CalculatePercent(NoData, maximum);
    var cls1 = CalculatePercent(class1, maximum);
    var cls2 = CalculatePercent(class2, maximum);
    var cls3 = CalculatePercent(class3, maximum);
    var cls4 = CalculatePercent(class4, maximum);
    var cls5 = CalculatePercent(class5, maximum);

   if (min == max) { 
       if(min == "0" && max == "0"){
                pieArray.push({
                  label: classesArray[0].description,
                  value: ND,
                  color: classesArray[0].color_code
              }); 
       }else{ 
                pieArray.push({
                    label: classesArray[1].description,
                    value: cls1,
                    color: classesArray[1].color_code
                });
        }
    } else {
        pieArray.push({
            label:'No Data',
            value: ND,
            color: classesArray[0].color_code
        });
        pieArray.push({
            label: classesArray[1].description,
            value: cls1,
            color: classesArray[1].color_code
        });
        pieArray.push({
            label: classesArray[2].description,
            value: cls2,
            color: classesArray[2].color_code
        });
        pieArray.push({
            label: classesArray[3].description,
            value: cls3,
            color: classesArray[3].color_code
        });
        pieArray.push({
            label: classesArray[4].description,
            value: cls4,
            color: classesArray[4].color_code
        });
        pieArray.push({
            label: classesArray[5].description,
            value: cls5,
            color: classesArray[5].color_code
        });
    }

    var revenueChart = new FusionCharts({
        type: 'pie2D',
        renderAt: 'chart-container',
        width: '100%',
        height: '240px',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+"-"+amctype + " Status",
                "subcaption": download,
                "showLabels": "0",
                "showlegend":"1",
                "slantLabels": '1',
                "enableLink": '1',
                "showValues": '1',
                "xAxisName": "",
                "numberSuffix": "%",
                "yAxisName": "District Count",
                "exportEnabled": "1",
                "theme": "fint"
            },
            "data": pieArray
        }
    });
    revenueChart.render("pie");
}


function clearData() {
    $("#loader").css("display", "block");
    $('.radio-button').prop('checked', false);
    classesArray.length = 0;
    maxValue.length = 0;
    pieArray.length = 0;
    $("#legend").html("");
    $("#mapTitle").html("");
    $("#legendDiv").css("display", "none");
    $("#graph").html("Click any district for Previous Consumption Status");
    NoData = '0';
    DataProblem = '0';
    class1 = '0';
    class2 = '0';
    class3 = '0';
    class4 = '0';
    class5 = '0';
    amctype = $("#type_sel option:selected").text();
    if(amctype == "Consumption"){amctype = $("#type_sel option:selected").text();}
    else{amctype = "AMC";}
}


function SortByID(x, y) {
    return x.consumption - y.consumption;
}

function SortByRankingID(x, y) {
    return y.value - x.value;
}

function lastMonthsStats(district_id, district_name) {
    $("#graph").html("");
    $.ajax({
        url: appPath+"maps/api/get-district-chart.php",
        type: "GET",
        data: {
            year: year,
            month: month,
            stk: stk,
            sector: sector,
            product: product,
            district_id: district_id,
            type: consumption_type
        },
        dataType: "json",
        success: callback
    });

    function callback(response) {

        var chart = [];
        chart = response;
        minMaxArray.length = 0;

        for (var i = 0; i < chart.length; i++) {
            minMaxArray.push(Number(chart[i].value));
        }
        maximumValue = Math.max.apply(Math, minMaxArray);
        minimumValue = Math.min.apply(Math, minMaxArray);
        var minMaxPercent = (maximumValue * 5 / 100);
        maximumValue = Math.round(maximumValue + minMaxPercent);
        minimumValue = Math.round(minimumValue - minMaxPercent);
        if(minimumValue < 0){minimumValue = 0;}
        
        var revenueChart = new FusionCharts({
            type: 'line',
            width: '100%',
            height: '200px',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": district_name+" - Previous "+amctype + " Status",
                    "subcaption": StkHolder+"->"+product_name,
                    "yAxisName": amctype,
                    "exportEnabled": "1",
                    "enableLink": '1',
                    "yAxisMaxValue": maximumValue,
                    "yAxisMinValue": minimumValue,
                    "formatnumberscale": "1",
                    "showYAxisValues":'1',
                    "adjustDiv":"0",
                    "numDivLines": "3",
                    "decimals":"1",
                    "theme": "fint"
                },
                "data": chart
            }
        });
        revenueChart.render("graph");
    }
}

function gridFilter(color) {
    $("#attributeGrid").html("");
    dataDownload.length = 0;
    var features = cLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th align='center'>StakeHolder</th><th align='center'>" + amctype + "</th><th></th></thead>";
    for (var i = 0; i < features.length; i++) {
        if (features[i].attributes.color == color) {
            table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td align='center'>" + features[i].attributes.StkHolder + "</td><td align='right'>" + features[i].attributes.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
            if (amctype == "Consumption") {
                dataDownload.push({
                    province: features[i].attributes.province,
                    district_name: features[i].attributes.district,
                    Stakeholder: features[i].attributes.StkHolder,
                    product: features[i].attributes.product,
                    Status: features[i].attributes.status,
                    Consumption: features[i].attributes.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                });
            } else {
                dataDownload.push({
                    province: features[i].attributes.province,
                    district_name: features[i].attributes.district,
                    Stakeholder: features[i].attributes.StkHolder,
                    product: features[i].attributes.product,
                    Status: features[i].attributes.status,
                    Avg_monthly_consumption: features[i].attributes.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                });
            }
        }
    }
    table += "</table>";
    $("#attributeGrid").append(table);
}