//v.3.0 build 110707

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
To use this component please contact sales@dhtmlx.com to obtain license
*/
dhtmlXGridObject.prototype.setRowspan=function(c,f,g){var a=this[this._bfs_cells?"_bfs_cells":"cells"](c,f).cell,b=this.rowsAr[c];if(a.rowSpan&&a.rowSpan!=1)for(var d=b.nextSibling,e=1;e<a.rowSpan;e++){var j=d.childNodes[d._childIndexes[a._cellIndex+1]],h=document.createElement("TD");h.innerHTML="&nbsp;";h._cellIndex=a._cellIndex;h._clearCell=!0;j?j.parentNode.insertBefore(h,j):d.parentNode.appendChild(h);this._shiftIndexes(d,a._cellIndex,-1);d=d.nextSibling}a.rowSpan=g;for(var b=this._h2?this.rowsAr[this._h2.get[b.idd].parent.childs[this._h2.get[b.idd].index+
1].id]:b.nextSibling||this.rowsCol[this.rowsCol._dhx_find(b)+1],k=[],e=1;e<g;e++){var i=null,i=this._fake&&!this._realfake?this._bfs_cells3(b,f).cell:this.cells3(b,f).cell;this._shiftIndexes(b,a._cellIndex,1);i&&i.parentNode.removeChild(i);k.push(b);this._h2?(b=this._h2.get[b.idd].parent.childs[this._h2.get[b.idd].index+1])&&(b=this.rowsAr[b.id]):b=b.nextSibling||this.rowsCol[this.rowsCol._dhx_find(b)+1]}this.rowsAr[c]._rowSpan=this.rowsAr[c]._rowSpan||{};this.rowsAr[c]._rowSpan[f]=k;this._fake&&
!this._realfake&&f<this._fake._cCount&&this._fake.setRowspan(c,f,g)};dhtmlXGridObject.prototype._shiftIndexes=function(c,f,g){if(!c._childIndexes){c._childIndexes=[];for(var a=0;a<c.childNodes.length;a++)c._childIndexes[a]=a}for(a=0;a<c._childIndexes.length;a++)a>f&&(c._childIndexes[a]-=g)};
dhtmlXGridObject.prototype.enableRowspan=function(){this._erspan=!0;this.enableRowspan=function(){};this.attachEvent("onAfterSorting",function(){if(!this._dload){for(var c=1;c<this.obj.rows.length;c++)if(this.obj.rows[c]._rowSpan){var f=this.obj.rows[c],g;for(g in f._rowSpan)for(var a=f,b=a._rowSpan[g],d=0;d<b.length;d++){a.nextSibling?a.parentNode.insertBefore(b[d],a.nextSibling):a.parentNode.appendChild(b[d]);if(this._fake){var e=this._fake.rowsAr[a.idd],j=this._fake.rowsAr[b[d].idd];e.nextSibling?
e.parentNode.insertBefore(j,e.nextSibling):e.parentNode.appendChild(j);this._correctRowHeight(a.idd)}a=a.nextSibling}}var h=this.rowsCol.stablesort;this.rowsCol=new dhtmlxArray;this.rowsCol.stablesort=h;for(c=1;c<this.obj.rows.length;c++)this.rowsCol.push(this.obj.rows[c])}});this.attachEvent("onXLE",function(c,f,g,a){for(var b=this.xmlLoader.doXPath("//cell[@rowspan]",a),d=0;d<b.length;d++){for(var e=b[d].parentNode,j=e.getAttribute("id"),h=b[d].getAttribute("rowspan"),k=0,i=0;i<e.childNodes.length;i++)if(e.childNodes[i].tagName==
"cell")if(e.childNodes[i]==b[d])break;else k++;this.setRowspan(j,k,h)}})};

//v.3.0 build 110707

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
To use this component please contact sales@dhtmlx.com to obtain license
*/