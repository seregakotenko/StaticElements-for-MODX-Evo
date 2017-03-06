var terminalType = "console";

function htmlspecialchars_decode(string, quote_style) {

  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined') {
    quote_style = 2;
  }
  string = string.toString()
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>');
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
    // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"');
  }
  // Put this in last place to avoid escape being double-decoded
  string = string.replace(/&amp;/g, '&');

  return string;
}

function isHidden(el) {
    var style = window.getComputedStyle(el);
    return (style.display === 'none')
}

function getObjectCss ($obj) {
	write("getObjectCss");
	var objCss = {};
	objCss.height = $obj.outerHeight();
	objCss.width = $obj.outerWidth();
	objCss.position = $obj.css("position") ? $obj.css("position") : "relative";
	objCss.left = $obj.position().left;
	objCss.top = $obj.position().top;
	if(objCss.position == "static") {
		objCss.position = "relative";
		objCss.left = "0px";
		objCss.top = "0px";
	}
	return(objCss);
}

function insertImageFileButton ($obj,name,type) {
	write("insertImageFileButton");
	$obj.addClass("init");
	var offset = $obj.offset();
	var $button = $("<div class='addinsertImageEditorContainerButton'></div>");
	offset.height = $obj.outerHeight();
	offset.width = $obj.outerWidth();
	offset.top = $obj.parent().offset().top < offset.top ? offset.top : $obj.parent().offset().top;
	var buttonPos = {
		top: offset.top,
		left: offset.left <= 0 ? 52 : (offset.width+offset.left > $(window).width() ? $(window).width() : offset.width+offset.left)
	}
	$button.css(buttonPos);
	if(type == "BrowseServer") {
		$button.attr("option-name",name).attr("onclick","BrowseServer('"+name+"'); return false;");
	} else {
		$button.attr("option-name",name).attr("onclick","BrowseFileServer('"+name+"'); return false;");
	}
	if($obj.is(":visible") || $obj.children().size(".addinsertTextEditorContainer") > 0) {
		if($obj.attr("href")) {
			$obj.addClass("aHrefButtonContainer");
			$button.removeAttr("style").addClass("aHrefButton");
			$obj.append($button);
			console.log($obj.html());
		} else {
			$("body").append($button);
		}
	}
}

function prepareOutForm () {
	write("prepareOutForm");
	var $form = $(".addinsertEditorFormContainer");
	if($form.size() == 0) {
		$form = $("<form class='addinsertEditorFormContainer' id='addinsertEditorFormContainer'></form>");
		$("body").append($form)
	}
	return $form;
}

function addinsertImageEditor (src,name) {
	write("addinsertImageEditor");
	$(document).ready(function() {
		var $obj = $("img[src='"+src+"']");
		var $form = prepareOutForm();
		if($obj.size() == 0) { return; }
		$obj.attr("id",name+"_image");
		$form.append("<input type='hidden' class='InputEventListner' name='"+name+"' id='"+name+"' />");
		$form.children("#"+name).val("/"+src);
		$obj.attr("option-name",name).attr("onclick","BrowseServer('"+name+"')");
		insertImageFileButton($obj,name,"BrowseServer");
	})
}

function addinsertFileEditor (src,name) {
	write("addinsertFileEditor");
	$(document).ready(function() {
		var $obj = $("*[src='"+src+"']").size() > 0 ? $("*[src='"+src+"']") : $("*[href='"+src+"']");
		var $parent = $obj.closest("video").size() > 0 ? $obj.closest("video") : $obj;
		var $form = prepareOutForm();
		if($parent.size() == 0) { return; }
		$parent.attr("id",name+"_file");
		$form.append("<input type='hidden' class='InputEventListner' name='"+name+"' id='"+name+"' />");
		$form.children("#"+name).val("/"+src);
		if($parent.attr("href")) {
			$parent.attr("option-name",name).attr("onclick","return false;");
		} else {
			$parent.attr("option-name",name).attr("onclick","BrowseFileServer('"+name+"')");
		}
		insertImageFileButton($parent,name,"BrowseFileServer");
	})
}

function addinsertTextEditor () {
	write("addinsertTextEditor");
	$(document).ready(function() {
		var $form = prepareOutForm();
		$(".globalplaceholdereditor").each(function () {
			var $obj = $(this);
			var name = $obj.data("name");
			var data = $obj.data("value");
			$obj.wrap("<div class='addinsertTextEditorContainer'></div>");
			$form.append("<input class='InputEventListner' type='hidden' name='"+name+"' id='"+name+"' />");
			$form.children("#"+name).val(data);
		})
		$(".globalplaceholdereditortext").each(function () {
			var $obj = $(this);
			var name = $obj.data("name");
			var data = $obj.data("value");
			$obj.wrap("<div class='addinsertTextEditorContainer'></div>");
			$form.append("<input class='InputEventListner' type='hidden' name='"+name+"' id='"+name+"' />");
			$form.children("#"+name).val(data);
		})
		$(".globalplaceholdereditortext").on("blur", function () {
			var name = $(this).data("name");
			var thishtml = $(this).html();
			thishtml = thishtml.replace("<p><br>", '<br>').replace("<p>", '<br>').replace("</p>", '');
			$(this).html(thishtml);
			$("#addinsertEditorFormContainer").children("#"+name).val(thishtml).trigger("change");
		})
	})
}

function addinsertTextEditorContainerChange (inst) {
	write("addinsertTextEditorContainerChange");
	var $parent = $(".globalplaceholdereditor#"+inst.id).closest(".addinsertTextEditorContainer");
	var $form = prepareOutForm();
	var data = inst.getContent().replace(/\r|\n/g, '').replace("&nbsp;", ' ');
	var data = htmlspecialchars_decode(data);
	$form.children("#"+inst.id).val(data).trigger("change");
}

function addInputEventListner () {
	write("addInputEventListner");
	$(document).ready(function() {
		$(document).find("input.InputEventListner").bind("change onchange", function () {
			$(window).trigger('resize');
			window.dispatchEvent(new Event('resize'));
			addInputEventListnerCallback($(this));
		})
		$("a.aHrefButtonContainer").bind("click touchend", function (e) {
			e.preventDefault();
			return false;
		});
	});
}

function addInputEventListnerCallback ($obj) {
	$(".gphfepreloader").removeClass("none").fadeIn(100);
	var formData = new FormData();
	formData.append('name', $obj.attr("name"));
	formData.append('value', $obj.val());
	$.ajax({
		type: "POST",
		url: "/?gphfe=savePH",
		contentType: false,
    	processData: false,
		data: formData,
		success: function(msg){
			if(msg != "error") {
				$(".gphfepreloader").fadeOut(300);
			} else {
				$(".gphfepreloader").fadeOut(300);
			}
		}
	});
}

addinsertTextEditor();

var lastImageCtrl;
var lastFileCtrl;

function OpenServerBrowser(url, width, height ) {
	var iLeft = (screen.width  - width) / 2 ;
	var iTop  = (screen.height - height) / 2 ;
	var sOptions = 'toolbar=no,status=no,resizable=yes,dependent=yes' ;
	sOptions += ',width=' + width ;
	sOptions += ',height=' + height ;
	sOptions += ',left=' + iLeft ;
	sOptions += ',top=' + iTop ;
	var oWindow = window.open( url, 'FCKBrowseWindow', sOptions ) ;
}
function BrowseServer(ctrl) {
	lastImageCtrl = ctrl;
	var w = screen.width * 0.5;
	var h = screen.height * 0.5;
	OpenServerBrowser('http://modx-custom.dev/manager/media/browser/mcpuk/browser.php?Type=images', w, h);
}
function BrowseFileServer(ctrl) {
	lastFileCtrl = ctrl;
	var w = screen.width * 0.5;
	var h = screen.height * 0.5;
	OpenServerBrowser('http://modx-custom.dev/manager/media/browser/mcpuk/browser.php?Type=files', w, h);
}
function SetUrlChange(el) {
	if ('createEvent' in document) {
		var evt = document.createEvent('HTMLEvents');
		evt.initEvent('change', false, true);
		el.dispatchEvent(evt);
	} else {
		el.fireEvent('onchange');
	}
}
function SetUrl(url, width, height, alt) {
	if(lastFileCtrl) {
		var c = document.getElementById(lastFileCtrl);
		var cc = document.getElementById(lastFileCtrl+"_file");
		if(c && c.value != url) {
			c.value = url;
			if(cc) {
				cc.src = "/"+url;
				cc.href = "/"+url;
			}
			SetUrlChange(c);
		}
		lastFileCtrl = '';
	} else if(lastImageCtrl) {
		var c = document.getElementById(lastImageCtrl);
		var cc = document.getElementById(lastImageCtrl+"_image");
		if(c && c.value != url) {
			c.value = url;
			if(cc) {
				cc.src = "/"+url;
			}
			SetUrlChange(c);
		}
		lastImageCtrl = '';
	} else {
		return;
	}
}

function write(text) {
	var date = new Date();
	var dateText = '[' + date.getFullYear() + '-' + (date.getMonth() + 1 > 9 ? date.getMonth() + 1 : '0' + (date.getMonth() + 1)) + '-' + (date.getDate() > 9 ? date.getDate() : '0' + date.getDate()) + ' ' + (date.getHours() > 9 ? date.getHours() : '0' + date.getHours()) + ':' + (date.getMinutes() > 9 ? date.getMinutes() : '0' + date.getMinutes()) + ':' + (date.getSeconds() > 9 ? date.getSeconds() : '0' + date.getSeconds()) + '.' + (date.getMilliseconds() > 9 ? (date.getMilliseconds() > 99 ? date.getMilliseconds() : '0' + date.getMilliseconds() ) : '00' + date.getMilliseconds()) + ']';
	if (terminalType == 'default' || terminalType == 'all') {
		var terminal = $(document).find('#terminal');
		$(terminal).prepend('<li>' + dateText + ' ' + text + '</li>');
	}
	if (terminalType == 'console' || terminalType == 'all') {
		console.log(dateText, text);
	}
}