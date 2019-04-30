//Start Function For Create New Item Type Input Box
function CreateNewBox()
	{
		element=document.getElementById('Newitm_type');
		if (document.frmData.itm_type.value== "New")
			{			
				element.style.display="";
				document.frmData.Newitm_type.focus();
			}
		else
			{
				element.style.display="None";
			}
	}
//End Function For Create New Item Type Input Box

//Start Function For New Item Entry Validation
function ValidateAddCMS()
	{
		var err =0;
		if (document.frmData.title.value== "")
			{
				alert("Please enter the title.")
				document.frmData.title.focus();
				err =1;
				return false;
			}
		if (err==0)
			{	
				var name = window.parent.location;
				window.parent.location=name;
			}
		
	}
//End Function For New Item Entry Validation


//Start Function For Item Edit Validation
function ValidateEditCMS()
	{
		var err =0;
		if (document.frmData.title.value== "")
			{
				alert("Please enter the title.")
				document.frmData.title.focus();
				err =1;
				return false;
			}
		
		if(err==0)
			{
				//alert("here");
				var name = window.parent.location;
				//alert(name + "  this is");
				//alert(name);
			//window.parent.location="http://localhost/paklmis/cpanel.php?pageid=item";
			window.parent.location=name;
			}
   // window.parent.refresh();
    }
//End Function For Item Edit Validation



