function getLegend(value, max, min, title) {
    $.ajax({
        url: appPath+"maps/api/get-color-classes.php",
        type: "GET",
        data: "id=" + value,
        dataType: "json",
        success: callback
    });


    function callback(response) {
        var colors = [];
        colors = response;

        $("#legend").empty();

        if (colors[2].description == "null") {
            var range = (max - min) / 5;

            var interval = min + range;
            var interval2 = interval + range;
            var interval3 = interval2 + range;
            var interval4 = interval3 + range;
            var interval5 = interval4 + range;


            if (min == max || max == "-Infinity") {
                miniLegend(max, colors, title);
                return;
            }


            if (range < 1 && max > 1) {
                interval = Number(interval).toPrecision(2);
                interval2 = Number(interval2).toPrecision(2);
                interval3 = Number(interval3).toPrecision(2);
                interval4 = Number(interval4).toPrecision(2);
                interval5 = Number(interval5).toPrecision(2);
            } else if (range < 1 && max < 1) {
                interval = Number(interval).toPrecision(2);
                interval2 = Number(interval2).toPrecision(2);
                interval3 = Number(interval3).toPrecision(2);
                interval4 = Number(interval4).toPrecision(2);
                interval5 = Number(interval5).toPrecision(2);
            } else {
                interval = Math.round(Number(interval));
                interval2 = Math.round(Number(interval2));
                interval3 = Math.round(Number(interval3));
                interval4 = Math.round(Number(interval4));
                interval5 = Math.round(Number(interval5));
            }

            for (var i = 0; i < 6; i++) {
               
                if (i == "0") {
                    classesArray.push({
                        "start_value": "0",
                        "end_value": "0",
                        "description": colors[0].description,
                        "color_code": colors[0].color_code
                    });
                }
                if (i == "1") {
                    if (interval == "0") {
                        description = "0-0";
                    } else {
                        if (min == "0") {
                            min = 1;
                        }
                        description = min.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "   -   " + interval.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    classesArray.push({
                        "start_value": "0",
                        "end_value": interval,
                        "description": description,
                        "color_code": colors[1].color_code
                    });
                }
                if (i == "2") {
                    classesArray.push({
                        "start_value": interval,
                        "end_value": interval2,
                        "description": interval.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "   -   " + interval2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
                        "color_code": colors[2].color_code
                    });
                }
                if (i == "3") {
                    classesArray.push({
                        "start_value": interval2,
                        "end_value": interval3,
                        "description": interval2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "   -   " + interval3.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
                        "color_code": colors[3].color_code
                    });
                }
                if (i == "4") {
                    classesArray.push({
                        "start_value": interval3,
                        "end_value": interval4,
                        "description": interval3.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "   -   " + interval4.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
                        "color_code": colors[4].color_code
                    });
                }
                if (i == "5") {
                    classesArray.push({
                        "start_value": interval4,
                        "end_value": interval5,
                        "description": interval4.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "   -   " + interval5.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
                        "color_code": colors[5].color_code
                    });
                }

            }
        }

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
            cell3.style.paddingLeft = "3px";
            cell1.innerHTML = "<input name='color' style='cursor:pointer' class='radio-button' type='radio' onclick='getColorName(\"" + classesArray[i].color_code + "\",\""+classesArray[i].description +"\")'/>";
            cell2.innerHTML = "<div style='width:22px;height:18px;background-color:" + classesArray[i].color_code + "'></div>";
            cell3.innerHTML = classesArray[i].description;
        }

        var row = legend.insertRow(0);
        var cell = row.insertCell(0);
        cell.colSpan = "2";
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
            cell3.style.paddingLeft = "3px";

            cell1.innerHTML = "<input name='color' style='cursor:pointer' class='radio-button' type='radio' onclick='getColorName(\"" + classesArray[i].color_code + "\",\""+classesArray[i].description +"\")'/>";
            cell2.innerHTML = "<div style='width:22px;height:18px;background-color:" + classesArray[i].color_code + "'></div>";
            cell3.innerHTML = classesArray[i].description;

        }


        var row = legend.insertRow(0);
        var cell = row.insertCell(0);
        cell.colSpan = "3";
        cell.align = "left";
        cell.innerHTML = "<font size='2' color='green'><b>" + title + "</b></font><br/>";

        $("#legendDiv").css("display", "block");
        drawLayer();
    }
}

function miniLegend(max, colors, title) {

    for (var i = 0; i < 2; i++) {
       
        if (i == "0") {
            classesArray.push({
                "start_value": "0",
                "end_value": "0",
                "description": colors[0].description,
                "color_code": colors[0].color_code
            });
        }
        if (i == "1") {
            classesArray.push({
                "start_value": max,
                "end_value": max,
                "description": max.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
                "color_code": colors[5].color_code
            });
        }
    }
    
    var end;
    if (max == "0" || max == "-Infinity") {
        classes = parseInt(classesArray.length) - 2;
        end = "0";
    } else {
        classes = parseInt(classesArray.length) - 1;
        end = "1";
    }
    var legend = document.getElementById('legend');
    var start = classes;
    
    for (var i = start; i >= end; i--) {
        var row = legend.insertRow(0);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        cell1.align = "left";
        cell2.align = "left";
        cell1.innerHTML = "<div style='width:30px;height:18px;border:1px solid darkgray;background-color:" + classesArray[i].color_code + "'></div>";
        cell2.innerHTML = classesArray[i].description;
    }
    var row = legend.insertRow(0);
    var cell = row.insertCell(0);
    cell.colSpan = "2";
    cell.align = "left";
    cell.innerHTML = "<font size='2' color='green'><b>" + title + "</b></font><br/>";
    $("#legendDiv").css("display", "block");
    drawLayer();
}