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
                    internalProjection: new OpenLayers.Projection("EPSG:900913"),
                    externalProjection: new OpenLayers.Projection("EPSG:900913")
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

    cLMIS = new OpenLayers.Layer.Vector("Month Of Stock", {
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
    level = $("#level_sel").val();
    if (level == 3 || level == "all") {
        getLegend('1', 'Month of Stock');
    } else {
        getLegend('2', 'Month of Stock');
    }
}

function drawFeature() {

    year = $("#year_sel").val();
    month = $('#slider').slider("option", "value");
    sector = $("#sector").val();
    stk = $("#stk_sel").val();
    prov = $("#prov_sel").val();
    product = $("#prod_sel").val();

       
    $.ajax({
        url: appPath+"maps/api/get-c-mos-map-data.php",
        type: "GET",
        data: {
            year: year,
            month: month,
            sector: sector,
            stk: stk,
            province: prov,
            product: product,
            level: level
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
            chkeArray(data[i].district_id, data[i].mapping_id, Number(data[i].mos));
        }
        drawGrid();
        districtCountGraph();
    }
}


function chkeArray(district_id, mapping_id, mos) {
    for (var i = 0; i < district.features.length; i++) {
        if (district_id == district.features[i].attributes.district_id) {
            cLMISLayer(district.features[i].geometry, district.features[i].attributes.province_id, district.features[i].attributes.province_name, product_name, StkHolder, mapping_id, district.features[i].attributes.district_name, mos);
        }
    }
}

function cLMISLayer(wkt, province_id, province, product, StkHolder, district_id, district_name, value) {
    var feature = new OpenLayers.Feature.Vector(wkt);

    if (value == classesArray[0].start_value && value == classesArray[0].end_value) {
        color = classesArray[0].color_code;
        NoData = Number(NoData) + 1;
        status = classesArray[0].description;
    }
    if (value > classesArray[1].start_value && value <= classesArray[1].end_value) {
        color = classesArray[1].color_code;
        StockOut = Number(StockOut) + 1;
        status = classesArray[1].description;
    }
    if (value >= classesArray[2].start_value && value <= classesArray[2].end_value) {
        color = classesArray[2].color_code;
        UnderStock = Number(UnderStock) + 1;
        status = classesArray[2].description;
    }
    if (value >= classesArray[3].start_value && value <= classesArray[3].end_value) {
        color = classesArray[3].color_code;
        Satisfactory = Number(Satisfactory) + 1;
        status = classesArray[3].description;
    }
    if (value >= classesArray[4].start_value) {
        color = classesArray[4].color_code;
        OverStock = Number(OverStock) + 1;
        status = classesArray[4].description;
    }

    feature.attributes = {
        district_id: district_id,
        province_id: province_id,
        district: district_name,
        province: province,
        product: product,
        StkHolder: StkHolder,
        status : status,
        value: value,
        color: color
    };
    cLMIS.addFeatures(feature);
    $("#loader").hide();
}

function onFeatureSelect(e) {
    $("#prov").html(e.feature.attributes['province']);
    $("#district").html(e.feature.attributes['district']);
    $("#stakeholder").html(e.feature.attributes['StkHolder']);
    $("#product").html(e.feature.attributes['product']);
    $("#mos").html(e.feature.attributes['value']);
    lastMonthsStats(e.feature.attributes['district_id'], e.feature.attributes['district']);
}

function onFeatureUnselect(e) {
    $("#prov").html("");
    $("#district").html("");
    $("#stakeholder").html("");
    $("#product").html("");
    $("#mos").html("");
}



function drawGrid() {
    $("#attributeGrid").html("");
    $("#districtRanking").html("");
    dataDownload.length = 0;
    jsonData.length = 0;
    var features = cLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th>StakeHolder</th><th>MOS</th></thead>";
    for (var i = 0; i < features.length; i++) {
        table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td>" + features[i].attributes.StkHolder + "</td><td align='right'>" + features[i].attributes.value + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
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
            Status:features[i].attributes.status,
            MOS: features[i].attributes.value
        });
    }
    table += "</table>";
    $("#attributeGrid").append(table);
    maximum = cLMIS.features.length;
    var pageTitle = $(".page-title").html();
    var title = pageTitle.split("Map");
    districtRanking(jsonData,"- "+title[0]);
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
            level: level
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
        var minMaxPercent = (maximumValue * 10 / 100);
        maximumValue = maximumValue + minMaxPercent;
        minimumValue = minimumValue - minMaxPercent
        if(minimumValue < 0){minimumValue = 0;}
        
        var revenueChart = new FusionCharts({
            type: 'line',
            width: '100%',
            height: '200px',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": district_name+" - Previous Stock Trend",
                    "subcaption":StkHolder+"->"+product_name,
                    "xAxisName": "",
                    "yAxisName": "Months",
                    "exportEnabled": "1",
                    "enableLink": '1',
                    "showValues": '1',
                    "showLabels": "1",
                    "yAxisMaxValue": maximumValue,
                    "yAxisMinValue": minimumValue,
                    "formatnumberscale": "1",
                    "showYAxisValues":'1',
                    "adjustDiv":"0",
                    "numDivLines": "3",
                    "theme": "fint"
                },
                "data": chart
            }
        });
        revenueChart.render("graph");
    }
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
    var year_name = $("#year_sel option:selected").text();
    var month_value = ($('#slider').slider("option", "value")) - 1;
    var level_name = $("#level_sel option:selected").text();
    var month_name = monthNames[month_value];
    month_year = month_name + " " + year_name;
    if(prov_name == "All"){ prov_name = "Pakistan";}
    download = StkHolder+"->"+product_name+"->"+month_year;
    $("#mapTitle").html("<font color='green' size='4'>Month of Stock  (" + month_year + ")</font> <br/> " + StkHolder + " " + product_name + " at " + level_name + " level");

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

function clearData() {
    $("#loader").show();
    $("#legendDiv").css("display", "none");
    $("#legend").html("");
    $('.radio-button').prop('checked', false);
    $("#mosRanges").html("");
    $("#graph").html("Click any district for previous Stock Trend");
    $("#attributeGrid").html("");
    $("#districtRanking").html("");
    $("#mapTitle").html("");
    classesArray.length = 0;
    pieArray.length = 0;
    NoData = '0';
    DataProblem = '0';
    StockOut = '0';
    UnderStock = '0';
    Satisfactory = '0';
    OverStock = '0';
}

function districtCountGraph() {

    var ND = CalculatePercent(NoData, maximum);
    var SO = CalculatePercent(StockOut, maximum);
    var US = CalculatePercent(UnderStock, maximum);
    var SAT = CalculatePercent(Satisfactory, maximum);
    var OS = CalculatePercent(OverStock, maximum);
    
    pieArray.push({
        label: 'No Data',
        value: ND,
        color: classesArray[0].color_code
    });
    pieArray.push({
        label: 'Stock Out',
        value: SO,
        color: classesArray[1].color_code
    });
    pieArray.push({
        label: 'Under Stock',
        value: US,
        color: classesArray[2].color_code
    });
    pieArray.push({
        label: 'Satisfactory',
        value: SAT,
        color: classesArray[3].color_code
    });
    pieArray.push({
        label: 'Over Stock',
        value: OS,
        color: classesArray[4].color_code
    });

    var revenueChart = new FusionCharts({
        type: 'pie2D',
        renderAt:'chart-container',
        width:'100%',
        height:'240px',
        dataFormat:'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - MOS Status",
                "subcaption":download,
                "showLabels":"0",
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


function districtRanking(records,title) {

    records.sort(SortByRankingID);

    if (records.length > 100) {
        width = '480%';
    } 
    else {
        width = '130%';
    }
    var maximum = records[0].value;
    var minMaxPercent = (maximum * 10 / 100);
    maximum = maximum + minMaxPercent;
    
    var revenueChart = new FusionCharts({
        type: 'column2d',
        renderAt: 'chart-container',
        width: width,
        height: '100%',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - District wise Stock Ranking "+ title,
                "subcaption":download,
                "yAxisMaxValue": maximum,
                "slantLabels": '1',            
                "showValues": '1',
                "rotateValues": '1',
                "placeValuesInside": '1',
                "adjustDiv":'0',
                "numDivLines":'3',
                "xAxisName": "",
                "yAxisName": "No.of Months",
                "exportEnabled": "1",
                "theme": "fint"
            },
            "data": records
        }
    });
    revenueChart.render("districtRanking");
}

function SortByID(x, y) {
    return x.mos - y.mos;
}

function SortByRankingID(x, y) {
    return y.value - x.value;
}

function gridFilter(color) {
    $("#attributeGrid").html("");
    dataDownload.length = 0;
    var features = cLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th>StakeHolder</th><th>MOS</th><th></th></thead>";
    for (var i = 0; i < features.length; i++) {
        if (features[i].attributes.color == color) {
            table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td>" + features[i].attributes.StkHolder + "</td><td align='right'>" + features[i].attributes.value + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
            dataDownload.push({
                province: features[i].attributes.province,
                district_name: features[i].attributes.district,
                Stakeholder: features[i].attributes.StkHolder,
                product: features[i].attributes.product,
                Status:features[i].attributes.status,
                MOS: features[i].attributes.value
            });
        }
    }
    table += "</table>";
    $("#attributeGrid").append(table);
}



function getLegend(value, title) {
    $.ajax({
        url: appPath+"maps/api/get-color-classes.php",
        type: "GET",
        data: "id=" + value,
        dataType: "json",
        async: false,
        success: callback
    });

    function callback(response) {
        
        classesArray = response;
        var classes = parseInt(classesArray.length) - 1;
        var legend = document.getElementById('legend');

        var row = legend.insertRow(0);
        var cell = row.insertCell(0);
        cell.colSpan = "3";
        cell.align = "right";
        cell.className = "hide_td";
        cell.innerHTML = "<a class='undo' onclick='getFullColor()'>Reset</a>";

        for (var i = 0; i >= 0; i--) {
            var row = legend.insertRow(0);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            cell1.align = "right";
            cell1.className = "hide_td";
            cell2.align = "right";
            cell2.width = "22px";
            cell3.align = "left";
            cell3.style.paddingLeft = "2px";
            cell1.innerHTML = "<input name='color' class='radio-button' type='radio' onclick='getColorName(\"" + classesArray[i].color_code + "\",\""+classesArray[i].description +"\")'/>";
            cell2.innerHTML = "<div style='width:22px;height:18px;background-color:" + classesArray[i].color_code + "'></div>";
            cell3.innerHTML = classesArray[i].description;
        }

        var row = legend.insertRow(0);
        var cell = row.insertCell(0);
        cell.colSpan = "3";
        cell.align = "center";
        cell.innerHTML = "<br/>";

         for (var i = classes; i >= 1; i--) {
            var row = legend.insertRow(0);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            cell1.align = "right";
            cell1.className = "hide_td";
            cell2.align = "right";
            cell2.width = "22px";
            cell3.align = "left";
            cell3.style.paddingLeft = "2px";
            cell1.innerHTML = "<input name='color' class='radio-button' type='radio' onclick='getColorName(\"" + classesArray[i].color_code + "\",\""+classesArray[i].description +"\")'/>";
            cell2.innerHTML = "<div style='cursor:pointer;width:22px;height:18px;background-color:" + classesArray[i].color_code + "'></div>";
            cell3.innerHTML = classesArray[i].description;
        }
        var row = legend.insertRow(0);
        var cell = row.insertCell(0);
        cell.colSpan = "3";
        cell.align = "left";
        cell.innerHTML = "<font size='2' color='green'><b>" + title + "</b></font>";
        
        var defination = document.getElementById('mosRanges');
        for (var i = classes; i >= 1; i--) {

            var row = defination.insertRow(0);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            cell1.align = "center";
            cell1.width = "250px";
            cell2.align = "center";
            cell2.width = "100px";
            cell1.innerHTML = classesArray[i].description;
            cell2.innerHTML = classesArray[i].interval;

        }   
        $("#mosDefination").css("display", "block");
        $("#legendDiv").css("display", "block");
        drawFeature();
    }
}