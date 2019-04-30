$(window).load(function() {
    $("#date_from").datepicker({
        dateFormat: 'yy-mm-dd',
        constrainInput: false,
        changeMonth: true,
        changeYear: true,
        onSelect: function(selectedDate) {
            $("#date_to").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#date_from").datepicker("setDate", '2014-01-01');

    var currentDate = new Date();
    $("#date_to").datepicker({
        dateFormat: 'yy-mm-dd',
        constrainInput: false,
        changeMonth: true,
        changeYear: true,
        onSelect: function(selectedDate) {
            $("#date_from").datepicker("option", "maxDate", selectedDate);
        }
    });
    $("#date_to").datepicker("setDate", '2014-12-31');

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
            styleMap: district_style
        });

    cLMIS = new OpenLayers.Layer.Vector("CYP By Population", {
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

    emptyArrays();
    onFeatureUnselect();

    date_from = $("#date_from").val();
    date_to = $("#date_to").val();
    sector = $("#sector").val();
    stk = $("#stk_sel").val();
    var province = $("#prov_sel").val();
    product = $("#prod_sel").val();

    titleGenerator();

    $.ajax({
        url: appPath+"maps/api/get-cyp-pop-generated.php",
        type: "GET",
        data: {
            datefrom: date_from,
            dateto: date_to,
            sector: sector,
            stk: stk,
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
        data = response;
        for (var i = 0; i < data.length; i++) {
            maxValue.push(Number(data[i].cyp_pop));
        }
        max = Math.max.apply(Math, maxValue);
        min = Math.min.apply(Math, maxValue);
        getLegend('5', max, min, 'CYP By Population ( % )');
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
        chkeArray(data[i].district_id, data[i].mapping_id, Number(data[i].cyp), Number(data[i].pop), Number(data[i].cyp_pop));
    }
    drawGrid();
    districtCountGraph();
}

function chkeArray(district_id, mapping_id, cyp, pop, cyp_pop) {
    for (var i = 0; i < district.features.length; i++) {
        if (district_id == district.features[i].attributes.district_id) {
            if (min == max) {
                cLMISMiniLayer(district.features[i].geometry, district.features[i].attributes.province_id, district.features[i].attributes.province_name, product_name, StkHolder, mapping_id, district.features[i].attributes.district_name, cyp, pop, cyp_pop);
                break;
            } else {
                cLMISLayer(district.features[i].geometry, district.features[i].attributes.province_id, district.features[i].attributes.province_name, product_name, StkHolder, mapping_id, district.features[i].attributes.district_name, cyp, pop, cyp_pop);
                break;
            }
        }
    }
}


function cLMISLayer(wkt, province_id, province, product, StkHolder, district_id, district, cyp, pop, cyp_pop) {

    feature = new OpenLayers.Feature.Vector(wkt);

    if (cyp_pop == classesArray[0].start_value && cyp_pop == classesArray[0].end_value) {
        color = classesArray[0].color_code;
        NoData = Number(NoData) + 1;
        status = classesArray[0].description;
    }
    if (cyp_pop > classesArray[1].start_value && cyp_pop <= classesArray[1].end_value) {
        color = classesArray[1].color_code;
        class1 = Number(class1) + 1;
        status = classesArray[1].description;
    }
    if (cyp_pop > classesArray[2].start_value && cyp_pop <= classesArray[2].end_value) {
        color = classesArray[2].color_code;
        class2 = Number(class2) + 1;
        status = classesArray[2].description;
    }
    if (cyp_pop > classesArray[3].start_value && cyp_pop <= classesArray[3].end_value) {
        color = classesArray[3].color_code;
        class3 = Number(class3) + 1;
        status = classesArray[3].description;
    }
    if (cyp_pop > classesArray[4].start_value && cyp_pop <= classesArray[4].end_value) {
        color = classesArray[4].color_code;
        class4 = Number(class4) + 1;
        status = classesArray[4].description;
    }
    if (cyp_pop > classesArray[5].start_value) {
        color = classesArray[5].color_code;
        class5 = Number(class5) + 1;
        status = classesArray[5].description;
    }

    feature.attributes = {
        province_id: province_id,
        district_id: district_id,
        district: district,
        province: province,
        product: product,
        StkHolder: StkHolder,
        cyp: cyp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        pop: pop.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        status:status,
        value: cyp_pop,
        color: color
    };
    cLMIS.addFeatures(feature);
    $("#loader").css("display", "none");
}

function cLMISMiniLayer(wkt, province_id, province, product, StkHolder, district_id, district, cyp, pop, cyp_pop) {
    feature = new OpenLayers.Feature.Vector(wkt);
    if(cyp_pop == parseInt(classesArray[0].start_value) && cyp_pop == parseInt(classesArray[0].end_value)) {
        color = classesArray[0].color_code;
        NoData = Number(NoData) + 1;
    } else {
        color = classesArray[1].color_code;
        class1 = Number(class1) + 1;
    }

    feature.attributes = {
        province_id: province_id,
        district_id: district_id,
        district: district,
        province: province,
        product: product,
        StkHolder: StkHolder,
        cyp: cyp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        pop: pop.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        status:status,
        value: cyp_pop,
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
    $("#cyp").html(e.feature.attributes['cyp']);
    $("#pop").html(e.feature.attributes['pop']);
    $("#cyp_per").html(e.feature.attributes['value']);
}

function onFeatureUnselect(e) {
    $("#prov").html("");
    $("#district").html("");
    $("#stakeholder").html("");
    $("#product").html("");
    $("#cyp").html("");
    $("#pop").html("");
    $("#cyp_per").html("");
}



function titleGenerator() {
    product_name = $("#prod_sel option:selected").text();
    StkHolder = $("#stk_sel option:selected").text();
    prov_name = $("#prov_sel option:selected").text();

    var month_from_year = date_from.split("-");
    var month_to_year = date_to.split("-");

    var from_month = month_from_year[1];
    var to_month = month_to_year[1];

    if (product_name == "All") {
        product_name = "All Products";
    }
    if (StkHolder == "All" && sector == "0") {
        StkHolder = $("#sector option:selected").text();
    } else if (StkHolder == "All" && sector == "1") {
        StkHolder = $("#sector option:selected").text();
    } else {}
    
    from =  month_from_year[0]+"-"+month_from_year[1];
    to =  month_to_year[0]+"-"+month_to_year[1];
    if (from == to) {
        month_year = monthNames[parseInt(from_month) - 1] + " " + month_from_year[0];
        $("#mapTitle").html("<font color='green' size='4'><b>CYP Normalized By Population<b></font> <br/><font color='green' size='3'><b>(" + month_year + ")</b></font> <br/> " + StkHolder + " " + product_name);
    } else {
        month_year = monthNames[parseInt(from_month) - 1] + " " + month_from_year[0] + " - " + monthNames[parseInt(to_month) - 1] + " " + month_to_year[0];
        $("#mapTitle").html("<font color='green' size='4'><b>CYP Normalized By Population<b></font> <br/><font color='green' size='3'><b>(" + month_year + ")</b></font> <br/> " + StkHolder + " " + product_name);
    }
    if(prov_name == "All"){ prov_name = "Pakistan";}
    download = StkHolder+"->"+product_name+"->"+month_year;
    
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

function emptyArrays() {
    $("#loader").css("display", "block");
    $('.radio-button').prop('checked', false);
    classesArray.length = 0;
    maxValue.length = 0;
    pieArray.length = 0;
    $("#legend").html("");
    $("#mapTitle").html("");
    $("#legendDiv").css("display", "none");
    NoData = '0';
    DataProblem = '0';
    class1 = '0';
    class2 = '0';
    class3 = '0';
    class4 = '0';
    class5 = '0';
}

function SortByID(x, y) {
    return x.cyp_pop - y.cyp_pop;
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
    }


    var revenueChart = new FusionCharts({
        type: 'pie2D',
        renderAt: 'chart-container',
        width: '100%',
        height: '240px',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - CYP Normalized",
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
    table += "<thead><th>Province</th><th>District</th><th>CYP</th><th>Population</th><th>CYP Normalized</th><th></th></thead>";
    for (var i = 0; i < features.length; i++) {
        table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td align='right'>" + features[i].attributes.cyp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td><td align='right'>" + features[i].attributes.pop.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td><td align='right'>" + features[i].attributes.value + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
        jsonData.push({
            label: features[i].attributes.district,
            value: features[i].attributes.value,
            color: features[i].attributes.color
        });
        dataDownload.push({
            province: features[i].attributes.province,
            district_name: features[i].attributes.district,
            Stakeholder: features[i].attributes.StkHolder,
            product: features[i].attributes.product,
            CYP: features[i].attributes.cyp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
            population: features[i].attributes.pop.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
            Status:features[i].attributes.status,
            CYP_Normalized_By_Population: features[i].attributes.value
        });
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
                "caption": prov_name+" - District wise CYP Normalized By Population Ranking "+title,
                "subcaption": download,
                "showLabels": "1",
                "slantLabels": '1',
                "enableLink": '0',
                "showValues": '1',
                "rotateValues": '1',
                "placeValuesInside": '1',
                "formatnumberscale": "0",
                "xAxisName": "",
                "yAxisName": "CYP",
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
    table += "<thead><th>Province</th><th>District</th><th>CYP</th><th>Population</th><th>CYP Normalized</th><th></th></thead>";
    for (var i = 0; i < features.length; i++) {
        if (features[i].attributes.color == color) {
            table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td align='right'>" + features[i].attributes.cyp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td><td align='right'>" + features[i].attributes.pop.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td><td align='right'>" + features[i].attributes.value + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
            dataDownload.push({
                province: features[i].attributes.province,
                district_name: features[i].attributes.district,
                Stakeholder: features[i].attributes.StkHolder,
                product: features[i].attributes.product,
                CYP: features[i].attributes.cyp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
                population: features[i].attributes.pop.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
                Status:features[i].attributes.status,
                CYP_Normalized_By_Population: features[i].attributes.value
            });
        }
    }
    table += "</table>";
    $("#attributeGrid").append(table);
}