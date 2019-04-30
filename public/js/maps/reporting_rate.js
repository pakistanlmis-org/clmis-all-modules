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
        "Province", {
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
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")
                })
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: district_style,
            isBaseLayer: false
        });

    cLMIS = new OpenLayers.Layer.Vector("Reporting Rate", {
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

    getLegend("6", 'Reporting Rate');
    handler = setInterval(readData, 2000);
});

function readData() {
    if (province.features.length == "9" && district.features.length == "147") {
        getData();
        clearInterval(handler);
    }
}

function getData() {
    emptyArray();
    onFeatureUnselect();

    year = $("#year_sel").val();
    month = $('#slider').slider("option", "value");
    stk = $("#stk_sel").val();
    sector = $("#sector").val();
    var province = $("#prov_sel").val();
    product = $("#prod_sel").val();

    titleGenerator();

    $.ajax({
        url: appPath+"maps/api/get-reporting-rate.php",
        type: "GET",
        data: {
            year: year,
            month: month,
            stk: stk,
            sector: sector,
            province: province,
            product: product
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
        var data = [];
        data = response;
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
            chkeArray(data[i].district_id, data[i].mapping_id, data[i].reported, data[i].total_warehouse, Number(data[i].reporting_rate));
        }
        drawGrid();
        districtCountGraph();
    }
}

function chkeArray(district_id, mapping_id, reported, total_warehouse, reporting_rate) {

    for (var i = 0; i < district.features.length; i++) {
        if (district_id == district.features[i].attributes.district_id) {
            cLMISLayer(district.features[i].geometry, district.features[i].attributes.province_name, product_name, StkHolder, mapping_id, district.features[i].attributes.district_name, reported, total_warehouse, reporting_rate);
            break;
        }
    }
}

function cLMISLayer(wkt, province, product, StkHolder, district_id, district_name, reported, total_warehouse, reporting_rate) {
    feature = new OpenLayers.Feature.Vector(wkt);
   
    if (reporting_rate == classesArray[0].start_value && reporting_rate == classesArray[0].end_value) {
        color = classesArray[0].color_code;
        NoData = Number(NoData) + 1;
        status = classesArray[0].description;
    }
    if (reporting_rate > classesArray[1].start_value && reporting_rate <= classesArray[1].end_value) {
        color = classesArray[1].color_code;
        class1 = Number(class1) + 1;
        status = classesArray[1].description;
    }
    if (reporting_rate > classesArray[2].start_value && reporting_rate <= classesArray[2].end_value) {
        color = classesArray[2].color_code;
        class2 = Number(class2) + 1;
        status = classesArray[2].description;
    }
    if (reporting_rate > classesArray[3].start_value && reporting_rate <= classesArray[3].end_value) {
        color = classesArray[3].color_code;
        class3 = Number(class3) + 1;
        status = classesArray[3].description;
    }
    if (reporting_rate > classesArray[4].start_value && reporting_rate <= classesArray[4].end_value) {
        color = classesArray[4].color_code;
        class4 = Number(class4) + 1;
        status = classesArray[4].description;
    }
    if (reporting_rate > classesArray[5].start_value && reporting_rate <= classesArray[5].end_value) {
        color = classesArray[5].color_code;
        class5 = Number(class5) + 1;
        status = classesArray[5].description;
    }
    feature.attributes = {
        district_id: district_id,
        district: district_name,
        province: province,
        product: product,
        StkHolder: StkHolder,
        reported: reported,
        total_warehouse: total_warehouse,
        status:status,
        reporting_rate: reporting_rate,
        color: color
    };
    cLMIS.addFeatures(feature);
    $("#loader").css("display", "none");
}


function onFeatureSelect(e) {
    $("#prov").html(e.feature.attributes['province']);
    $("#district").html(e.feature.attributes['district']);
    $("#stakeholder").html(e.feature.attributes['StkHolder']);
    $("#product").html(e.feature.attributes['product']);
    $("#total_warehouses").html(e.feature.attributes['total_warehouse']);
    $("#reported").html(e.feature.attributes['reported']);
    $("#reporting_rate").html(e.feature.attributes['reporting_rate']);
}

function onFeatureUnselect(e) {
    $("#prov").html("");
    $("#district").html("");
    $("#stakeholder").html("");
    $("#product").html("");
    $("#total_warehouses").html("");
    $("#reported").html("");
    $("#reporting_rate").html("");
}

function titleGenerator() {
    product_name = $("#prod_sel option:selected").text();
    StkHolder = $("#stk_sel option:selected").text();
    prov_name = $("#prov_sel option:selected").text();
    year_name = $("#year_sel option:selected").text();
    month_value = ($('#slider').slider("option", "value")) - 1;
    month_year = monthNames[month_value] + " " + year_name;
    if (StkHolder == "All" && sector == "0") {
        StkHolder = $("#sector option:selected").text();
    } else if (StkHolder == "All" && sector == "1") {
        StkHolder = $("#sector option:selected").text();
    } else {}
    if(prov_name == "All"){ prov_name = "Pakistan";}
    download = StkHolder+"->"+product_name+"->"+month_year;
    $("#mapTitle").html("<font color='green' size='4'><b>Reporting Rate (" + month_year + ")</b></font> <br/> " + StkHolder + " " + product_name);

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

function emptyArray() {
    $("#loader").css("display", "block");
    $('.radio-button').prop('checked', false);
    $("#mapTitle").html("");
    pieArray.length = 0;
    NoData = '0';
    DataProblem = '0';
    class1 = '0';
    class2 = '0';
    class3 = '0';
    class4 = '0';
    class5 = '0';
}

function SortByID(x, y) {
    return x.reporting_rate - y.reporting_rate;
}

function SortByRankingID(x, y) {
    return y.value - x.value;
}

function districtCountGraph() {

    var ND = CalculatePercent(NoData, maximum);
    var cls1 = CalculatePercent(class1, maximum);
    var cls2 = CalculatePercent(class2, maximum);
    var cls3 = CalculatePercent(class3, maximum);
    var cls4 = CalculatePercent(class4, maximum);
    var cls5 = CalculatePercent(class5, maximum);

    pieArray.push({
        label: classesArray[0].description,
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


    var revenueChart = new FusionCharts({
        type: 'pie2D',
        renderAt: 'chart-container',
        width: '100%',
        height: '240px',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - Reporting Rate Status",
                "subcaption":download,
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

function drawGrid() {
    $("#attributeGrid").html("");
    $("#districtRanking").html("");
    dataDownload.length = 0;
    jsonData.length = 0;
    var features = cLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th>StakeHolder</th><th>Total</th><th>Reported</th><th>Reporting Rate (%)</th></thead>";
    for (var i = 0; i < features.length; i++) {
        table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td>" + features[i].attributes.StkHolder + "</td><td align='right'>" + features[i].attributes.total_warehouse + "</td><td align='right'>" + features[i].attributes.reported + "</td><td align='right'>" + features[i].attributes.reporting_rate + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
        jsonData.push({
            label: features[i].attributes.district,
            value: features[i].attributes.reporting_rate,
            color: features[i].attributes.color
        });
        dataDownload.push({
            province: features[i].attributes.province,
            district_name: features[i].attributes.district,
            Stakeholder: features[i].attributes.StkHolder,
            product: features[i].attributes.product,
            total_warehouse: features[i].attributes.total_warehouse,
            reported: features[i].attributes.reported,
            Status:features[i].attributes.status,
            reporting_rate: features[i].attributes.reporting_rate
        });
    }
    table += "</table>";
    $("#attributeGrid").append(table);
    maximum = cLMIS.features.length;
    districtRanking(jsonData,"");
}


function districtRanking(Data,title) {

    Data.sort(SortByRankingID);
    maximumValue = Number(Data[0].value) + 1;

    if (Data.length > 100) {
        width = '480%';
    } else {
        width = '125%';
    }

    var revenueChart = new FusionCharts({
        type: 'column2D',
        renderAt: 'chart-container',
        width: width,
        height: '100%',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - District Wise Reporting Rate Ranking "+ title,
                "subcaption": download,
                "showLabels": "1",
                "slantLabels": '1',
                "enableLink": '0',
                "showValues": '1',
                "rotateValues": '1',
                "placeValuesInside": '1',
                "yAxisMaxValue": maximumValue,
                "xAxisName": "",
                "yAxisName": "Reporting Rate ( % )",
                "exportEnabled": "1",
                "theme": "fint"
            },
            "data": Data
        }
    });
    revenueChart.render("districtRanking");
}


function gridFilter(color) {
    $("#attributeGrid").html("");
    dataDownload.length = 0;
    var features = cLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th>StakeHolder</th><th>Total</th><th>Reported</th><th>Reporting Rate (%)</th></thead>";
    for (var i = 0; i < features.length; i++) {
        if (features[i].attributes.color == color) {
            table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td>" + features[i].attributes.StkHolder + "</td><td align='right'>" + features[i].attributes.total_warehouse + "</td><td align='right'>" + features[i].attributes.reported + "</td><td align='right'>" + features[i].attributes.reporting_rate + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
            dataDownload.push({
                province: features[i].attributes.province,
                district_name: features[i].attributes.district,
                Stakeholder: features[i].attributes.StkHolder,
                product: features[i].attributes.product,
                total_warehouse: features[i].attributes.total_warehouse,
                reported: features[i].attributes.reported,
                Status:features[i].attributes.status,
                reporting_rate: features[i].attributes.reporting_rate
            });
        }
    }
    table += "</table>";
    $("#attributeGrid").append(table);
}