function changeColor(newColor,target)  
  { 
	var elem = document.getElementById(target);  
    elem.style.color = newColor;  
  }

function showUser(url,target)
{
//alert(url);

document.getElementById(target).innerHTML="";

if (url=="")
  {
  document.getElementById(target).innerHTML="No found";
  return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById(target).innerHTML=xmlhttp.responseText;
    }
	else
	{
	changeColor('red',target);
		document.getElementById(target).innerHTML="Wait Loading Data ......";
	changeColor('black',target);
	}
  }
xmlhttp.open("GET",url,true);
xmlhttp.send();
}

function showWHfromDistrict(url,target)
{
//alert(url);
try
{
var extra="&stkid=";
var sid=document.getElementById("stkofficeid");
if (sid!=null)
{
var url=url+extra+String(document.getElementById("stkofficeid").value);
}
else
var url=url+extra+"0";
//alert(url+extra);
}
catch(err)
{
	txt="There was an error on this page.\n\n";
  	txt+="Error description: " + err.message + "\n\n";
  	txt+="Click OK to continue.\n\n";
  	alert(txt);
}
document.getElementById(target).innerHTML="";

if (url=="")
  {
  document.getElementById(target).innerHTML="No found";
  return;
  }
  


if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById(target).innerHTML=xmlhttp.responseText;
    }
	else
	{
	changeColor('red',target);
		document.getElementById(target).innerHTML="Wait Loading Data ......";
	changeColor('black',target);
	}
  }
xmlhttp.open("GET",url,true);
xmlhttp.send();
}