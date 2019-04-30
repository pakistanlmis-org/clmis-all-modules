var i=0;
function PrintReport()
	{
		document.body.offsetHeight;
		window.print();
	}
function saveit(file_name)
	{		
		document.execCommand('SaveAs',true,file_name)
		return false;
	}

function Blink(Blinkelement)
	{		
		if(i==0)
			{
				Blinkelement.style.display ='none';								
				i++;
			}
		else
			{							
				Blinkelement.style.display ='';
				i--;
			}
	}
function ChangeStatus(message) 
		{
			window.status = message
		}


function SetFocus(ele)
		{
			document.getElementById(ele).focus()
		}

	//Load Menu Images

		onImageArray = new Array()
		offImageArray = new Array()
/*
			for(i=0;i<9;i++)
				{
					onImageArray[i] = new Image();
					offImageArray[i] = new Image();
					offImageArray[i].src="plmis_img/Menu/"+i+"_N.jpg";
					onImageArray[i].src ="plmis_img/Menu/"+i+"_O.jpg";
				}
*/
	//Load Menu Images

	//Menu Image Change Function
		function ChangeImageOn(imagename, i)
			{
				document [imagename].src = onImageArray[i].src
			}
		function ChangeImageOff(imagename, i)
			{
				document [imagename].src = offImageArray[i].src
			}
	//Menu Image Change Function

		function CheckNumeric()
			{
				if ((event.keyCode>=48)&&(event.keyCode<=57))
					{
						event.returnValue = true;
					}
				else
					{
						event.returnValue = false;
					}
			}

		function CheckNumericWDot()
			{
				
				if (event.keyCode==46)
					{
						event.returnValue = true;
					}
				else if ((event.keyCode>=48)&&(event.keyCode<=57))
					{
						event.returnValue = true;
					}
				else
					{
						event.returnValue = false;
					}
			}

var message = "You Can't Click The Right Mouse Button !!!";




function numbersonly(myfield, e, dec)
{
var key;
var keychar;

if (window.event)
   key = window.event.keyCode;
else if (e)
   key = e.which;
else
   return true;
keychar = String.fromCharCode(key);

// control keys
//|| (key==13) enter key
if ((key==null) || (key==0) || (key==8) || 
    (key==9)  || (key==27) )
   return true;

// numbers
else if ((("0123456789").indexOf(keychar) > -1))
   return true;

// decimal point jump
else if (dec && (keychar == "."))
   {
   myfield.form.elements[dec].focus();
   return false;
   }
else
   return false;
}


/*
function click(e)
	{
		if (document.all)
			{
			    if (event.button == 2||event.button == 3)
			    {
			      alert(message);
			      return false;
			    }
			}

		if (document.layers)
			{
				if (e.which == 3)
				{
			      alert(message);
				  return false;
				}
			}
	}

if (document.layers)
	{
		 document.captureEvents(Event.MOUSEDOWN);
	}
document.onmousedown=click;
					
*/