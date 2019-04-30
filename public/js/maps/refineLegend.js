function getColorName(color,description) {
    selectfeature.unselectAll();
    var features = cLMIS.features;
    for (var i = 0; i < features.length; i++) {
        features[i].style = '';
    }
    cLMIS.redraw();

    for (var i = 0; i < features.length; i++) {
        if (features[i].attributes.color != color) {
            features[i].style = {
                display: 'none'
            };
        }
    }
    cLMIS.redraw();
    
   my_json = JSON.stringify(jsonData)
   filtered_json = find_in_object(JSON.parse(my_json), {color: color});
   if(filtered_json.length == '0'){
        $("#attributeGrid").html("<p align='center' style='padding:50px'>No Data Found</p>");
        $("#districtRanking").html("<p align='center' style='padding:50px'>No Data Found</p>");
        dataDownload.length = 0;
        return;
   }
   var desc = "- "+ description;
   districtRanking(filtered_json,desc);
   gridFilter(color);
}

function getFullColor() {
    selectfeature.unselectAll();
    var features = cLMIS.features;
    for (var i = 0; i < features.length; i++) {
        features[i].style = '';
    }
    cLMIS.redraw();
    $('.radio-button').prop('checked', false);
    drawGrid();
}


function find_in_object(my_object, my_criteria){

  return my_object.filter(function(obj) {
    return Object.keys(my_criteria).every(function(c) {
      return obj[c] == my_criteria[c];
    });
  });

}

 
 
 