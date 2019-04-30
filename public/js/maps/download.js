$(document).ready(function() {

    $("#image").click(function() {
        map.zoomToExtent(downloadExtent);
        selectfeature.unselectAll();
        $('.hide_td').hide();
        $('#customZoom').hide();
        map.removeControl(map.getControlsByClass('OpenLayers.Control.ScaleLine')[0]);
        map.removeControl(map.getControlsByClass('OpenLayers.Control.MousePosition')[0]);
        $("#printedDate").css("display", "block");

        html2canvas(document.getElementById("map"), {
            useCORS: true,
            onrendered: function(canvas) {
                var imgprint = canvas.toDataURL("image/jpeg",1.0);
                var link = document.createElement('a');
                link.href = imgprint;
                link.download = 'map.jpeg';
                document.body.appendChild(link);
                link.click();
                setTimeout(drawControls, 50);
            }
        });
    });

    function drawControls() {
        map.addControl(new OpenLayers.Control.ScaleLine());
        map.addControl(new OpenLayers.Control.MousePosition({
            prefix: 'coordinates: ',
            numDigits: 2,
            separator: ' | '
        }));
        $("#printedDate").css("display", "none");
        $('.hide_td').show();
        $('#customZoom').show();
    }

    var img, urljpg;
    $("#print").click(function() {
        map.zoomToExtent(downloadExtent);
        selectfeature.unselectAll();
        $('.hide_td').hide();
        $('#customZoom').hide();
        map.removeControl(map.getControlsByClass('OpenLayers.Control.Zoom')[0]);
        map.removeControl(map.getControlsByClass('OpenLayers.Control.ScaleLine')[0]);
        map.removeControl(map.getControlsByClass('OpenLayers.Control.MousePosition')[0]);

        $("#printedDate").css("display", "block");

        html2canvas(document.getElementById("map"), {
            useCORS: true,
            onrendered: function(canvas) {
                img = canvas.toDataURL("image/jpeg",1.0);
                var image = document.createElement("img");
                image.src = img;
                urljpg = image.src.replace(/^data:image\/[^;]/, 'data:image/jpeg');

                var printWindow = window.open();
                printWindow.document.write('<html><head><title></title>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<div id="content" style="width:100%;height:600px;position:absolute;top:100px;left:0px">');
                printWindow.document.write('<img style="width:100%;height:80%" src="' + urljpg + '"');
                printWindow.document.write('</div>');
                printWindow.document.write('</body></html>');
                printWindow.document.close();

                map.addControl(new OpenLayers.Control.ScaleLine());
                map.addControl(new OpenLayers.Control.MousePosition({
                    prefix: 'coordinates: ',
                    numDigits: 2,
                    separator: ' | '
                }));
                map.addControl(new OpenLayers.Control.Zoom({
                    zoomInId: "customZoomIn",
                    zoomOutId: "customZoomOut"
                }));
                $("#printedDate").css("display", "none");
                $('.hide_td').show();
                $('#customZoom').show();
                printWindow.print();
            }
        });
    });


    $("#excel").click(function() {
        var title = $(".page-title").html();
        var split = title.split("Map");
        JSONToCSVConvertor(dataDownload,split[0], true);
    });

    function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
        var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
        var CSV = '';
        CSV += ReportTitle + '\r\n\n';
        if (ShowLabel) {
            var row = "";
            for (var index in arrData[0]) {
                row += index + ',';
            }
            row = row.slice(0, -1);
            CSV += row + '\r\n';
        }

        for (var i = 0; i < arrData.length; i++) {
            var row = "";
            for (var index in arrData[i]) {
                row += '"' + arrData[i][index] + '",';
            }
            row.slice(0, row.length - 1);
            CSV += row + '\r\n';
        }

        if (CSV == '') {
            alert("Invalid data");
            return;
        }

        var fileName = "";
        fileName += ReportTitle.replace(/ /g, "_");
        var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
        var link = document.createElement("a");
        link.href = uri;
        link.style = "visibility:hidden";
        link.download = fileName + ".csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

});