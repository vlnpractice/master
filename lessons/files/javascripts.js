<!--


function add_to_fav(siteurl,site_name) {
if (document.all) window.external.AddFavorite(siteurl,site_name);
else alert("Your browser doesn't support this function. Please press Ctrl");
}


function show_hide_div(state,object) {
//alert(object);
if(state) { object.style.display="block"; }
else { object.style.display="none"; }
}


function show_hide_div_id(state,object_id) {
//	alert(state+'---'+object_id);
var object = document.getElementById(object_id);
if(state) { object.style.display="block"; }
else { object.style.display="none"; }
}


function check_show_hide_div(object_id) {
//alert(object_id);
var object = document.getElementById(object_id);
//alert(object);
var current_display = object.style.display;
if (current_display=='block') { object.style.display="none"; }
else { object.style.display="block"; }
}

function check_show_hide_div1(object_id) {
var object = document.getElementById(object_id);
var current_display = object.style.display;
if (current_display=='block') { object.style.display="none"; }
else { object.style.display="block"; }
}

function show_gallery(object_n) {
for (x=1;x<=50;x++)
{ var this_id = 'image-'+x;
  if (document.getElementById(this_id)) show_hide_div_id(0,this_id);
}
show_hide_div_id(1,object_n);
}


function show_top_submenu(object_n) {
for (x=1;x<=15;x++)
{ var this_id = 'top_submenu_'+x;
  if (document.getElementById(this_id)) show_hide_div_id(0,this_id);
}
show_hide_div_id(1,'top_submenu_'+object_n);
}

function show_submenu(object_n) {
for (x=1;x<=5;x++)
{ var this_id = 'search_submenu_'+x;
  if (document.getElementById(this_id)) show_hide_div_id(0,this_id);
}
for (x=1;x<=15;x++)
{ var this_id = 'details_submenu_'+x;
  if (document.getElementById(this_id)) show_hide_div_id(0,this_id);
}
show_hide_div_id(1,object_n);
}

function show_simple_search_submenu(object_n) {
for (x=1;x<=10;x++)
{ var this_id = 'simple_search_submenu_'+x;
  if (document.getElementById(this_id)) show_hide_div_id(0,this_id);
}
show_hide_div_id(1,object_n);
}

function show_waiting(object_id) {
	//alert(object_id);
var object = document.getElementById(object_id);
object.innerHTML = '<div><br><br><br><img src="'+site_url+'/images/waiting.gif" border="0"><br><br><br></div>';
}


/*###############################################################################*/

function show_ajax_content(url,elementid) {
if (window.XMLHttpRequest)
{ // IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
}
else
{ // code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{ if (xmlhttp.readyState==4 && xmlhttp.status==200)
  { document.getElementById(elementid).innerHTML=xmlhttp.responseText; }
}
xmlhttp.open("GET",url,true);
xmlhttp.send();
}


function process_ajax_form(form_id,php_script_url,element_id) {
var http_request = false;
//alert(form_id+'---'+php_script_url+'---'+element_id);
var request = '';
var form_element = document.getElementById(form_id);
if (form_element)
{ for(i=0; i<form_element.elements.length; i++)
  { request += form_element.elements[i].name+"="+form_element.elements[i].value+'&';
  }
}
if (window.XMLHttpRequest) { http_request = new XMLHttpRequest(); } 
else if (window.ActiveXObject)
{ try { http_request = new ActiveXObject("Msxml2.XMLHTTP"); }
  catch (eror) { http_request = new ActiveXObject("Microsoft.XMLHTTP"); }
}
http_request.onreadystatechange = function() { show_ajax_result(http_request,element_id); };
http_request.open('POST',php_script_url,true);
http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
http_request.send(request);
}


/*###############################################################################*/



function parse_ajax_request(form_id,php_script_url,element_id) {
var http_request = false;
//alert(form_id+'---'+php_script_url+'---'+element_id);
var request = '';
if (form_id)
{ for(i=0; i<form_id.elements.length; i++)
  { request += form_id.elements[i].name+"="+form_id.elements[i].value+'&';
    //document.write("The field name is: " + form_id.elements[i].name + " and its value is: " + form_id.elements[i].value + ".<br />");
  }
}
//alert(request);
//alert(form_id.string.value);
//var string = form_id.string.value;
//var request = "string="+string;
if (window.XMLHttpRequest) { http_request = new XMLHttpRequest(); } 
else if (window.ActiveXObject)
{ try { http_request = new ActiveXObject("Msxml2.XMLHTTP"); }
  catch (eror) { http_request = new ActiveXObject("Microsoft.XMLHTTP"); }
}
http_request.onreadystatechange = function() { show_ajax_result(http_request,element_id); };
http_request.open('GET',php_script_url+'?'+request,true);
http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
http_request.send(request);
}


function show_ajax_result(http_request,element_id) {
	//alert(http_request.readyState+'----'+element_id);

if (http_request.readyState == 4)
{ if (http_request.status == 200) {
  //alert(element_id);
  document.getElementById(element_id).innerHTML  = http_request.responseText;
  }
  else { alert('An error has occurred. Please try again.'); }
}
}

/*###############################################################################*/

function Set_Cookie(name,value,expires)
{
var path = '/';
var domain = '';
var secure = '';
// set time, it's in milliseconds
var today = new Date();
today.setTime( today.getTime() );

/*
if the expires variable is set, make the correct
expires time, the current script below will set
it for x number of days, to make it for hours,
delete * 24, for minutes, delete * 60 * 24
*/
if ( expires )
{
expires = expires * 1000 * 60 * 60 * 24;
}
var expires_date = new Date( today.getTime() + (expires) );

document.cookie = name + "=" +escape( value ) +
( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
( ( path ) ? ";path=" + path : "" ) +
( ( domain ) ? ";domain=" + domain : "" ) +
( ( secure ) ? ";secure" : "" );
}
function Get_Cookie( check_name ) {
	// first we'll split this cookie up into name/value pairs
	// note: document.cookie only returns name=value, not the other components
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false; // set boolean t/f default f

	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );


		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			// we need to handle case where cookie has no value but exists (no = sign, that is):
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			// note that in cases where cookie is initialized but no value, null is returned
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found )
	{
		return null;
	}
}
function Delete_Cookie(name) {
var path = '/';
var domain = '';
if ( Get_Cookie( name ) ) document.cookie = name + "=" +
( ( path ) ? ";path=" + path : "") +
( ( domain ) ? ";domain=" + domain : "" ) +
";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

/*###############################################################################*/



function smoothHeight(id,curH,targetH,stepH,mode) {
var smooth_timer;
diff = targetH - curH;
if (diff != 0)
{ newH = (diff > 0) ? curH + stepH : curH - stepH;
  ((document.getElementById) ? document.getElementById(id) : eval("document.all['" + id + "']")).style.height = newH + "px";
  if (smooth_timer) window.clearTimeout(smooth_timer);
  smooth_timer = window.setTimeout( "smoothHeight('" + id + "'," + newH + "," + targetH + "," + stepH + ",'" + mode + "')", 20 );
}
else if (mode != "o") ((document.getElementById) ? document.getElementById(mode) : eval("document.all['" + mode + "']")).style.display="none";
}

function mini_preview(number,url) {
var tr_object = (document.getElementById) ? document.getElementById('row'+number) : eval("document.all['row"+number+"']");
var nameObj = (document.getElementById) ? document.getElementById('name'+number) : eval("document.all['name"+number+"']");
var iframe_object = (document.getElementById) ? document.getElementById('iframe'+number) : eval("document.all['iframe"+number+"']");
if (tr_object != null)
{ if (tr_object.style.display=="none")
  { tr_object.style.display="";
    if (!iframe_object.src) iframe_object.src = url;
    smoothHeight('iframe'+number,0,400,80,'o');
  }
  else smoothHeight('iframe'+number,400,0,80,'row'+number);
}
}
function mini_preview_rss(number,url) {
var tr_object = (document.getElementById) ? document.getElementById('rowrss'+number) : eval("document.all['rowrss"+number+"']");
var nameObj = (document.getElementById) ? document.getElementById('namerss'+number) : eval("document.all['namerss"+number+"']");
var iframe_object = (document.getElementById) ? document.getElementById('iframerss'+number) : eval("document.all['iframerss"+number+"']");
if (tr_object != null)
{ if (tr_object.style.display=="none")
  { tr_object.style.display="";
    if (!iframe_object.src) iframe_object.src = url;
    smoothHeight('iframerss'+number,0,210,42,'o');
  }
  else smoothHeight('iframerss'+number,210,0,42,'rowrss'+number);
}
}

function mini_iframe(number,url) {
var tr_object = (document.getElementById) ? document.getElementById('mini_row'+number) : eval("document.all['mini_row"+number+"']");
var nameObj = (document.getElementById) ? document.getElementById('name'+number) : eval("document.all['name"+number+"']");
var iframe_object = (document.getElementById) ? document.getElementById('mini_iframe'+number) : eval("document.all['mini_iframe"+number+"']");
if (tr_object != null)
{ if (tr_object.style.display=="none")
  { tr_object.style.display="";
    if (!iframe_object.src) iframe_object.src = url;
    smoothHeight('mini_iframe'+number,0,210,42,'o');
  }
  else smoothHeight('mini_iframe'+number,210,42,'mini_row'+number);
}
}

function open_new_window(url,w,h,scrollbars) {
winLeft = (screen.width-800)/2; 
winTop = (screen.height-720)/2; 
new_window = window.open(url,'my_window','scrollbars='+scrollbars+',toolbar=0,menubar=0,resizable=0,dependent=0,status=0,width='+w+',height='+h+',left='+winLeft+',top='+winTop);
}

function go_to_delete(text,url) { if (confirm(text)) { location = url; } }

function insertSmiley(smiley,field_id)
{
 var currentText = document.getElementById(field_id);
 var smileyWithPadding = " " + smiley + " ";
 currentText.value += smileyWithPadding;
 currentText.focus();
}



/*###############################################################################*/


function DOMCall(name) {
if (document.layers) return document.layers[name];
else if (document.all) return document.all[name];
else if (document.getElementById) return document.getElementById(name);
}
function showPic (whichpic) {
DOMCall('big_image').src = whichpic.href;
if (whichpic.title) { DOMCall('image_description').innerHTML = whichpic.title; DOMCall('image_description').className = ""; } 
else { DOMCall('image_description').className = "hidden"; }
return false;
}
function clickedImage (whichpic) {
imageUrl = whichpic.src;
}


var http_request = false;
function makeRequest(url,parameters,what) {
http_request = false;
if (window.XMLHttpRequest) // Mozilla, Safari,...
{ http_request = new XMLHttpRequest();
  if (http_request.overrideMimeType) { http_request.overrideMimeType('text/html'); }
}
else if (window.ActiveXObject) // IE
{ try { http_request = new ActiveXObject("Msxml2.XMLHTTP"); }
  catch (e) { try { http_request = new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {} }
}
if (!http_request) { alert('Cannot create XMLHTTP instance'); return false; }
if (what=='poll') http_request.onreadystatechange = alertContents_poll;
else if (what=='rate_form') http_request.onreadystatechange = alertContents_rate_form;
http_request.open('GET', url + parameters, true);
http_request.send(null);
}
function alertContents_poll() {
if (http_request.readyState==4)
{ if (http_request.status==200)
  { //alert(http_request.responseText);
    result = http_request.responseText;
    document.getElementById('poll_results').innerHTML = result;            
  }
  else { alert('There was a problem with the request.'); }
}
}
function alertContents_rate_form() {
if (http_request.readyState==4)
{ if (http_request.status==200)
  { //alert(http_request.responseText);
    result = http_request.responseText;
    document.getElementById('rate_results').innerHTML = result;            
  }
  else { alert('There was a problem with the request.'); }
}
}
   
function get(obj,php_script_url) {
var getstr = "?";
for (i=0; i<obj.childNodes.length; i++)
{ if (obj.childNodes[i].tagName == "INPUT")
  { if (obj.childNodes[i].type=="hidden") { getstr += obj.childNodes[i].name + "=" + obj.childNodes[i].value + "&"; }
    if (obj.childNodes[i].type=="text") { getstr += obj.childNodes[i].name + "=" + obj.childNodes[i].value + "&"; }
    if (obj.childNodes[i].type == "checkbox")
    { if (obj.childNodes[i].checked) { getstr += obj.childNodes[i].name + "=" + obj.childNodes[i].value + "&"; } 
      else { getstr += obj.childNodes[i].name + "=&"; }
    }
    if (obj.childNodes[i].type == "radio")
    { if (obj.childNodes[i].checked)
      { getstr += obj.childNodes[i].name + "=" + obj.childNodes[i].value + "&"; }
    }
    }   
    if (obj.childNodes[i].tagName == "SELECT")
    { var sel = obj.childNodes[i];
      getstr += sel.name + "=" + sel.options[sel.selectedIndex].value + "&";
   }
}
makeRequest(php_script_url,getstr,'rate_form');
}


-->