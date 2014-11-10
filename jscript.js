var admin = false;

var base = location.protocol+'//'+location.hostname+location.pathname+'/extensions/pdfjs/web/viewer.html?file=http://'+location.hostname+location.pathname;
var root = '';
var win;
var f = false,first = true;


function ie8(){
  var elem = document.createElement('canvas');
  return !(elem.getContext && elem.getContext('2d'));
}
function uploadChange(){
	$('input[type="submit"]').click();
}
function h(){
	$('#search-body').height($(window).height()-150);
}

$(document).ready(function(){
	$.post('panel/config.xml',function(data){

		root = $(data).find('baseDir').text();
		
		
	},'xml');
	
	
	h();
	$(window).resize(function(){
		h();
	});
	$(document).on('click','.jaofiletree:gt(0) > li > input',function(e){
		f = true;
	}).on('click','.jaofiletree:gt(0) > li:not(input)',function(e){		
		if (f==false){
			$(this).removeClass('selected').find('a[data-file]:eq(0)').click();
			cb();
			return false;
		}
		f = false;
		
	});
	$(document).on('click','.page',function(){
		var f = $('.page.active').index();
		$('.page.active').removeClass('active')
		$(this).addClass('active');
		if ($('.page.active').index()!=f){
			page();
		}
	});
	
	
	$(document).on('click','#search-left,#search-right',function(){
		var f = $('.page.active').index();
		if ($(this).attr('id')=='search-left' && $('.page.active').index()!=0){
			var temp = $('.page:eq('+($('.page.active').index() - 1)+')');
			$('.active').removeClass('active');
			temp.addClass('active');
		}else if($(this).attr('id')=='search-right' && $('.page.active').index()!=($('.page').length-1)){
			var temp = $('.page:eq('+($('.page.active').index() + 1)+')');
			$('.active').removeClass('active');
			temp.addClass('active');
		}
		if ($('.page.active').index()!=f){
			page();
		}
	});
	
	$('#jao').jaofiletree({
		'showroot':'Docs'
		,'script':'extensions/filetree/connectors/jaoconnector.php'
		,'onclick':function(elem,type,file){
			if (file === undefined) return false;
			var ext = file.substring(file.lastIndexOf('.')+1);
			ext = ext.substring(0,(ext.length-1));
			if (type=='file' && ext=='pdf'){
				//fucker
				//var _file = base+'docs'+file.substring(0,(file.length-1));
				
				var _file = base+root+file.substring(0,(file.length-1));
				if (ie8()){
					var path = _file.split('file=');
					_file = path[1];
				}
				win = window.open(_file,'sap');
				win.focus();
			}
		}
		,'oncheck':function(elem,checked,type,file){
			
			$('.directory.selected').removeClass('selected');
			
			if (file=='/'){
				if (checked){
					$('input[type="checkbox"]:not(:checked)').each(function(){$(this).prop('checked',true)});
				}else{
					$('input[type="checkbox"]:checked').each(function(){$(this).prop('checked',false)});
				}
			}else{
				cb();
				
			}
			//return false;
		}
		,'usecheckboxes':'dirs'
	});
	
	
	
	
	
	$('#jao').on('afteropen',function(e){
		var jao = $('.jaofiletree:eq(1)');
		
		//$('#jao li.selected input').prop('checked',true);
		$(' > li.expanded input',jao).prop('checked',true);
		
		//$('.jaofiletree:eq(1) > li.collapsed').find('input').prop('checked',false);
		if ($('> li.expanded',jao).length==0 && $('> li input:checked',jao).length==$('> li',jao).length){
		
		}else{
			$('> li.collapsed',jao).find('input').prop('checked',false)
		}
		$('input[type="checkbox"][disabled="disabled"]').remove();
		cb();
		
	});
	$('#jao').on('afterclose',function(e){
		$('#jao li.selected input').prop('checked',false);
		$('input[type="checkbox"][disabled="disabled"]').remove();
	});
	$(document).on('click','#iframe-urltext',function(){
		selectText($(this).attr('id'))
	});
	
	$(document).on('keydown','#search-box',function(e){
		if (e.keyCode==13) $('#search-button').click();
	});
	
	$(document).on('click','#search-button',function(){
		search();
	});
	
	$(document).on('keydown',function(e){
		if (e.ctrlKey && e.shiftKey && e.keyCode==70){
			$('#search-box').focus();
		}else if(!$('#search-box').is(':focus') && e.shiftKey && e.keyCode==191){
			$('body').append('<div id="helper">This is the helper document</div>');
				$('#helper').css({'width':$(window).width(),'height':$(window).height()});
				$('#helper').on('click.helper',function(){
					$('#helper').off('click.helper');
					$('#helper').remove();
				});
		}
	});
	
	$(document).on('click','#search-body .sr-div',function(){
		$('#search-body .sr-div').removeClass('sbactive');
		$(this).addClass('sbactive');
		
		var _file = base + root +  $(this).attr('data-base') +'/' + $(this).attr('data-file')
		if (ie8()){
			var path = _file.split('file=');
			_file = path[1];
		}
		win = window.open(_file,'sap');
		win.focus();
		
		
		
	});
	
	$(document).on('click','#search-header .sr-div div',function(e){
		$('#search-header .sr-div div').removeClass('shactive');
		
		$(this).addClass('shactive');
		var type = ($(this).hasClass('numeric')?'numeric':'text');
		var dir  = ($(this).hasClass('asc')?'asc':'desc');
		
		sortUsingNestedText($('#search-body'),'div','div.'+searchClass(this,'sr-'),type,dir);
		$(this).toggleClass('asc desc')
	});
});

function page(){
	//var offset = $('.page.active').index();
	search();
	
}

function search(){
	var offset = ($('.page.active').length>0?$('.page.active').index():0);
	if ($('#search-box').val().length==0) return false;
	
	var obj = {};
		obj['term'] = $('#search-box').val();
		var checked = $('#jao').jaofiletree('getChecked');
		var totalCB = $('#jao').find('input[type="checkbox"][data-file] + a').length;
		if (checked.length!=totalCB){
			var c = []
			$.each(checked,function(dex,val){
				c.push(val['file']);
			});
			obj['folders'] = c;
		}
		obj['offset'] = offset;
	var json = JSON.stringify(obj);
	$('#search-time,#search-body').html('&nbsp;');
	
	$.post('php_scripts/search.php',{'json':json},function(data){
		
		
		if (!ie8()){
			console.log(data)
		}
		var l = $(data).find('file').length;
		var s = $(data).find('start').text();
		var e = $(data).find('end').text();
		var t = $(data).find('total').text();
		var p = parseInt($(data).find('pages').text());
		
		if (t==0 || p==1){
			$('#search-footer').addClass('h');
		}else{
			$('#search-footer').removeClass('h');
		}
		
		$('#search-pages').html('');
		for(var a = 0;a<p;a++){
			$('#search-pages').append('<div class="page hover glow" style="height:30px;width:30px;">'+(a+1)+'</div>');
		}
		
		var cur = parseInt($(data).find('curPage').text())
		$('.page:eq('+cur+')').addClass('active');
		$('#search-pages').html
		//$('#search-time').html(l + ' Result'+(l!=1?'s':'')+' (' + $(data).find('time').text()  +' seconds)');
		$('#search-time').html(s+' - '+ e + ' of ' + t +' Result'+(l!=1?'s':'')+' (' + $(data).find('time').text()  +' seconds)');

		$(data).find('file').each(function(){
			$('#search-body').append('<div class="sr-div hover" data-base="'+$(this).find('baseDir').text()+'" data-file="'+$(this).find('fileName').text()+'"></div>');
				$('.sr-div:last').append('<div class="sr-title">'+$(this).find('fileName').text()+'</div>');
				$('.sr-div:last').append('<div class="sr-file">'+$(this).find('baseDir').text()+'</div>');
				//$('.sr-div:last').append('<div class="sr-count">'+$(this).find('count').text()+'</div>');
				$('.sr-div:last').append('<div class="sr-count">&nbsp;</div>');
				$('.sr-div:last').append('<div class="sr-pages">'+$(this).find('pageCount').text()+'</div>');
				//var lm = new Date($(this).find('lastModified').text());
				var lm = Date.fromISO($(this).find('lastModified').text());
				//alert($(this).find('lastModified').text())
				//alert(lm)
				$('.sr-div:last').append('<div class="sr-lm">'+(lm.getFullYear() +'-'+ _2(lm.getMonth()+1) +'-'+ _2(lm.getDate()))+'</lm>');
		});
	},'xml');
}

function cb(){
	
	var tot = $('input[type="checkbox"][data-file]').length;
	var cur = $('input[type="checkbox"][data-file]:checked').length;
	var cb = $('.drive').find('input[type="checkbox"]:eq(0)');
	
	if (first==true){
		$('#jao input[type="checkbox"]:eq(0)').click();
		first = false;
	}
	if (cur==0){
		cb.prop('indeterminate',false);
		cb.prop('checked',false);
	}else if(cur==tot){
		cb.prop('indeterminate',false);
		cb.prop('checked',true);
	}else{
		cb.prop('indeterminate',true);
		cb.prop('checked',false);
	}
}

Date.fromISO= function(s){
	var day, tz,
	rx=/^(\d{4}\-\d\d\-\d\d([tT ][\d:\.]*)?)([zZ]|([+\-])(\d\d):(\d\d))?$/,
	p= rx.exec(s) || [];
	if(p[1]){
		day= p[1].split(/\D/);
		for(var i= 0, L= day.length; i<L; i++){
			day[i]= parseInt(day[i], 10) || 0;
		};
		day[1]-= 1;
		day= new Date(Date.UTC.apply(Date, day));
		if(!day.getDate()) return NaN;
		if(p[5]){
			tz= (parseInt(p[5], 10)*60);
			if(p[6]) tz+= parseInt(p[6], 10);
			if(p[4]== '+') tz*= -1;
			if(tz) day.setUTCMinutes(day.getUTCMinutes()+ tz);
		}
		return day;
	}
	return NaN;
}

function sortUsingNestedText(parent, childSelector, keySelector, type, dir) {
	type = typeof type !== 'undefined' ? type : 'text';
	d  = typeof dir  !== 'undefined' ? (dir.toLowerCase()=='desc'?-1:1)  : 1;
	d = -1*d;
    var items = parent.children(childSelector).sort(function(a, b) {
        var vA = $(keySelector, a).text();
		var vB = $(keySelector, b).text();
		
		if (type == 'numeric'){
			
			vA = parseFloat(vA);
			vB = parseFloat(vB);
		}
        return d*((vA < vB) ? -1 : (vA > vB) ? 1 : 0);		
    });
    parent.append(items);
}




function _2(t){
	return ('0'+t.toString()).slice(-2);
}

function selectText(containerid) {
	if (document.selection) {
		var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(containerid));
		range.select();
	} else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode(document.getElementById(containerid));
		window.getSelection().addRange(range);
	}
}



/*
setInterval(function(){
	$('#iframe-urltext').html(document.getElementById("viewer").contentWindow.location.href);
},1500);
*/
function c(t){
	$('#console').append('<div>'+t+'</div>');
}





function anim(that){$(that).animate({backgroundColor:'#ff0000'},1000,function(){$(that).css('background-color','')});}
function setCookie(c_name,value,exdays){var exdate=new Date();exdate.setDate(exdate.getDate() + exdays);var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());document.cookie=c_name + "=" + c_value;}
function getCookie(c_name){var c_value = document.cookie;var c_start = c_value.indexOf(" " + c_name + "=");if (c_start == -1){c_start = c_value.indexOf(c_name + "=");}if (c_start == -1){c_value = null;}else{c_start = c_value.indexOf("=", c_start) + 1;var c_end = c_value.indexOf(";", c_start);if (c_end == -1){c_end = c_value.length;}c_value = unescape(c_value.substring(c_start,c_end));}return c_value;}
function delCookie(name){document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';}
function searchClass(that,_class){var regex = new RegExp('\\b'+_class+'\\S+','g');var r = ($(that).attr('class').match(regex) || []).join(' ');return (r.length==0?false:r);}