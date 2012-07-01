<style>
body { margin-top: 80px!important }
#debug-container{position:absolute; z-index: 12000; display:block;background:#d3d3d3;color:#333;height:auto;overflow:hidden;margin:0;padding:0;width: 100%;top:0;left:0;border-radius:0 0 4px 4px;-moz-border-radius:0 0 4px 4px;-webkit-border-radius:0 0 4px 4px;box-shadow:0 3px 5px rgba(0,0,0,0.3); -moz-box-shadow:0 3px 5px rgba(0,0,0,0.3); -webkit-box-shadow:0 3px 5px rgba(0,0,0,0.3);}
#debug-content { display: none; padding: 0 20px 20px 20px; }
#debug-header{color:#fff;font-weight:normal;font-size:1.6em;text-shadow: 1px 1px 2px rgba(0,0,0,0.5); margin:0;padding:12px 0 8px 0;}
.debug-section{background:#e3e3e3;border-radius: 6px;-moz-border-radius:6px;-webkit-border-radius:6px;box-shadow :0 0 4px #666;-moz-box-shadow :0 0 4px #666;-webkit-box-shadow:0 0 4px #666;margin:0;padding:0}
#debug-content h2{color:#80B3DD;text-shadow:1px 1px 0 #fff;margin:0 0 0 0;padding:8px 0 8px 0;text-indent:20px;font-size:18px;position:relative;z-index:1;border-radius :6px 6px 0 0;-moz-border-radius :6px 6px 0 0;-webkit-border-radius:6px 6px 0 0;background:-webkit-gradient(linear,left top,left bottom,from(#fff),to(#e3e3e3));background:-moz-linear-gradient(top,#fff,#e3e3e3);filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#fff',endColorstr='#e3e3e3');-ms-filter:"progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#fff',endColorstr='#e3e3e3')"}
#debug-content p {margin:0;padding:0 8px 8px 25px;list-style:none}
#debug-content ul{margin:0;padding:0;list-style:none}
#debug-content li{margin:0 0 0 45px;padding:4px;list-style:none}
#debug-content b{font-weight:bold;color:#333}
#debug-content i{font-style:normal;color:#900}
#debug-close{cursor:pointer;display:block;padding:12px;margin:0;font-weight:bold;color:#fff;text-shadow:1px 1px 1px #333;text-align:center;border-radius:0 0 4px 4px;-moz-border-radius:0 0 4px 4px;-webkit-border-radius:0 0 4px 4px;background:#BDDAF9;background:-moz-linear-gradient(top,#BDDAF9 0%,#A7CFEF 5%,#80B3DD 97%,#2E7ACC 100%);background:-webkit-gradient(linear,left top,left bottom,color-stop(0%,#BDDAF9),color-stop(5%,#A7CFEF),color-stop(97%,#80B3DD),color-stop(100%,#2E7ACC));filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#BDDAF9',endColorstr='#2E7ACC',GradientType=0 )}
#debug-close.dropshadow{box-shadow :0 0 4px #666;-moz-box-shadow :0 0 4px #666;-webkit-box-shadow:0 0 4px #666;background:#BDDAF9;background:-moz-linear-gradient(top,#BDDAF9 0%,#A7CFEF 5%,#5F94CE 97%,#2E7ACC 100%);background:-webkit-gradient(linear,left top,left bottom,color-stop(0%,#BDDAF9),color-stop(5%,#A7CFEF),color-stop(97%,#5F94CE),color-stop(100%,#2E7ACC));filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#BDDAF9',endColorstr='#2E7ACC',GradientType=0 )}
#error-dim {
	position: absolute;
	z-index: 49999;
	top: 0;
	right: 0;
	left: 0;
	width: 100%;
	height: 100%;
	bottom: 0;
	background: rgba(75,75,75,0.5);
}

#error-container {
	font: 12px/18px Tahoma, Arial;
	letter-spacing: 0.1em;
	position: absolute;
	width: auto;
	height: auto;
	background: #f4f4f4;
	z-index: 50000;
	overflow: hidden;
	border: 2px solid #f4f4f4;
	border-radius: 6px; 
	-moz-border-radius: 6px; 
	-webkit-border-radius: 6px;
	box-shadow: 0 0 12px #666; 
	-moz-box-shadow: 0 0 12px #666; 
	-webkit-box-shadow: 0 0 12px #666; 
}

#error-container h1 {
	position: relative;
	background: #666;
	color: #fff;
	padding: 18px;
	margin: 0;
	border-radius: 4px 4px 0 0; 
	-moz-border-radius: 4px 4px 0 0; 
	-webkit-border-radius: 4px 4px 0 0;
}

#error-container #error-list {
	position: relative;
	z-index: 2;
	list-style: none;
	margin: 0;
	padding: 12px 0 12px 0;
	box-shadow: 0 0 6px #666; 
	-moz-box-shadow: 0 0 6px #666; 
	-webkit-box-shadow: 0 0 6px #666; 
}

#error-wrapper .error-error-item {
	padding: 14px 14px 14px 0;
	margin: 0 0 0 20px;
}

.error-error-item .error-error-header {
	font-weight: normal;
}

.error-error-item table { margin: 14px 0 0 20px; }
.error-error-item td {
	font: 12px/18px Tahoma, Arial;
	letter-spacing: 0.1em;
}

#error-wrapper .error-error-item .error-error-view {
	display: inline-block; *display: inline; *zoom: 1;
	padding: 2px 8px 2px 8px;
	margin-right: 12px;
	background: #666;
	color: #e19679;
	cursor: pointer;
}

#error-wrapper #error-close {
	position: relative;
	z-index: 0;
	background: #fff;
	padding: 0;
	margin: 0;
	text-align: right;
}

#error-wrapper #error-close a {
	position: relative;
	z-index: 0;
	display: inline-block; *display: inline; *zoom: 1;
	font-weight: bold;
	text-decoration: none;
	padding: 2px 8px 2px 8px;
	background: #666;
	color: #e19679;
	cursor: pointer;
	padding: 10px;
	border-radius: 0 0 4px 4px; 
	-moz-border-radius: 0 0 4px 4px; 
	-webkit-border-radius: 0 0 4px 4px;
}
	
</style>
<div id="debug-container"><div id="debug-content"><h1 id="debug-header">Here's what we have found</h1>%%_DEBUG_OUTPUT_%%</div><div id="debug-close" class="dropshadow">errors were found</div></div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">$(document).ready(function() { var _content = $('#debug-content'); var _close   = $('#debug-close'); var _text    = _close.text(); _close.mousedown(function() { _close.removeClass('dropshadow'); }); _close.mouseup(function() { _close.addClass('dropshadow'); }); _close.click(function() { if (_text == "close") { _content.slideUp(); _text    = 'open'; _close.text(_text); } else { _content.slideDown('fast'); _text    = 'close'; _close.text(_text); } }); }); </script>