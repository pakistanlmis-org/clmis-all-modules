<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");

include(PUBLIC_PATH . "html/header.php");
include APP_PATH."includes/classes/functions.php";
//include(PUBLIC_PATH . "html/header.php");
require(PUBLIC_PATH."FusionCharts/Code/PHP/includes/FusionCharts.php");

$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$stakeholder = $_REQUEST['stakeholder'];
$stk_name = $_REQUEST['stk_name'];
$products = $_REQUEST['products'];

//Chart caption
$caption = 'Stock Availability Rate at Districts of '.$prov_name.' - '.$stk_name.' for '.date('M-Y',strtotime($from_date));
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b3';

?>
<?php

include PUBLIC_PATH . "html/top_im.php";
?>
<link href="<?php echo PUBLIC_URL;?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //query for total districts of the stakeholder
         $qry_dist ="SELECT 
                    count( DISTINCT tbl_warehouse.dist_id ) as total_districts

                    FROM
                            tbl_warehouse
                    INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                    INNER JOIN sysuser_tab ON wh_user.sysusrrec_id = sysuser_tab.UserID
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
                    WHERE
                            stakeholder.lvl = 3 AND
                            tbl_warehouse.prov_id = $province AND
                            tbl_warehouse.stkid = $stakeholder

";
         
        $res_dist= mysql_query($qry_dist);
        $row = mysql_fetch_assoc($res_dist);
        $total_districts = $row['total_districts'];
        //Query for shipment main dashboard
  $qry = "
            SELECT
              summary_district.item_id,
              count( DISTINCT summary_district.district_id ) AS reported_districts,
	      SUM( CASE WHEN (summary_district.soh_district_store / summary_district.avg_consumption ) > 0 THEN 1 ELSE 0 END ) as nmbr_of_districts,
              itminfo_tab.itm_name
            FROM
                      summary_district
              INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
              INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
              INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
              INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
            WHERE
              summary_district.reporting_date = '".$from_date."' AND
              stakeholder.stkid = $stakeholder AND
              Province.PkLocID = $province
              AND itminfo_tab.itm_category = 1
              ";
    if(empty($products) || $products == 'null') 
           $qry .= " AND itminfo_tab.itm_id NOT IN(4,6,10,33) ";
    else
           $qry .= " AND itminfo_tab.itm_id IN(".$products.") ";
            $qry .= " 
                GROUP BY 
              summary_district.item_id
 ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
     $itm_arr = $reported_arr  = $so_arr = array();
    
    while($row = mysql_fetch_assoc($qryRes))
    {
       
        $itm_arr[$row['item_id']] = $row['itm_name'];
        
        $reported_arr[$row['item_id']] = $row['reported_districts'];
        $so_arr[$row['item_id']] = $row['nmbr_of_districts'];
    }  
    //echo '<pre>';print_r($so_arr);exit;
    $r_arr =$reverse_arr = $stake_wise_arr =array();
  
    $temp = $prods = 0;
    foreach($so_arr as $itm_id => $val)
    {
         
            $prods++;
            if(!empty($reported_arr[$itm_id]) && $reported_arr[$itm_id]>0)
                $perc = ((!empty($val)?$val:0)* 100)/$reported_arr[$itm_id];
            else
                $perc=0;
            
            $r_arr[$itm_id] = $perc;
            
           
    }
    //echo '<pre>'.$total_districts;print_r($r_arr);print_r($so_arr);print_r($itm_arr);exit;    
        
    //xml for chart
    $xmlstore = '<chart caption="'.$caption.'" yaxismaxvalue="100"  subcaption="Product Wise" xaxisname="Products" yaxisname="Percentage" numberprefix=""  palettecolors="#26C281,#FF0000,#53A5E0"  exportEnabled="1" theme="fint">';
        
    $xmlstore .= ' <categories>';
   foreach($so_arr as $itm_id => $val)
    {
        $name = (isset($itm_arr[$itm_id])?$itm_arr[$itm_id]:'0');
         $xmlstore .= '     <category label="'.$name.'"/>';  
    }
    $xmlstore .= ' </categories>';
    
        $xmlstore .= ' <dataset  seriesname="Stock Availability Rate"  showvalues="1">';
            foreach($itm_arr as $id => $name)
            {
                if(!empty($reported_arr[$itm_id]) && $reported_arr[$itm_id]>0)
                    $t_rep = $reported_arr[$itm_id];

                $t_rep = (isset($reported_arr[$id])?$reported_arr[$id]:0);
                $t_rep2 = (isset($so_arr[$id])?$so_arr[$id]:0);

                $val = (isset($r_arr[$id])?$r_arr[$id]:0);
                $xmlstore .= '     <set label="'.$name.'" tooltext="Stock available at '.$t_rep2.' out of '.$t_rep.' reported districts" value="'.(number_format(!empty($val)?$val:0,2)).'" link="JavaScript:showDrillDown_lvl2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$stakeholder.',\''.$stk_name.'\',\''.$id.'\',\''.$name.'\');"  />';
            }

        $xmlstore .= '    </dataset>';
    
        $xmlstore .= ' <dataset  seriesname="Reporting Rate (District Stores)"  renderas="line" showvalues="1">';
             foreach($so_arr as $itm_id => $val)
            {
                $rep = (isset($reported_arr[$itm_id])?$reported_arr[$itm_id]:'0');
                $tot = $total_districts;
                if(isset($tot) && $tot>0) $val = $rep * 100 / $tot;
                else $val = 0;

                $xmlstore .= '     <set label="'.$name.'" value="'.(number_format(!empty($val)?$val:0,1)).'"  />';
            }
        $xmlstore .= '  </dataset>';
    $xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/StackedColumn2DLine.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>
    
    <div class="widget widget-tabs">    
        <div class="widget-body" id="drilldown_div_2">
            
        </div>
    </div>
    
<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js" type="text/javascript"></script>
    <script>

    $(document).on('click', '.legend_block', function(){
        var clr = $(this).data('clr');
        //alert(clr);
        $('.t_rows').hide();
        $('.t_rows[data-clr='+clr+']').show();
        $('#show_all').show();
        
    });
    $(document).on('click', '#show_all', function(){
        $('.t_rows').show();
    });
    function showDrillDown_lvl2(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name) {
        //window.open("drilldown_stk_1.php?province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name ,"", "width=800,height=700");
        
        var url = 'drilldown_fp2020_b1_2.php';
        
        var dataStr='';
        dataStr += "province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name;

        $('#drilldown_div_2').html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#drilldown_div_2").html(data);
            }
        });
    
    }
    </script>

    <script src="SIPml-api.js?svn=252" type="text/javascript"> </script>
    <script type="text/javascript">

        // to avoid caching
        //if (window.location.href.indexOf("svn=") == -1) {
        //    window.location.href += (window.location.href.indexOf("?") == -1 ? "svn=236" : "&svn=229");
        //}

        var sTransferNumber;
        var oRingTone, oRingbackTone;
        var oSipStack, oSipSessionRegister, oSipSessionCall, oSipSessionTransferCall;
        var videoRemote, videoLocal, audioRemote;
        var bFullScreen = false;
        var oNotifICall;
        var viewVideoLocal, viewVideoRemote, viewLocalScreencast; // <video> (webrtc) or <div> (webrtc4all)
        var oConfigCall;
        var oReadyStateTimer;
		
		var valRealm="webcall.ntc.net.pk";
		var valPvtId="9110044";
		var valPubId="sip:9110044@175.107.60.26";
		var valPass="1234";
		var valDispName="Test Call";
		var valWebProxUrl="wss://webcall.ntc.net.pk:443/mfstwebsock";
		var valOutProxUrl="udp://175.107.60.26:5060";
		var valIceSer="[{url:'stun:webcall.ntc.net.pk:8090'}]";
		var valDisableVideo=true;
		var valRtcBreak=true;
		var valDisableDebug=false;
		var valDisableCallBtnOpt=true;
		var valDisableEralyIms=false;
		var valCacheMedia=false;
		var valMaxBand="";
		var valVideoSize="";


        C =
        {
            divKeyPadWidth: 220
        };

        window.onload = function () {
            window.console && window.console.info && window.console.info("location=" + window.location);

            videoLocal = document.getElementById("video_local");
            videoRemote = document.getElementById("video_remote");
            audioRemote = document.getElementById("audio_remote");

            document.onkeyup = onKeyUp;
            document.body.onkeyup = onKeyUp;
            divCallCtrl.onmousemove = onDivCallCtrlMouseMove;

            // set debug level
            SIPml.setDebugLevel(valDisableDebug ? "error" : "info");

            //loadCredentials();
			sipUnRegister();

            // Initialize call button
            uiBtnCallSetText("Call");

            var getPVal = function (PName) {
                var query = window.location.search.substring(1);
                var vars = query.split('&');
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split('=');
                    if (decodeURIComponent(pair[0]) === PName) {
                        return decodeURIComponent(pair[1]);
                    }
                }
                return null;
            }

            var preInit = function () {
                // set default webrtc type (before initialization)
                var s_webrtc_type = getPVal("wt");
                var s_fps = getPVal("fps");
                var s_mvs = getPVal("mvs"); // maxVideoSize
                var s_mbwu = getPVal("mbwu"); // maxBandwidthUp (kbps)
                var s_mbwd = getPVal("mbwd"); // maxBandwidthUp (kbps)
                var s_za = getPVal("za"); // ZeroArtifacts
                var s_ndb = getPVal("ndb"); // NativeDebug

                if (s_webrtc_type) SIPml.setWebRtcType(s_webrtc_type);

                // initialize SIPML5
                SIPml.init(postInit);

                // set other options after initialization
                if (s_fps) SIPml.setFps(parseFloat(s_fps));
                if (s_mvs) SIPml.setMaxVideoSize(s_mvs);
                if (s_mbwu) SIPml.setMaxBandwidthUp(parseFloat(s_mbwu));
                if (s_mbwd) SIPml.setMaxBandwidthDown(parseFloat(s_mbwd));
                if (s_za) SIPml.setZeroArtifacts(s_za === "true");
                if (s_ndb == "true") SIPml.startNativeDebug();

                //var rinningApps = SIPml.getRunningApps();
                //var _rinningApps = Base64.decode(rinningApps);
                //tsk_utils_log_info(_rinningApps);
            }

            oReadyStateTimer = setInterval(function () {
                if (document.readyState === "complete") {
                    clearInterval(oReadyStateTimer);
                    // initialize SIPML5
                    preInit();
                }
            },
            500);

            /*if (document.readyState === "complete") {
                preInit();
            }
            else {
                document.onreadystatechange = function () {
                     if (document.readyState === "complete") {
                        preInit();
                    }
                }
           }*/
		
			setTimeout(function() { loadCallOptions(); }, 1500);
		
		};
						
        function postInit() {
            // check for WebRTC support
            if (!SIPml.isWebRtcSupported()) {
                // is it chrome?
                if (SIPml.getNavigatorFriendlyName() == 'chrome') {
                    if (confirm("You're using an old Chrome version or WebRTC is not enabled.\nDo you want to see how to enable WebRTC?")) {
                        window.location = 'http://www.webrtc.org/running-the-demos';
                    }
                    else {
                        window.location = "index.html";
                    }
                    return;
                }
                else {
                    if (confirm("webrtc-everywhere extension is not installed. Do you want to install it?\nIMPORTANT: You must restart your browser after the installation.")) {
                        window.location = 'https://github.com/sarandogou/webrtc-everywhere';
                    }
                    else {
                        // Must do nothing: give the user the chance to accept the extension
                        // window.location = "index.html";
                    }
                }
            }

            // checks for WebSocket support
            if (!SIPml.isWebSocketSupported()) {
                if (confirm('Your browser don\'t support WebSockets.\nDo you want to download a WebSocket-capable browser?')) {
                    window.location = 'https://www.google.com/intl/en/chrome/browser/';
                }
                else {
                    window.location = "index.html";
                }
                return;
            }

            // FIXME: displays must be per session
            viewVideoLocal = videoLocal;
            viewVideoRemote = videoRemote;

            if (!SIPml.isWebRtcSupported()) {
                if (confirm('Your browser don\'t support WebRTC.\naudio/video calls will be disabled.\nDo you want to download a WebRTC-capable browser?')) {
                    window.location = 'https://www.google.com/intl/en/chrome/browser/';
                }
            }

            document.body.style.cursor = 'default';
            oConfigCall = {
                audio_remote: audioRemote,
                video_local: viewVideoLocal,
                video_remote: viewVideoRemote,
                screencast_window_id: 0x00000000, // entire desktop
                bandwidth: { audio: undefined, video: undefined },
                video_size: { minWidth: undefined, minHeight: undefined, maxWidth: undefined, maxHeight: undefined },
                events_listener: { events: '*', listener: onSipEventSession },
                sip_caps: [
                                { name: '+g.oma.sip-im' },
                                { name: 'language', value: '\"en,fr\"' }
                ]
            };
        }


        function loadCallOptions() {
            /*if (window.localStorage) {
                var s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.call.phone_number'))) txtPhoneNumber.value = s_value;

                txtCallStatus.innerHTML = '<i>Video ' + (valDisableVideo ? 'disabled' : 'enabled') + '</i>';
            }*/
			
			sipRegister();
        }

        function saveCallOptions() {
            if (window.localStorage) {
                window.localStorage.setItem('org.doubango.call.phone_number', txtPhoneNumber.value);
                window.localStorage.setItem('org.doubango.expert.disable_video', valDisableVideo ? "true" : "false");
            }
        }

        function loadCredentials() {
            if (window.localStorage) {
                // IE retuns 'null' if not defined
                var s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.display_name'))) txtDisplayName.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.impi'))) txtPrivateIdentity.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.impu'))) txtPublicIdentity.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.password'))) txtPassword.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.realm'))) txtRealm.value = s_value;
            }
            else {
                //txtDisplayName.value = "005";
                //txtPrivateIdentity.value = "005";
                //txtPublicIdentity.value = "sip:005@sip2sip.info";
                //txtPassword.value = "005";
                //txtRealm.value = "sip2sip.info";
                //txtPhoneNumber.value = "701020";
            }
        };

        function saveCredentials() {
            if (window.localStorage) {
                window.localStorage.setItem('org.doubango.identity.display_name', valDispName);
                window.localStorage.setItem('org.doubango.identity.impi', valPvtId);
                window.localStorage.setItem('org.doubango.identity.impu', valPubId);
                window.localStorage.setItem('org.doubango.identity.password', valPass);
                window.localStorage.setItem('org.doubango.identity.realm', valRealm);
            }
        };
		
        // sends SIP REGISTER request to login
        function sipRegister() {
            // catch exception for IE (DOM not ready)
            try {
				txtCallStatus.innerHTML = '<i>Checking all mandatory parameters...</i>';
                if (!valIceSer || !valOutProxUrl || !valWebProxUrl || !valRealm || !valPvtId || !valPubId) {
                    txtCallStatus.innerHTML = '<b>Some madatory parameters are missing. Please double check.</b>';
                    btnCall.disabled = true;
                    return;
                }
                var o_impu = tsip_uri.prototype.Parse(valPubId);
                if (!o_impu || !o_impu.s_user_name || !o_impu.s_host) {
                    txtCallStatus.innerHTML = "<b>[" + valPubId + "] is not a valid Public identity</b>";
                    btnCall.disabled = true;
                    return;
                }

                // enable notifications if not already done
                if (window.webkitNotifications && window.webkitNotifications.checkPermission() != 0) {
                    window.webkitNotifications.requestPermission();
                }

                // save credentials
                //saveCredentials();

                // update debug level to be sure new values will be used if the user haven't updated the page
                SIPml.setDebugLevel(valDisableDebug ? "error" : "info");

                // create SIP stack
                oSipStack = new SIPml.Stack({
                    realm: valRealm,
                    impi: valPvtId,
                    impu: valPubId,
                    password: valPass,
                    display_name: valDispName,
                    websocket_proxy_url: valWebProxUrl,
                    outbound_proxy_url: valOutProxUrl,
                    ice_servers: valIceSer,
                    enable_rtcweb_breaker: valRtcBreak,
                    events_listener: { events: '*', listener: onSipEventStack },
                    enable_early_ims: valDisableEralyIms, // Must be true unless you're using a real IMS network
                    enable_media_stream_cache: valCacheMedia,
                    bandwidth: valMaxBand, // could be redefined a session-level
                    video_size: valVideoSize, // could be redefined a session-level
                    sip_headers: [
                            { name: 'User-Agent', value: 'IM-client/OMA1.0 sipML5-v1.2016.03.04' },
                            { name: 'Organization', value: 'Doubango Telecom' }
                    ]
                }
                );
				
				if (oSipStack.start() != 0) {
                    txtCallStatus.innerHTML = '<b>Failed to start the SIP stack</b>';
                }
                else return;
            }
            catch (e) {
                txtCallStatus.innerHTML = "<b>2:" + e.discription + "</b>";
            }
        }

        // sends SIP REGISTER (expires=0) to logout
        function sipUnRegister() {
            if (oSipStack) {
                oSipStack.stop(); // shutdown all sessions
            }
		return true;
        }

        // makes a call (SIP INVITE)
        function sipCall(s_type) {
			if(!txtPhoneNumber.value || txtPhoneNumber.value!=0){
				if (oSipStack && !oSipSessionCall && !tsk_string_is_null_or_empty(txtPhoneNumber.value)) {
					if (s_type == 'call-screenshare') {
						if (!SIPml.isScreenShareSupported()) {
							alert('Screen sharing not supported. Are you using chrome 26+?');
							return;
						}
						if (!location.protocol.match('https')) {
							if (confirm("Screen sharing requires https://. Do you want to be redirected?")) {
								sipUnRegister();
								window.location = 'https://ns313841.ovh.net/call.htm';
							}
							return;
						}
					}
					
					btnCall.disabled = true;
					btnHangUp.disabled = false;

					/*if (window.localStorage) {
						oConfigCall.bandwidth = tsk_string_to_object(valMaxBand); // already defined at stack-level but redifined to use latest values
						oConfigCall.video_size = tsk_string_to_object(valVideoSize); // already defined at stack-level but redifined to use latest values
					}*/

					// create call session
					oSipSessionCall = oSipStack.newSession(s_type, oConfigCall);
					// make call
					if (oSipSessionCall.call(txtPhoneNumber.value) != 0) {
						oSipSessionCall = null;
						txtCallStatus.value = 'Failed to make call';
						btnCall.disabled = false;
						btnHangUp.disabled = true;
						return;
					}
					saveCallOptions();
				}
				else if (oSipSessionCall) {
					txtCallStatus.innerHTML = '<i>Connecting...</i>';
					oSipSessionCall.accept(oConfigCall);
				}
			}else{
				txtCallStatus.innerHTML = '<i>Please select a valid phone number.</i>';
			}
        }

        // Share entire desktop aor application using BFCP or WebRTC native implementation
        function sipShareScreen() {
            if (SIPml.getWebRtcType() === 'w4a') {
                // Sharing using BFCP -> requires an active session
                if (!oSipSessionCall) {
                    txtCallStatus.innerHTML = '<i>No active session</i>';
                    return;
                }
                if (oSipSessionCall.bfcpSharing) {
                    if (oSipSessionCall.stopBfcpShare(oConfigCall) != 0) {
                        txtCallStatus.value = 'Failed to stop BFCP share';
                    }
                    else {
                        oSipSessionCall.bfcpSharing = false;
                    }
                }
                else {
                    oConfigCall.screencast_window_id = 0x00000000;
                    if (oSipSessionCall.startBfcpShare(oConfigCall) != 0) {
                        txtCallStatus.value = 'Failed to start BFCP share';
                    }
                    else {
                        oSipSessionCall.bfcpSharing = true;
                    }
                }
            }
            else {
                sipCall('call-screenshare');
            }
        }

        // transfers the call
        function sipTransfer() {
            if (oSipSessionCall) {
                var s_destination = prompt('Enter destination number', '');
                if (!tsk_string_is_null_or_empty(s_destination)) {
                    btnTransfer.disabled = true;
                    if (oSipSessionCall.transfer(s_destination) != 0) {
                        txtCallStatus.innerHTML = '<i>Call transfer failed</i>';
                        btnTransfer.disabled = false;
                        return;
                    }
                    txtCallStatus.innerHTML = '<i>Transfering the call...</i>';
                }
            }
        }

        // holds or resumes the call
        function sipToggleHoldResume() {
            if (oSipSessionCall) {
                var i_ret;
                btnHoldResume.disabled = true;
                txtCallStatus.innerHTML = oSipSessionCall.bHeld ? '<i>Resuming the call...</i>' : '<i>Holding the call...</i>';
                i_ret = oSipSessionCall.bHeld ? oSipSessionCall.resume() : oSipSessionCall.hold();
                if (i_ret != 0) {
                    txtCallStatus.innerHTML = '<i>Hold / Resume failed</i>';
                    btnHoldResume.disabled = false;
                    return;
                }
            }
        }

        // Mute or Unmute the call
        function sipToggleMute() {
            if (oSipSessionCall) {
                var i_ret;
                var bMute = !oSipSessionCall.bMute;
                txtCallStatus.innerHTML = bMute ? '<i>Mute the call...</i>' : '<i>Unmute the call...</i>';
                i_ret = oSipSessionCall.mute('audio'/*could be 'video'*/, bMute);
                if (i_ret != 0) {
                    txtCallStatus.innerHTML = '<i>Mute / Unmute failed</i>';
                    return;
                }
                oSipSessionCall.bMute = bMute;
                btnMute.value = bMute ? "Unmute" : "Mute";
            }
        }

        // terminates the call (SIP BYE or CANCEL)
        function sipHangUp() {
            if (oSipSessionCall) {
                txtCallStatus.innerHTML = '<i>Terminating the call...</i>';
                oSipSessionCall.hangup({ events_listener: { events: '*', listener: onSipEventSession } });
            }
        }

        function sipSendDTMF(c) {
            if (oSipSessionCall && c) {
                if (oSipSessionCall.dtmf(c) == 0) {
                    try { dtmfTone.play(); } catch (e) { }
                }
            }
        }

        function startRingTone() {
            try { ringtone.play(); }
            catch (e) { }
        }

        function stopRingTone() {
            try { ringtone.pause(); }
            catch (e) { }
        }

        function startRingbackTone() {
            try { ringbacktone.play(); }
            catch (e) { }
        }

        function stopRingbackTone() {
            try { ringbacktone.pause(); }
            catch (e) { }
        }

        function toggleFullScreen() {
            if (videoRemote.webkitSupportsFullscreen) {
                fullScreen(!videoRemote.webkitDisplayingFullscreen);
            }
            else {
                fullScreen(!bFullScreen);
            }
        }

        function openKeyPad() {
            divKeyPad.style.visibility = 'visible';
            divKeyPad.style.left = ((document.body.clientWidth - C.divKeyPadWidth) >> 1) + 'px';
            divKeyPad.style.top = '70px';
            divGlassPanel.style.visibility = 'visible';
        }

        function closeKeyPad() {
            divKeyPad.style.left = '0px';
            divKeyPad.style.top = '0px';
            divKeyPad.style.visibility = 'hidden';
            divGlassPanel.style.visibility = 'hidden';
        }

        function fullScreen(b_fs) {
            bFullScreen = b_fs;
            if (tsk_utils_have_webrtc4native() && bFullScreen && videoRemote.webkitSupportsFullscreen) {
                if (bFullScreen) {
                    videoRemote.webkitEnterFullScreen();
                }
                else {
                    videoRemote.webkitExitFullscreen();
                }
            }
            else {
                if (tsk_utils_have_webrtc4npapi()) {
                    try { if (window.__o_display_remote) window.__o_display_remote.setFullScreen(b_fs); }
                    catch (e) { divVideo.setAttribute("class", b_fs ? "full-screen" : "normal-screen"); }
                }
                else {
                    divVideo.setAttribute("class", b_fs ? "full-screen" : "normal-screen");
                }
            }
        }

        function showNotifICall(s_number) {
            // permission already asked when we registered
            if (window.webkitNotifications && window.webkitNotifications.checkPermission() == 0) {
                if (oNotifICall) {
                    oNotifICall.cancel();
                }
                oNotifICall = window.webkitNotifications.createNotification('images/sipml-34x39.png', 'Incaming call', 'Incoming call from ' + s_number);
                oNotifICall.onclose = function () { oNotifICall = null; };
                oNotifICall.show();
            }
        }

        function onKeyUp(evt) {
            evt = (evt || window.event);
            if (evt.keyCode == 27) {
                fullScreen(false);
            }
            else if (evt.ctrlKey && evt.shiftKey) { // CTRL + SHIFT
                if (evt.keyCode == 65 || evt.keyCode == 86) { // A (65) or V (86)
                    valDisableVideo = (evt.keyCode == 65);
                    txtCallStatus.innerHTML = '<i>Video ' + (valDisableVideo ? 'disabled' : 'enabled') + '</i>';
                    window.localStorage.setItem('org.doubango.expert.disable_video', valDisableVideo);
                }
            }
        }

        function onDivCallCtrlMouseMove(evt) {
            try { // IE: DOM not ready
                if (tsk_utils_have_stream()) {
                    btnCall.disabled = (!tsk_utils_have_stream() || !oSipSessionRegister || !oSipSessionRegister.is_connected());
                    document.getElementById("divCallCtrl").onmousemove = null; // unsubscribe
                }
            }
            catch (e) { }
        }

        function uiOnConnectionEvent(b_connected, b_connecting) { // should be enum: connecting, connected, terminating, terminated
            btnCall.disabled = !(b_connected && tsk_utils_have_webrtc() && tsk_utils_have_stream());
            btnHangUp.disabled = !oSipSessionCall;
        }

        function uiVideoDisplayEvent(b_local, b_added) {
            var o_elt_video = b_local ? videoLocal : videoRemote;

            if (b_added) {
                o_elt_video.style.opacity = 1;
                uiVideoDisplayShowHide(true);
            }
            else {
                o_elt_video.style.opacity = 0;
                fullScreen(false);
            }
        }

        function uiVideoDisplayShowHide(b_show) {
            if (b_show) {
                tdVideo.style.height = '340px';
                divVideo.style.height = navigator.appName == 'Microsoft Internet Explorer' ? '100%' : '340px';
            }
            else {
                tdVideo.style.height = '0px';
                divVideo.style.height = '0px';
            }
            btnFullScreen.disabled = !b_show;
        }
		
        function uiDisableCallOptions() {
            if (window.localStorage) {
                valDisableCallBtnOpt=true;
                uiBtnCallSetText('Call');
                alert('Use expert view to enable the options again (/!\\requires re-loading the page)');
            }
        }

        function uiBtnCallSetText(s_text) {
            switch (s_text) {
                case "Call":
                    {
                        var bDisableCallBtnOptions = (window.localStorage && valDisableCallBtnOpt);
                        btnCall.value = btnCall.innerHTML = bDisableCallBtnOptions ? 'Call' : 'Call <span id="spanCaret" class="caret">';
                        btnCall.setAttribute("class", bDisableCallBtnOptions ? "btn btn-primary" : "btn btn-primary dropdown-toggle");
                        btnCall.onclick = bDisableCallBtnOptions ? function () { sipCall(valDisableVideo ? 'call-audio' : 'call-audiovideo'); } : null;
                        ulCallOptions.style.visibility = bDisableCallBtnOptions ? "hidden" : "visible";
                        if (!bDisableCallBtnOptions && ulCallOptions.parentNode != divBtnCallGroup) {
                            divBtnCallGroup.appendChild(ulCallOptions);
                        }
                        else if (bDisableCallBtnOptions && ulCallOptions.parentNode == divBtnCallGroup) {
                            document.body.appendChild(ulCallOptions);
                        }

                        break;
                    }
                default:
                    {
                        btnCall.value = btnCall.innerHTML = s_text;
                        btnCall.setAttribute("class", "btn btn-primary");
                        btnCall.onclick = function () { sipCall(valDisableVideo ? 'call-audio' : 'call-audiovideo'); };
                        ulCallOptions.style.visibility = "hidden";
                        if (ulCallOptions.parentNode == divBtnCallGroup) {
                            document.body.appendChild(ulCallOptions);
                        }
                        break;
                    }
            }
        }

        function uiCallTerminated(s_description) {
            uiBtnCallSetText("Call");
            btnHangUp.value = 'HangUp';
            btnHoldResume.value = 'hold';
            btnMute.value = "Mute";
            btnCall.disabled = false;
            btnHangUp.disabled = true;
            if (window.btnBFCP) window.btnBFCP.disabled = true;

            oSipSessionCall = null;

            stopRingbackTone();
            stopRingTone();

            txtCallStatus.innerHTML = "<i>" + s_description + "</i>";
            uiVideoDisplayShowHide(false);
            divCallOptions.style.opacity = 0;

            if (oNotifICall) {
                oNotifICall.cancel();
                oNotifICall = null;
            }

            uiVideoDisplayEvent(false, false);
            uiVideoDisplayEvent(true, false);

            setTimeout(function () { if (!oSipSessionCall) txtCallStatus.innerHTML = ''; }, 3000);
        }

        // Callback function for SIP Stacks
        function onSipEventStack(e /*SIPml.Stack.Event*/) {
            tsk_utils_log_info('==stack event = ' + e.type);			
            switch (e.type) {
                case 'started':
                    {
                        // catch exception for IE (DOM not ready)
                        try {
                            // LogIn (REGISTER) as soon as the stack finish starting
                            oSipSessionRegister = this.newSession('register', {
                                expires: 200,
                                events_listener: { events: '*', listener: onSipEventSession },
                                sip_caps: [
                                            { name: '+g.oma.sip-im', value: null },
                                            //{ name: '+sip.ice' }, // rfc5768: FIXME doesn't work with Polycom TelePresence
                                            { name: '+audio', value: null },
                                            { name: 'language', value: '\"en,fr\"' }
                                ]
                            });
                            oSipSessionRegister.register();
                        }
                        catch (e) {
                            txtCallStatus.value = txtCallStatus.innerHTML = "<b>1:" + e.discription + "</b>";
                        }
                        break;
                    }
                case 'stopping': case 'stopped': case 'failed_to_start': case 'failed_to_stop':
                    {
                        var bFailure = (e.type == 'failed_to_start') || (e.type == 'failed_to_stop');
                        oSipStack = null;
                        oSipSessionRegister = null;
                        oSipSessionCall = null;

                        uiOnConnectionEvent(false, false);

                        stopRingbackTone();
                        stopRingTone();

                        uiVideoDisplayShowHide(false);
                        divCallOptions.style.opacity = 0;

                        //txtCallStatus.innerHTML = '';
                        txtCallStatus.innerHTML = bFailure ? "<i>Disconnected: <b>" + e.description + "</b></i>" : "<i>Disconnected</i>";
                        break;
                    }

                case 'i_new_call':
                    {
                        if (oSipSessionCall) {
                            // do not accept the incoming call if we're already 'in call'
                            e.newSession.hangup(); // comment this line for multi-line support
                        }
                        else {
                            oSipSessionCall = e.newSession;
                            // start listening for events
                            oSipSessionCall.setConfiguration(oConfigCall);

                            uiBtnCallSetText('Answer');
                            btnHangUp.value = 'Reject';
                            btnCall.disabled = false;
                            btnHangUp.disabled = false;

                            startRingTone();

                            var sRemoteNumber = (oSipSessionCall.getRemoteFriendlyName() || 'unknown');
                            txtCallStatus.innerHTML = "<i>Incoming call from [<b>" + sRemoteNumber + "</b>]</i>";
                            showNotifICall(sRemoteNumber);
                        }
                        break;
                    }

                case 'm_permission_requested':
                    {
                        divGlassPanel.style.visibility = 'visible';
                        break;
                    }
                case 'm_permission_accepted':
                case 'm_permission_refused':
                    {
                        divGlassPanel.style.visibility = 'hidden';
                        if (e.type == 'm_permission_refused') {
                            uiCallTerminated('Media stream permission denied');
                        }
                        break;
                    }

                case 'starting': default: break;
            }
        };

        // Callback function for SIP sessions (INVITE, REGISTER, MESSAGE...)
        function onSipEventSession(e /* SIPml.Session.Event */) {
            tsk_utils_log_info('==session event = ' + e.type);
            switch (e.type) {
                case 'connecting': case 'connected':
                    {
                        var bConnected = (e.type == 'connected');
                        if (e.session == oSipSessionRegister) {
                            uiOnConnectionEvent(bConnected, !bConnected);
                            txtCallStatus.innerHTML = "<i>" + e.description + "</i>";
                        }
                        else if (e.session == oSipSessionCall) {
                            btnHangUp.value = 'HangUp';
                            btnCall.disabled = true;
                            btnHangUp.disabled = false;
                            btnTransfer.disabled = false;
                            if (window.btnBFCP) window.btnBFCP.disabled = false;

                            if (bConnected) {
                                stopRingbackTone();
                                stopRingTone();

                                if (oNotifICall) {
                                    oNotifICall.cancel();
                                    oNotifICall = null;
                                }
                            }

                            txtCallStatus.innerHTML = "<i>" + e.description + "</i>";
                            //divCallOptions.style.opacity = bConnected ? 1 : 0;

                            if (SIPml.isWebRtc4AllSupported()) { // IE don't provide stream callback
                                uiVideoDisplayEvent(false, true);
                                uiVideoDisplayEvent(true, true);
                            }
                        }
                        break;
                    } // 'connecting' | 'connected'
                case 'terminating': case 'terminated':
                    {
                        if (e.session == oSipSessionRegister) {
                            uiOnConnectionEvent(false, false);

                            oSipSessionCall = null;
                            oSipSessionRegister = null;

                            txtCallStatus.innerHTML = "<i>" + e.description + "</i>";
                        }
                        else if (e.session == oSipSessionCall) {
                            uiCallTerminated(e.description);
                        }
                        break;
                    } // 'terminating' | 'terminated'

                case 'm_stream_video_local_added':
                    {
                        if (e.session == oSipSessionCall) {
                            uiVideoDisplayEvent(true, true);
                        }
                        break;
                    }
                case 'm_stream_video_local_removed':
                    {
                        if (e.session == oSipSessionCall) {
                            uiVideoDisplayEvent(true, false);
                        }
                        break;
                    }
                case 'm_stream_video_remote_added':
                    {
                        if (e.session == oSipSessionCall) {
                            uiVideoDisplayEvent(false, true);
                        }
                        break;
                    }
                case 'm_stream_video_remote_removed':
                    {
                        if (e.session == oSipSessionCall) {
                            uiVideoDisplayEvent(false, false);
                        }
                        break;
                    }

                case 'm_stream_audio_local_added':
                case 'm_stream_audio_local_removed':
                case 'm_stream_audio_remote_added':
                case 'm_stream_audio_remote_removed':
                    {
                        break;
                    }

                case 'i_ect_new_call':
                    {
                        oSipSessionTransferCall = e.session;
                        break;
                    }

                case 'i_ao_request':
                    {
                        if (e.session == oSipSessionCall) {
                            var iSipResponseCode = e.getSipResponseCode();
                            if (iSipResponseCode == 180 || iSipResponseCode == 183) {
                                startRingbackTone();
                                txtCallStatus.innerHTML = '<i>Remote ringing...</i>';
                            }
                        }
                        break;
                    }

                case 'm_early_media':
                    {
                        if (e.session == oSipSessionCall) {
                            stopRingbackTone();
                            stopRingTone();
                            txtCallStatus.innerHTML = '<i>Early media started</i>';
                        }
                        break;
                    }

                case 'm_local_hold_ok':
                    {
                        if (e.session == oSipSessionCall) {
                            if (oSipSessionCall.bTransfering) {
                                oSipSessionCall.bTransfering = false;
                                // this.AVSession.TransferCall(this.transferUri);
                            }
                            btnHoldResume.value = 'Resume';
                            btnHoldResume.disabled = false;
                            txtCallStatus.innerHTML = '<i>Call placed on hold</i>';
                            oSipSessionCall.bHeld = true;
                        }
                        break;
                    }
                case 'm_local_hold_nok':
                    {
                        if (e.session == oSipSessionCall) {
                            oSipSessionCall.bTransfering = false;
                            btnHoldResume.value = 'Hold';
                            btnHoldResume.disabled = false;
                            txtCallStatus.innerHTML = '<i>Failed to place remote party on hold</i>';
                        }
                        break;
                    }
                case 'm_local_resume_ok':
                    {
                        if (e.session == oSipSessionCall) {
                            oSipSessionCall.bTransfering = false;
                            btnHoldResume.value = 'Hold';
                            btnHoldResume.disabled = false;
                            txtCallStatus.innerHTML = '<i>Call taken off hold</i>';
                            oSipSessionCall.bHeld = false;

                            if (SIPml.isWebRtc4AllSupported()) { // IE don't provide stream callback yet
                                uiVideoDisplayEvent(false, true);
                                uiVideoDisplayEvent(true, true);
                            }
                        }
                        break;
                    }
                case 'm_local_resume_nok':
                    {
                        if (e.session == oSipSessionCall) {
                            oSipSessionCall.bTransfering = false;
                            btnHoldResume.disabled = false;
                            txtCallStatus.innerHTML = '<i>Failed to unhold call</i>';
                        }
                        break;
                    }
                case 'm_remote_hold':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = '<i>Placed on hold by remote party</i>';
                        }
                        break;
                    }
                case 'm_remote_resume':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = '<i>Taken off hold by remote party</i>';
                        }
                        break;
                    }
                case 'm_bfcp_info':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = 'BFCP Info: <i>' + e.description + '</i>';
                        }
                        break;
                    }

                case 'o_ect_trying':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = '<i>Call transfer in progress...</i>';
                        }
                        break;
                    }
                case 'o_ect_accepted':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = '<i>Call transfer accepted</i>';
                        }
                        break;
                    }
                case 'o_ect_completed':
                case 'i_ect_completed':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = '<i>Call transfer completed</i>';
                            btnTransfer.disabled = false;
                            if (oSipSessionTransferCall) {
                                oSipSessionCall = oSipSessionTransferCall;
                            }
                            oSipSessionTransferCall = null;
                        }
                        break;
                    }
                case 'o_ect_failed':
                case 'i_ect_failed':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = '<i>Call transfer failed</i>';
                            btnTransfer.disabled = false;
                        }
                        break;
                    }
                case 'o_ect_notify':
                case 'i_ect_notify':
                    {
                        if (e.session == oSipSessionCall) {
                            txtCallStatus.innerHTML = "<i>Call Transfer: <b>" + e.getSipResponseCode() + " " + e.description + "</b></i>";
                            if (e.getSipResponseCode() >= 300) {
                                if (oSipSessionCall.bHeld) {
                                    oSipSessionCall.resume();
                                }
                                btnTransfer.disabled = false;
                            }
                        }
                        break;
                    }
                case 'i_ect_requested':
                    {
                        if (e.session == oSipSessionCall) {
                            var s_message = "Do you accept call transfer to [" + e.getTransferDestinationFriendlyName() + "]?";//FIXME
                            if (confirm(s_message)) {
                                txtCallStatus.innerHTML = "<i>Call transfer in progress...</i>";
                                oSipSessionCall.acceptTransfer();
                                break;
                            }
                            oSipSessionCall.rejectTransfer();
                        }
                        break;
                    }
            }
        }

    </script>
    <div id="divCallCtrl" class="centeralign" style='display:table-cell; vertical-align:middle'>
<label class="alert" align="center" id="txtCallStatus"><i>Please wait...</i>
                </label>
        </div>
    
    <div class="btn-toolbar" style="margin: 0; vertical-align:middle">
                                <!--div class="btn-group">
                                    <input type="button" id="btnBFCP" style="margin: 0; vertical-align:middle; height: 100%;" class="btn btn-primary" value="BFCP" onclick='sipShareScreen();' disabled />
                                </div-->
                                <div id="divBtnCallGroup" class="btn-group">
                                    <button id="btnCall" class="btn btn-primary" data-toggle="dropdown">Call</button>
                                     <button id="btnCall" class="btn btn-primary" data-toggle="dropdown">Call</button>
                                      <button id="btnCall" class="btn btn-primary" data-toggle="dropdown">Call</button>
                                       <button id="btnCall" class="btn btn-primary" data-toggle="dropdown">Call</button>
                                </div>&nbsp;&nbsp;
                                <div class="btn-group">
                                    <input type="button" id="btnHangUp" style="margin: 0; vertical-align:middle; height: 100%;" class="btn btn-primary" value="HangUp" onclick='sipHangUp();' disabled />
                                </div>
                            </div>
    
    <ul id="ulCallOptions" class="dropdown-menu" style="visibility:hidden">
        <li><a href="#" onclick='sipCall("call-audio");'>Audio</a></li>
        <li><a href="#" onclick='sipCall("call-audiovideo");'>Video</a></li>
        <li id='liScreenShare'><a href="#" onclick='sipShareScreen();'>Screen Share</a></li>
        <li class="divider"></li>
        <li><a href="#" onclick='uiDisableCallOptions();'><b>Disable these options</b></a></li>
    </ul>
    
    <input type="text" style="width: 100%; height:100%;" id="txtPhoneNumber" readonly="readonly" value="03339652360" placeholder="Enter phone number to call" /> 