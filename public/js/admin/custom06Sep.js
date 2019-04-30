// JavaScript Document

$(function() {	
	// Change password Validation
	$("#form1").validate({
		rules: {
			old_pass: {
				required: true,
				equalTo: '#old_pass_hid'
			},
			new_pass: 'required',
			confirm_pas: {
				required: true,
				equalTo: '#new_pass'
			}
		},
		messages: {
			old_pass: {
				required: 'Enter old password.',
				equalTo: 'Wrong old password.'
			},
			new_pass: 'Enter new password.',
			confirm_pas: {
				required: 'Confirm new password.',
				equalTo: 'Enter the same password as above.'
			}
		}
	});
	
	//mos scale form validation
	$("#frmDatamos").validate({
		 rules: {
			 itm_id:'required',
			 shortterm:'required',
			 longterm:'required',
			 sclstart:{
				required:true,
				number:true 
			 },
			 sclsend:{
				required:true,
				number:true  
			 },
			 colorcode:'required'	 
		 },
		 messages:{
			 itm_id:'Enter product',
			 shortterm:'Enter short term',
			 longterm:'Enter long term',
			 sclstart:{
				required:'Enter scale start',
				number:'Enter numbers only'
			 },
			 sclsend:{
				required:'Enter scale end',
				number:'Enter numbers only' 
			 },
			 colorcode:'Enter code'
		 }
	});
	
	//manage product form validation
	$("#manageitems").validate({
		 rules: {
			 txtStkName1:'required',
			 txtStkName2:'required',
			 txtStkName4:'required',
			 txtStkName6:'required',
			 'stkid[]':'required',
			 'select2[]':'required'
		 },
		 messages:{
			 txtStkName1:'Enter Product Name',
			 txtStkName2:'Enter Product Type',
			 txtStkName4:'Enter Category',
			 txtStkName6:'Enter Status',
			 'stkid[]':'Select stakeholder',
			 'select2[]':'Select Product Group'
		 }
		 
	});
	
	//validation manage oitems of groups
	$("#manageitemsofgroup").validate({
		rules:{
			'ItemID[]':"required"
		},
		messages:{
			'ItemID[]':"Select Products"
		}
	});
	
	//validation manage item groups
	$("#manageitemgroups").validate({
		rules:{
			ItemGroupName:"required"
		},
		messages:{
			ItemGroupName:"Enter Product's Group"
		}
	});
	
	//validation manage stakeholder
	$("#Managestakeholdersaction").validate({
		rules:{
			txtStkName:"required"
		},
		messages:{
			txtStkName:"Enter Stakeholder Name"
		}
	});
	
	//validation manage stakeholder office type
	$("#ManageStakeholdersOfficeTypesAction").validate({
		rules:{
			txtStktype:"required",
			Stakeholders:"required",
			lstlvl:"required"
		},
		messages:{
			txtStktype:"Enter office type",
			Stakeholders:"Choose stakeholder",
			lstlvl:"Choose list level"
		}
	});
	
	
	//Manage stakeholder item action
	$("#ManageStakeholdersItemsAction").validate({
		rules:{
			'ItemID[]':"required"
		},
		messages:{
			'ItemID[]':"Select Products"
		}
	});
	
	//Manage Filter for user
	$("#managefilter").validate({
		rules:{
			providz:"required"
		},
		messages:{
			providz:"Select Provice first"
		}
	});
	
	//Manage Mos Manage
	$("#managefiltermos").validate({
		rules:{
			prodidz:"required"
		},
		messages:{
			prodidz:"Select Product first"
		}
	});
	
	//Manage Mos Manage
	$("#managefilterwarehouse").validate({
		rules:{
			provids:"required"
		},
		messages:{
			provids:"Select Province first"
		}
	});
	//manage warehouses validation
	
		$("#managewarehouses").validate({
			 rules: {
				 Stakeholders:'required',
				 StakeholdersOffices:'required',
				 Provinces:'required',
				 districts:'required',
				 wh_name:'required'
			 },
			 messages:{
				 Stakeholders:'Select Stakeholder',
				 StakeholdersOffices:'Select Stakeholder Office',
				 Provinces:'Select Province',
				 districts:'Select District',
				 wh_name:'Enter warehouse name'
			 }
			 
		});
		
	//manage profile form
		$("#manage_profile").validate({
			 rules: {
				 usrlogin_id:'required',
				 full_name:'required',
				 email_id:'required',
				 phone_no:'required'
			 },
			 messages:{
				 usrlogin_id:'Enter Login ID',
				 full_name:'Enter Full Name',
				 email_id:'Enter Email ID',
				 phone_no:'Enter Phone No'
			 }
			 
		});	
	
		//manage content form
		$("#content_form").validate({
			 rules: {
				 page_title:'required',
				 page_heading:'required' 
			 },
			 messages:{
				 page_title:'Enter Page Title',
				 page_heading:'Enter Heading'
			 }
			 
		});	
	
	
	//manage user form validation
	$("#manageuser").validate({
		 rules: {
			 usrlogin_id:"required",
			 txtStkName2:{
				required: true
			 },
			 txtStkName22:{
				 required:true,
				 equalTo: "#txtStkName2"
			 },
			 full_name:"required",
			 sysusr_dept:"required",
			 sysusr_deg:"required",
			 sysusr_type:"required",
			 email_id:{
				 required:true,
				 email:true
			 },
			 phone_no:{
				 required:true,
				 digits:true
			 }
		 },
		 messages:{
			 usrlogin_id:"Enter Username",
			 txtStkName2:{
			 required: "Enter Password First"
			 },
			 txtStkName22:{
				 required:"Enter password again",
				equalTo: "Enter same password as previous"
			 },
			 full_name:"Enter name first",
			  email_id:{
				 required:"Enter your Email ID",
				 email:"Enter valid Email ID"
			 },
			 phone_no:{
				required:"Enter your phone/cell no",
				digits:"Enter numbers only"
			 }
		 }
	});
	
	//manage user form validation
	$("#manageadminuser").validate({
		 rules: {
			 usrlogin_id:"required",
			 txtStkName2:{
				required: true
			 },
			 txtStkName22:{
				 required:true,
				 equalTo: "#txtStkName2"
			 },
			 full_name:"required",
			  email_id:{
				 required:true,
				 email:true
			 },
			 phone_no:{
				 required:true,
				 digits:true
			 },
			 sysusr_type:"required"
			 		 },
		 messages:{
			 usrlogin_id:"Enter Username",
			 txtStkName2:{
			 required: "Enter Password First"
			 },
			 txtStkName22:{
				 required:"Enter password again",
				equalTo: "Enter same password as previous"
			 },
			 full_name:"Enter name first",
			  email_id:{
				 required:"Enter your Email ID",
				 email:"Enter valid Email ID"
			 },
			 phone_no:{
				required:"Enter your phone/cell no",
				digits:"Enter numbers only"
			 }
		 }
	});
	
		//manage product type form
		$("#manageitemtypes").validate({
			 rules: {
				 producttype:'required'
			 },
			 messages:{
				 producttype:'Enter Product Type'
			 }
		});	
	
		//manage product type form
		$("#manageitemcategory").validate({
			 rules: {
				 productcategory:'required'
			 },
			 messages:{
				 productcategory:'Enter Product Category'
			 }
		});
	   
	    //manage product status form
		$("#manageitemstatus").validate({
			 rules: {
				 productstatus:'required'
			 },
			 messages:{
				 productstatus:'Enter Product Status'
			 }
		});
	
	    //manage location status form
		$("#managelocation").validate({
			 rules: {
				 loc_level:'required',
				 loc_type:'required',
				 provinces:'required',
				 loc_name:'required'
				 
			 },
			 messages:{
				 loc_level:'Select Level',
				 loc_type:'Select Type',
				 provinces:'Select Province',
				 loc_name:'Enter Location Name'
			 }
		});
	
	
	
	//load last 3 months
	$('#wharehouse_id').change(function(){
		$('#showGrid').html('');
		show3Months();
	});
	
	

	
	
});

function show3Months(){
	//$('#showGrid').html('');
	var wh_id = $('#wharehouse_id').val();
	if ( wh_id != '' ){
		$.ajax({
			type: "POST",
			url: "loadLast3Months.php",
			data: "wharehouse_id="+wh_id,
			success: function(data){
				$('#showMonths').html(data);
			}  
		});
	}else {
		$('#showMonths').html('');
	}
}





