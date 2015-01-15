var admin = false;


var base = location.protocol+'//'+location.hostname+location.pathname.replace(/[\\\/][^\\\/]*$/, '')+'/extensions/pdfjs/web/viewer.html?file=http://'+location.hostname+location.pathname.replace(/[\\\/][^\\\/]*$/, '');
var root = '';
var win;
var f = false;
var first = true;
var timeout;
var bSearch;


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
	
	
	
	if (get('url')){
		URLpopState();
	}
	
	$('#search-box').on('blur',function(){
		$('#search-bg').val('');
	});
	
	h();
	$(window).resize(function(){
		h();
	});
	/*
	$(document).on('click','.jaofiletree:gt(0) > li > input',function(e){
		f = true;
	}).on('click','.jaofiletree:gt(0) > li:not(input)',function(e){		
		if (f==false){
			console.log(this)
			console.log($(this).find('
			$(this).removeClass('selected')
			//$(this).removeClass('selected').find('a[data-file]:eq(0)').click();
				
			cb();
			return false;
		}
		f = false;
		
	});
	*/
	
	$(document).on('click','.jaofiletree input',function(e){
		$(this).parents().filter('input:eq(0)').prop('checked',false).prop('indeterminate',true);
		var checked = $(this).prop('checked');
		var that = this;
		$(this).parentsUntil('#jao').each(function(e){
			if ($(this).hasClass('directory') && this!=$(that).parent()[0]){
				
				$(this).find('input:eq(0)').prop('checked',checked).prop('indeterminate',!checked);
			}
		});
		
		/*
		$(this).parentsUntil('#jao','li.directory input').css('background','pink')
		console.log($(this).parentsUntil('#jao','li.directory').find('input').length);
		*/
	});
	$(document).on('click','.page',function(){
		var f = $('.page.active').index();
		$('.page.active').removeClass('active');
		$(this).addClass('active');
		if ($('.page.active').index()!=f){
			page();
		}
	});
	
	
	$(document).on('click','#search-left,#search-right',function(){
		var f = $('.page.active').index();
		if ($(this).attr('id')=='search-left' && $('.page.active').index()!==0){
			var temp = $('.page:eq('+($('.page.active').index() - 1)+')');
			$('.page.active').removeClass('active');
			temp.addClass('active');
		}else if($(this).attr('id')=='search-right' && $('.page.active').index()!=($('.page').length-1)){
			var temp = $('.page:eq('+($('.page.active').index() + 1)+')');
			$('.page.active').removeClass('active');
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
				var _file = base+root+encodeURIComponent(file.substring(0,(file.length-1)));
				
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
		,'afterload':function(e){
			if (get('url')){
				var json = JSON.parse(decodeURIComponent(get('url')));
				if (json['folders']){
				    
					
					$('.jaofiletree input[type="checkbox"]').prop('checked',false);
					$('.jaofiletree input[type="checkbox"]:eq(0)').prop('indeterminate',true);
					
					$.each(json['folders'],function(dex,val){
						var a = $('.jaofiletree a[data-file="'+val+'"]');
						$('#jao').jaofiletree('open',val);
						a.prev('input[type="checkbox"]').prop('checked',true);
						
					});
					
				}
				
			}
		}
		,'usecheckboxes':'dirs'
	});
	
	
	
	
	$('.radio-sub').on('click',function(){
		$(this).closest('.radio').find('.radio-sub.active').removeClass('active');
		$(this).addClass('active');
	});
	
	
	
	$('#jao').on('afteropen',function(e){
		
		var jao = $('.jaofiletree:eq(1)');
		
		$(' > li.selected input',jao).prop('checked',true);
		
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
		
		
	})
	
	$(document).on('click','.bbs',function(){
		search();
	});
	
	$(document).on('keydown',function(e){
		var t = $($(e.target)[0]);
		if(t.attr('id')=='search-box'){
			
			var v = $('#search-box').val();
			if (v.length>0){
				$('#search-bg').val('');
				$('#search-box').data('oVal',$('#search-box').val());
				var v = !$('#search-x').hasClass('h') && !$('#search-auto').hasClass('h');
				if (e.keyCode==39 && v){
					$('#search-box').val($('#search-bg').data('oVal'));
				}else if(e.keyCode==40 && v){
					if ($('.div-auto.active').length==0){
						$('.div-auto:eq(0)').addClass('active');
					}else if($('.div-auto.active').index()==$('.div-auto').length-1){
						$('.div-auto.active').removeClass('active');
						$('#search-box').val($('#search-box').data('oVal'));
						$('#search-bg').val($('#search-bg').data('oVal'));
					}else{
						$('.div-auto.active').removeClass('active').next('.div-auto').addClass('active');
					}
				}else if(e.keyCode==38 && v){
					if ($('.div-auto.active').length==0){
						$('.div-auto:eq('+($('.div-auto').length-1)+')').addClass('active');
					}else if($('.div-auto.active').index()==0){
						$('.div-auto.active').removeClass('active');
						$('#search-box').val($('#search-box').data('oVal'));
						$('#search-bg').val($('#search-bg').data('oVal'));
					}else{
						$('.div-auto.active').removeClass('active').prev('.div-auto').addClass('active');
					}
				}else if(e.keyCode==27){
					sa_remove();
				}else{
					if (timeout){
						clearTimeout(timeout);
						timeout = null;
					}
					timeout = setTimeout(autocomplete,10);
				}
				if (($('.div-auto.active').length>0 && (e.keyCode==38 || e.keyCode==40)) || e.keyCode==39){
					if (e.keyCode!=39){ $('#search-box').val($('.div-auto.active').text());}
					$('#search-box').data('auto',true);
					return false;
				}else if(e.keyCode!=13){
					$('#search-box').data('auto',false);
				}
			}
		}else{
			//up/down arrows for selecting 
			var dex = 0;
			if (e.keyCode==40){
				if ($('#search-body .sr-div .sr-row.active').length===0 || $('#search-body .sr-div .sr-row.active').closest('.sr-div').index()+1 == $('#search-body .sr-div').length){
					$('#search-body .sr-div .sr-row.active').removeClass('active');
					$('#search-body .sr-div:eq(0)').find('.sr-row').addClass('active');	
				}else{
					dex = $('#search-body .sr-div .sr-row.active').closest('.sr-div').index();
					$('#search-body .sr-div .sr-row.active').removeClass('active');
					$('#search-body .sr-div:eq('+(dex+1)+')').find('.sr-row').addClass('active');
				}
			}else if(e.keyCode==38){
				if ($('#search-body .sr-div .sr-row.active').length===0 || $('#search-body .sr-div .sr-row.active').closest('.sr-div').index()===0){
					$('#search-body .sr-div .sr-row.active').removeClass('active');
					$('#search-body .sr-div:eq('+($('#search-body .sr-div').length-1)+')').find('.sr-row').addClass('active');
				}else{
					dex = $('#search-body .sr-div .sr-row.active').closest('.sr-div').index();
					$('#search-body .sr-div .sr-row.active').removeClass('active');
					$('#search-body .sr-div:eq('+(dex-1)+')').find('.sr-row').addClass('active');
				}
			}else if(e.keyCode==13 && $('#search-body .sr-div .sr-row.active').length>0){
				var that = $('#search-body .sr-div .sr-row.active').closest('.sr-div');
				var _file = base + root +  that.attr('data-base') +'/' + encodeURIComponent(that.attr('data-file'));
				var background = e.ctrlKey;
				openFile(_file,background);
				$('#search-body .sr-div .sr-row.active').removeClass('active');
				return false;
				//fucker
			}else if (e.ctrlKey && e.shiftKey && e.keyCode==70){
				$('#search-box').focus();
			}else if(!$('#search-box').is(':focus') && e.shiftKey && e.keyCode==191){
				$('body').append('<div id="helper">This is the helper document</div>');
					$('#helper').css({'width':$(window).width(),'height':$(window).height()});
					$('#helper').on('click.helper',function(){
						$('#helper').off('click.helper');
						$('#helper').remove();
					});
			}else if ($(e.target).attr('id')=='copy-text' && e.ctrlKey && e.keyCode==67){
				setTimeout(function(){
					$('#copy').addClass('h');
				},10);
			}
		}
		
		//searches on enter
		
		if (t.attr('id')=='search-box' || t.hasClass('si')){
			if (e.keyCode==13){
				bSearch = true;
				clearTimeout(timeout);
				timeout = null;
				$('#search-button').click();
				sa_remove();
			}else{
				bSearch = false;
			}
		}
	}).on('keyup','#search-box',function(e){
		if ($('#search-box').val().length==0){
			sa_remove();
		}
	});
	
	$(document).on('click','#search-body .sr-div',function(){
		$('#search-body .sr-div').removeClass('sbactive');
		$(this).addClass('sbactive');
		
		var _file = base + root +  $(this).attr('data-base') +'/' + encodeURIComponent($(this).attr('data-file'));
		openFile(_file);
	});
	
	$(document).on('click','#search-header .sr-div div',function(e){
		$('#search-header .sr-div div').removeClass('shactive');
		
		$(this).addClass('shactive');
		var type = ($(this).hasClass('numeric')?'numeric':'text');
		var dir  = ($(this).hasClass('asc')?'asc':'desc');
		
		
		//sortUsingNestedText($('#search-body'),'div','div.'+searchClass(this,'sr-'),type,dir);
		$(this).toggleClass('asc desc')
		
		search(true);
	});
	
	$('#search-dd').on('click',function(){
		btog('advanced');
	});
	$('#search-xx').on('click',function(){
		sa_remove();
	});
	$(document).on('click','.div-auto',function(){
		$('#search-box').val($(this).text());
		$('#search-box').data('auto',true);
		$('#search-button').click();
		
	});
	
	//$(document).on('click','
	$('#s-date,#s-date2').datepicker({
		dateFormat:'yy-mm-dd'
		,onSelect:function(){
			if (!$('#s-date2').hasClass('h')){
				dp();
			}
		}
	});
	
	$(document).on('click','#s-db span,#s-pb span',function(){
		
		if ($(this).attr('data-op')=='bt'){
			$(this).closest('tr').find('.sai').addClass('half').removeClass('full');
			$(this).closest('tr').find('.sai-ic .second').removeClass('h');
			
		}else{
			
			$(this).closest('tr').find('.sai').removeClass('half').addClass('full');
			$(this).closest('tr').find('.sai-ic .second').addClass('h');
		}
		
		
	});
	
	$(document).on('click','.ctnt',function(e){
		
		//var url = encodeURI(base+root+$('#contextmenu').attr('data-file'));
		var url = base+root+encodeURIComponent($('#contextmenu').attr('data-file'));
		
		window.open(url,'_blank');
		if (e.ctrlKey){
			window.blur();
			window.focus();
		}
		rc();
	});
	
	$(document).on('click','.ctcl',function(e){
		$('#contextmenu').addClass('h');
		$('#copy').removeClass('h');
		//var url = encodeURI(base+root+$('#contextmenu').attr('data-file'));
		var url = base+root+encodeURIComponent($('#contextmenu').attr('data-file'));
		$('#copy-text').val(url).select();
	});
	
	$(document).on('click','.ctd',function(e){
		var file = root+encodeURIComponent($('#contextmenu').attr('data-file'));
		var url = 'php_scripts/download_file.php?f='+file;
		window.open(url);
		rc();
	});
	
	
	
	$(document).on('contextmenu',function(e){
		
		if ($(e.target).attr('data-type')=='file' || $(e.target).hasClass('ctx')){		
			if ($(e.target).hasClass('ctx')){
				var d = $(e.target).closest('div.sr-div');
				var file = d.attr('data-base')+'/'+d.attr('data-file');
				
			}else{
				
				var file = $(e.target).attr('data-file').substring(0,($(e.target).attr('data-file').length-1));
			}
			$('body').append('<div id="contextmenu" class="bs" data-file="'+file+'"></div>');
			
			$('#contextmenu').append('<div class="ctdiv ctnt">New Tab</div><div class="ctdiv ctcl">Copy Link</div>');
			$('#contextmenu').append('<hr>');
			$('#contextmenu').append('<div class="ctdiv ctd">Download</div>');
			$('#contextmenu').css({'left':e.pageX,'top':e.pageY});

			$('body').on('click.clicker',function(e){
				if (!$(e.target).hasClass('ctdiv')) rc();
			}).on('contextmenu',function(){
				if (!$(e.target).hasClass('ctdiv')) rc();
			})
			return false;
		}
		
		
		
	});
	
	$('#b-clear').on('click',function(){
		$('.si').val('');
		fncHistory('url','null',/url=([^&]*)/);
	});
	
	
	if (window.history.replaceState){
		window.onpopstate = function(event){
			$('#search-body').empty();
			if (get('url')){
				URLpopState();
			}else{
				$('.sai').val('');
				$('#search-time,#search-footer').html('');
			}
    	}
	}
	//END OF DOCUMENT READY
});

function openFile(_file,background){
	background = typeof background !== 'undefined' && background == true ? '_blank' : 'sap';
	
	if (ie8()){
		var path = _file.split('file=');
		_file = path[1];
	}
	
	
	
	if (background=='_blank'){
		window.open(_file,'_blank');
		window.blur();
		window.focus();
	}else{
		window.open(_file,'sap');
	}
}

function URLpopState(){
    var json = JSON.parse(decodeURIComponent(get('url')));
	
	if (json.term) $('#search-box').val(json.term);
	if (json.creator) $('#s-creator').val(json.creator);
	if (json.title) $('#s-title').val(json.title)
	
	if (json.date && json['date-op']){
	    $('#s-date').val(json.date);
        $('#s-db span.active').removeClass('active');
        $('#s-db span[data-op="'+json['date-op']+'"]').addClass('active');
        if (json.date2){
            $('#s-date,#s-date2').addClass('half');
            $('#s-date2').removeClass('h').val(json.date2);
        }
	}
	if (json.page && json['page-op']){
	    $('#s-page').val(json.page);
        $('#s-pb span.active').removeClass('active');
        $('#s-pb span[data-op="'+json['page-op']+'"]').addClass('active');
        if (json.page2){
            $('#s-page,#s-page2').addClass('half');
            $('#s-page2').removeClass('h').val(json.page2);
        }
	}
	
	
	if (json.term || json.title || json.creator || json.date || json.page){
	    sp('search',decodeURIComponent(get('url')));
	}
    
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function rc(){
	$('#contextmenu').remove();
	$('body').off('click.clicker');
	$('#copy').addClass('h');
	
}

function dp(){
	$('#s-date').datepicker('option','maxDate',($('#s-date2').val().length>0?$('#s-date2').datepicker('getDate'):null));
	$('#s-date2').datepicker('option','minDate',($('#s-date').val().length>0?$('#s-date').datepicker('getDate'):null));
}

function btog(type){
	type = typeof type !== 'undefined' ? type : 'advanced';
	if ($('#search-x').hasClass('h')){
		$('#search-x').removeClass('h');
		
		$('body').append('<div id="clicker"></div>');
		$('input:not(.sai),a').attr('tabIndex','-1');
		
		$(document).on('click.clicker','#clicker',function(){
			sa_remove();
		});
	}else if(type=='auto'){
		
	}else{
		sa_remove();
	}
	if (type=='advanced'){
		$('#search-advanced,#search-xx').removeClass('h');
		$('#search-auto').addClass('h');
		$('#search-box').focus();
	}else{
		$('#search-advanced,#search-xx').addClass('h');
		$('#search-auto').removeClass('h');
	}
	
}

function autocomplete(){
	var v = $('#search-box').val();
	
	$.post('php_scripts/search.php',{'action':'auto','json':v},function(data){
		if ($(data).find('main').length>0 && !bSearch){
			btog('auto');
			var sb = $.trim($('#search-box').val());
			var sbr = new RegExp(sb,'gi');
			var m = $(data).find('main').text().replace(sbr,sb);
			$('#search-bg').data('oVal',m).val(m);
			$('#search-auto').html('').append('<div class="div-auto hover boom">'+$(data).find('main').text().replace(sbr,sb)+'</div>');
			$(data).find('auto').each(function(){
				$('#search-auto').append('<div class="div-auto hover">'+$(this).text().replace(sbr,sb)+'</div>');
			});
		}
		
	},'xml');
}

function sa_remove(){

	$('input:not(.sai),a').removeAttr('tabIndex');
	$('#search-x').addClass('h');
	$('#clicker').remove();
	$(document).off('click.clicker');

}

function page(){
	//var offset = $('.page.active').index();
	search(true);
	
}

function search(o){
	if (typeof o !== 'undefined'){
		var offset = ($('.page.active').length>0?(parseInt($('.page.active').text())-1):0);
	}else{
		var offset = 0;
	}
	
	if ($('.si').filter(function(){
		return $(this).val().length>0;
	}).length==0){
		return false;
	}
	
	//if ($('#search-box').val().length==0) return false;
	
	var obj = {};
		
		if ($('#search-box').val().length>0){
		
			obj['term'] = $('#search-box').val();
		}
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
		
		//Advanced search
		if ($('#s-creator').val().length>0){
			obj['creator'] = $('#s-creator').val();
		}
		if ($('#s-title').val().length>0){
			obj['title'] = $('#s-title').val();
		}
		if ($('#s-page').val().length>0){
			obj['page'] = $('#s-page').val();
			obj['page-op'] = $('.r-pc.active').attr('data-op');
			if ($('#s-pb .active').attr('data-op')=='bt' && $('#s-page2').val().length==0){
				return false;
			}else{
				if ($('#s-page2').val().length>0) obj['page2'] = $('#s-page2').val();
			}
		}
		if ($('#s-date').val().length>0){
			obj['date'] = $('#s-date').val();
			obj['date-op'] = $('#s-db .active').attr('data-op');
			if ($('#s-db .active').attr('data-op')=='bt' && $('#s-date2').val().length==0){
				return false;
			}else{
				if ($('#s-date2').val().length>0) obj['date2'] = $('#s-date2').val();
			}
		}
		obj['auto'] = ($('#search-box').data('auto')?'true':'false');
		
		if ($('.shactive').length>0){
			obj['sort'] = $('.shactive').attr('data-field');
			obj['sortDir'] = ($('.shactive').hasClass('asc')?'asc':'desc');
		}
	
	var json = JSON.stringify(obj);
	
	
	$('#search-time,#search-body').html('&nbsp;');
	
	sa_remove();
	
	sp('search',json);
	//saves search in the URL
	fncHistory('url',json,/url=([^&]*)/);
}


function sp(action,json){
	$.post('php_scripts/search.php',{'action':action,'json':json},function(data){
		
		if (!ie8()){
			console.log(data)
		}
		var cur = parseInt($(data).find('curPage').text())
		
		var l = $(data).find('file').length;
		var s = $(data).find('start').text();
		var e = $(data).find('end').text();
		var t = $(data).find('total').text();
		var totalPage = parseInt($(data).find('pages').text());
		//var p = (totalPage>(cur+5)?cur+5:totalPage);
		
		if (totalPage>10){
			if (cur+5<10){
				var p = 10;
			}else{
				var p = (totalPage>(cur+5)?cur+5:totalPage);
			}
		}else{
			var p = totalPage;
		}
		
		
		if (totalPage>10){
			if (cur-5>totalPage-10){
				var b = totalPage - 10;
			}else{
				var b = (cur>5?cur-5:0);
			}
		}else{
			var b = 0;
		}
		
		
		
		if (t==0 || totalPage==1){
			$('#search-footer').addClass('h');
		}else{
			$('#search-footer').removeClass('h');
		}
		
		$('#search-pages').html('');
		
		
		
		for(var a = b;a<p;a++){
			$('#search-pages').append('<div class="page hover glow">'+(a+1)+'</div>');
		}
		
		$('.page').filter(function(){
			return $(this).text()==(cur+1);
		}).addClass('active');
		//$('.page:eq('+cur+')').addClass('active');
		$('#search-pages').html
		//$('#search-time').html(l + ' Result'+(l!=1?'s':'')+' (' + $(data).find('time').text()  +' seconds)');
		$('#search-time').html(s+' - '+ e + ' of ' + t +' Result'+(l!=1?'s':'')+' (' + $(data).find('time').text()  +' seconds)');

		$(data).find('file').each(function(){
			$('#search-body').append('<div class="sr-div hover" data-base="'+$(this).find('baseDir').text()+'" data-file="'+$(this).find('fileName').text()+'"></div>');
				$('.sr-div:last').append('<div class="sr-row">&nbsp;</div>');
				$('.sr-div:last').append('<div class="sr-title nus ctx" title="'+$(this).find('fileName').text()+'">'+$(this).find('fileName').text()+'</div>');
				$('.sr-div:last').append('<div class="sr-file nus ctx">'+$(this).find('baseDir').text()+'</div>');
				//$('.sr-div:last').append('<div class="sr-count nus">'+$(this).find('count').text()+'</div>');
				//$('.sr-div:last').append('<div class="sr-count nus ctx">&nbsp;</div>');
				$('.sr-div:last').append('<div class="sr-pages nus ctx">'+$(this).find('pageCount').text()+'</div>');
				//var lm = new Date($(this).find('lastModified').text());
				var lm = Date.fromISO($(this).find('lastModified').text());
				//alert($(this).find('lastModified').text())
				//alert(lm)
				$('.sr-div:last').append('<div class="sr-lm nus ctx">'+(lm.getFullYear() +'-'+ _2(lm.getMonth()+1) +'-'+ _2(lm.getDate()))+'</lm>');
		});
	},'xml');
}


function fncHistory(key,val,regex){
	var loc = (window.history.replaceState?location.search:location.hash.substring(1));
	val = encodeURIComponent(val);
	var str = (loc.search(regex)>-1?(val=='null'?loc.replace(new RegExp('(&|\\?)' + regex.source),''):loc.replace(regex,key+'='+val)):(val=='null'?'':(loc.length===0?'?':loc+'&')+key+'='+val) );
		
	if (window.history.replaceState){
		if (str.length===0) str = window.location.pathname.replace(/^.*[\\\/]/, '');
		//window.history.replaceState({},'',str);
		window.history.pushState({},'',str);
		
	}
	
}

function cb(){
	
	var tot = $('input[type="checkbox"][data-file]').length;
	var cur = $('input[type="checkbox"][data-file]:checked').length;
	var cb = $('.drive').find('input[type="checkbox"]:eq(0)');
	/*
	if (first==true){
		console.log(first);
		//$('#jao input[type="checkbox"]:eq(0)').click();
		first = false;
	}
	*/
	
	
	/*
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
	*/
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

function selectText(element){var doc = document,text = $(element)[0],range, selection;if (doc.body.createTextRange) {range = document.body.createTextRange();range.moveToElementText(text);range.select();} else if (window.getSelection) {selection = window.getSelection();range = document.createRange();range.selectNodeContents(text);selection.removeAllRanges();selection.addRange(range);}}
function rtrim(str, charlist) {
  //  discuss at: http://phpjs.org/functions/rtrim/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //    input by: Erkekjetter
  //    input by: rem
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Onno Marsman
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: rtrim('    Kevin van Zonneveld    ');
  //   returns 1: '    Kevin van Zonneveld'

  charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
    .replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
  var re = new RegExp('[' + charlist + ']+$', 'g');
  return (str + '')
    .replace(re, '');
}