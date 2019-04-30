//v.3.0 build 110707

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
To use this component please contact sales@dhtmlx.com to obtain license
*/
dhtmlXGridObject.prototype._process_json_row=function(a,b){a._attrs=b;for(var d=0;d<a.childNodes.length;d++)a.childNodes[d]._attrs={};if(b.userdata)for(var e in b.userdata)this.setUserData(a.idd,e,b.userdata[e]);for(var c=0;c<b.data.length;c++)if(typeof b.data[c]=="object"&&b.data[c]!=null){a.childNodes[c]._attrs=b.data[c];if(b.data[c].type)a.childNodes[c]._cellType=b.data[c].type;b.data[c]=b.data[c].value}this._fillRow(a,this._c_order?this._swapColumns(b.data):b.data);return a};
dhtmlXGridObject.prototype._process_json=function(a){this._parsing=!0;try{a&&a.xmlDoc?eval("data="+a.xmlDoc.responseText+";"):typeof a=="string"&&eval("data="+a+";")}catch(b){dhtmlxError.throwError("LoadXML","Incorrect JSON",[a.xmlDoc||a,this]),a={rows:[]}}var d=parseInt(a.pos||0),e=parseInt(a.total_count||0),c=!1;e&&(this.rowsBuffer[e-1]||(this.rowsBuffer.length&&(c=!0),this.rowsBuffer[e-1]=null),e<this.rowsBuffer.length&&(this.rowsBuffer.splice(e,this.rowsBuffer.length-e),c=!0));for(var g in a)g!=
"rows"&&this.setUserData("",g,a[g]);if(this.isTreeGrid())return this._process_tree_json(a);for(var f=0;f<a.rows.length;f++)if(!this.rowsBuffer[f+d]){var h=a.rows[f].id;this.rowsBuffer[f+d]={idd:h,data:a.rows[f],_parser:this._process_json_row,_locator:this._get_json_data};this.rowsAr[h]=a.rows[f]}if(c&&this._srnd){var i=this.objBox.scrollTop;this._reset_view();this.objBox.scrollTop=i}else this.render_dataset();this._parsing=!1};
dhtmlXGridObject.prototype._get_json_data=function(a,b){return typeof a.data[b]=="object"?a.data[b].value:a.data[b]};
dhtmlXGridObject.prototype._process_tree_json=function(a,b,d){this._parsing=!0;var e=!1;if(!b){this.render_row=this.render_row_tree;e=!0;b=a;d=b.parent||0;d=="0"&&(d=0);if(!this._h2)this._h2=new dhtmlxHierarchy;if(this._fake)this._fake._h2=this._h2}if(b.rows)for(var c=0;c<b.rows.length;c++){var g=b.rows[c].id,f=this._h2.add(g,d);f.buff={idd:g,data:b.rows[c],_parser:this._process_json_row,_locator:this._get_json_data};if(b.rows[c].open)f.state="minus";this.rowsAr[g]=f.buff;this._process_tree_json(b.rows[c],
b.rows[c],g)}if(e)d!=0&&this._h2.change(d,"state","minus"),this._updateTGRState(this._h2.get[d]),this._h2_to_buff(),d!=0&&(this._srnd||this.pagingOn)?this._renderSort():this.render_dataset(),this._slowParse===!1&&this.forEachRow(function(a){this.render_row_tree(0,a)}),this._parsing=!1};

//v.3.0 build 110707

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
To use this component please contact sales@dhtmlx.com to obtain license
*/