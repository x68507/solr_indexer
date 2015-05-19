
var curDir = '';
var dCount = 0;
$(document).ready(function(){
	
	
	$('#container-folders').dropzone({ 
		url: 'upload.php',
		paramName:'uploadFile',
		forceFallback:false,
		clickable:false,
		fallback:function(){
			$('#upload,#submit').removeClass('h');
		},
		dragenter:function(e){
			dCount++;
			$('#container-folders').addClass('gb');
		},
		dragleave:function(e){
			dCount--;
			if (dCount === 0){
				$('#container-folders').removeClass('gb');
			}
		},
		drop:function(e){
			console.log('drop',e.target)
			$('#container-folders').removeClass('gb');
		},success:function(e){
			console.log(e)
			dir();
			$('#progress-container').addClass('h');
			$('#status').text('');
		},
		addedfile:function(file){
			$('#status').text('Uploading...');
			$('#progress-container').removeClass('h');
		},
		uploadprogress:function(file,progress,bytesSent){
			$('#progress-container').removeClass('h');
			$('#status').text('Uploading...');
			$('#progress').width(progress*2).addClass('partial').removeClass('full');
			if (progress==100){
				$('#progress').addClass('full').removeClass('partial');
				$('#status').text('Processing...');
				
			}
		}
	});

	dir();
	$(document).on('click','#folders a',function(e){
		setCookie('curDir',getCookie('curDir')+$(this).text());
		dir();
		return false;
		e.preventDefault();
	});
	$(document).on('mousedown',function(e){
		
		e.originalEvent.preventDefault();
	});
	
	$('#folders').on('dblclick','#back,.dir,.file',function(e){
		if ($(e.target).closest('.ft').attr('id')!='dirs') return false;

		
		console.log('path',$(e.target).closest('div.df')[0]);
		
		setCookie('curDir',getCookie('curDir')+$(e.target).closest('div.df').attr('data-dir'));
		dir();
		return false;
		e.preventDefault();
		//dir();
	}).on('mousedown','.dir,.file',{'that':this},function(e){
		
		$(window).on('mousemove.file',function(e){
			isDragging = true;
			$(window).off('mousemove.file');
			$(document).on('mouseenter.file','.dir,.file',function(e){
				var last = $('#folders').data('last');
				if ($(this).hasClass('act')){
					
					if ($(this).hasClass('act') && $(last).hasClass('act')){
						$(last).removeClass('act');
					}
				}
				$('#folders').data('init',($(this).hasClass('act') && $(last).hasClass('act')));
				if (!$(this).hasClass('act')){
					$(this).addClass('act');
				}
			}).on('mouseleave.file','.dir,.file',function(e){
				$('#folders').data('last',this);
				var init = $('#folders').data('init');
				if (init){
					$(this).removeClass('act');
				}
			});
		});
		
		
		if (!e.ctrlKey && !e.shiftKey){
			var l = $(this).hasClass('act');
			console.log('l',l)
			$('#folders .act').removeClass('act');
			if (!l){
				$(this).addClass('act');
			}
			
			
		}else if(e.ctrlKey){
			$(this).toggleClass('act');
		}else if(e.shiftKey){
			var _old = $($('#folders').data('click')).index('#folders .df');
			var _new = $(this).index('#folders .df');
			
			var start = (_old<_new?_old:_new);;
			var end = (_old<_new?_new:_old);
			
			$('#folders .df').removeClass('act');
			
			
			for(var dex = start;dex<=end;dex++){
				$('#folders .df:eq('+dex+')').addClass('act');
			}
		}
		if (!e.shiftKey) $('#folders').data('click',this);
		
		
	});
	$(document).on('mouseup',function(e) {
		$(document).off('mouseenter.file').off('mouseleave.file');
		$(window).off('mousemove.file');
	});
	
	$(document).on('contextmenu','#files',function(e){
		
		
			$('body').append('<div id="clicker"></div>');
			$(document).on('click.clicker','#clicker',function(){
				$('#clicker').remove();
				$('#contextmenu').addClass('h');
				$(document).off('click.clicker');
			});
			console.log('pageX',e.pageX,'pageY',e.pageY);
			
			$('#contextmenu').css({'left':e.pageX,'top':e.pageY}).removeClass('h');
			
			e.preventDefault();
		
	});
	
	$(document).on('click','#contextmenu div',function(e){
		var file = $('#curDir').text() + '\\' + $('.act .name').text();
		var url = 'download_file.php?f='+file;
		$('#clicker').click();
		window.open(url,'_blank');
		
	});
	
	/*END OF DOCUMENT READY*/
});

function debounce(fn, delay) {
	var timer = null;
	return function () {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
			fn.apply(context, args);
		}, delay);
	};
}

function secure_ip(){
	$.post('actions.php',{'action':'secure_ip','host':$('#secure_ip').val()},function(data){
		c(data);
	});
}

function server_start(){
	$.post('actions.php',{'action':'server_start'},function(data){
		c(data);
	});
}
function server_stop(){
	$.post('actions.php',{'action':'server_stop'},function(data){
		c(data);
	});
}
function refresh_index(){
	c('Refresh started...');
	//c('this sometimes takes a few minutes.  Please be patient and do not refresh',true);
	var t = microtime(true);
	
	$.post('actions.php',{'action':'refresh_index'},function(data){
		console.log('mio');
		//c(data);
		//var time = microtime(true)-t;
		c('<hr>'+data+'<hr>');
		//c('Completed in ' + Math.round(time*1000)/1000 + 's',true);
	});
	
}
function clear_console(){
	$('#console').text('');
}

function c(t,tab){
	tab = typeof tab !== 'undefined' ? ' class="tab"' : ' class="other" ';
	$('#console').append('<div'+tab+'>'+t+'</div>');
}
function enable_base(){
	$('#base').prop('disabled',false);
	$('#base').on('blur.base',function(){
		$.post('actions.php',{'action':'update_base','base':$('#base').val()},function(data){
			$('#base').prop('disabled',true);
			c(data);
		});
	});
}

function logout(){
	$.post('actions.php',{'action':'logout'},function(){
		delCookie('curDir');
		document.location.href = 'index.php';
	});
}

function delete_entries(){
	
	var win = window.open(window.location.origin + ':8983/solr/update?stream.body=%3Cdelete%3E%3Cquery%3E*:*%3C/query%3E%3C/delete%3E','del');
	setTimeout(function(){
		c('All entries deleted');
		win.close();
	},100);
}



function dir(){
	$.ajax({
		url:'listDir.php'
		,data:{'curDir':curDir}
		,dataType:'xml'
		,type:'POST'
		,success:function(data){
			$('#numFiles').text($(data).find('numFiles').text() + ' files');
			curDir = $(data).find('curDir').text();
			setCookie('curDir',curDir);
			$('#curDir').html(curDir);
		
			$('#dirs,#files').html('');
			
			$('#dirs').append('<div id="back" data-dir="\\.."><span class="i-dir"></span><span class="name hover">\\..</span></div>');
			$(data).find('dir').each(function(){
				$('#dirs').append('<div class="dir df" data-dir="\\'+$(this).text()+'"><div class="ext ext_dir">&nbsp;</div><div class="name hover">\\'+$(this).text()+'</div></div>');
			});
			$(data).find('file').each(function(){
				$('#files').append('<div class="file df"><div class="ext '+$(this).find('ext').text()+'">&nbsp;</div><div class="name">'+$(this).find('name').text()+'</span></div>');
			});
		}
		,error:function(e){
			console.log(e);
		}
	});
}

function rename(){
	
	if ($('#folders .act').length==0){
		alert('Please select either a single folder or file');
	}else if ($('#folders .act').length>1){
		alert('You can only rename one file/folder at a time');
	}else{
		var c = $('#folders .act');
		var n = c.closest('div').text();
		var oldName = n;
		if (c.closest('div').hasClass('file')){
			var e = n.split('.');
			n = e[0];
			ext = e[1];
			var type = 'file';
		}else if(c.closest('div').hasClass('dir')){
			n = n.slice(-(n.length-1));
			var type = 'dir';
		}else{
			return false;
		}
		
		
		var name = prompt('New name',n);
		//todo: need to loop through the SOLR index to update baseDir
		if (name!==null){
			if (!isValid(name)){
				alert('Please enter a valid file name');
				return false;
			}
			if (c.closest('div').hasClass('file')){
				name = name +'.'+ ext;
				var fName = name;
			}else if(c.closest('div').hasClass('dir')){
				var fName = '\\'+name;
			}
			name = '\\'+name;
			
			$('input:not(.disabled)').prop('disabled',true);
			$.ajax({
				url:'actions.php'
				,data:{'action':'rname','type':type,'curDir':curDir,'oldName':oldName,'newName':name}
				,dataType:'xml'
				,type:'POST'
				,success:function(data){
					console.log(data);
					$('input:not(.disabled)').prop('disabled',false);
					if ($(data).find('error').length>0){
						alert($(data).find('error').text());
						return false;
					}
					c.prop('checked',false);
					c.closest('div').find('.name').text(fName);
				}
				,error:function(data){
					$('input:not(.disabled)').prop('disabled',false);
					console.log(data);
				}
			});
		}
	}
}

function new_directory(){
	var name = prompt('New directory name:');
	if (name!==null){
		if (!isValid){
			alert('Please enter a valid file name');
			return false;
		}
		$.post('actions.php',{'action':'new_directory','name':name,'curDir':curDir},function(data){
			
			dir();
		},'xml');
	}
}

function del(){
	
	var l = $('#folders .act').length;
	if (l==0){
		alert('Please select a file/folder to delete');
		return false;
	}
	
	var ans = confirm('Are you sure you want to delete these files/folders?');
	if (ans){
		var obj = {};
		
		
		$('#folders .act').each(function(dex){
		
			obj[dex] = $.trim($(this).text());
		});
		
		var json = JSON.stringify(obj);
		
		$('#status').text('Deleting...');
		$('#progress-container').removeClass('h');
		$('#progress').width(200).removeClass('partial').addClass('full');
		
		console.log('before','json',json,'curDir',curDir);
		$.ajax({
			url:'actions.php'
			,data:{'action':'del','json':json,'curDir':curDir}
			,dataType:'xml'
			,type:'POST'
			,success:function(data){
				console.log('success',data);
				$('#status').text('');
				$('#progress-container').addClass('h');
				$('#progress').width(0);
				
				dir();
			}
			,error:function(data){
				console.log('error',data);
				c('It appears one or more directories were not empty.  Please refresh the page and try to delete all files inside directory and try again<hr>');
				
				$('#status').text('');
				$('#progress-container').addClass('h');
				$('#progress').width(0);
				
				dir();
			}
		});
	}
}

function uploadChange(){
	
	$('input[type="submit"]').click();
}

function schema(){
	//var ans = confirm('You only need to do this one time during initial setup.  Are you sure you want to proceed?');
	ans = true;
	
	if (ans){
		$.ajax({
			url:'actions.php'
			,data:{'action':'schema'}
			,dataType:'text'
			,type:'POST'
			,success:function(data){
				console.log('success');
				console.log(data);
			}
			,error:function(data){
				console.log('failure');
				console.log(data);
			}
		});
	}
}

var isValid=(function(){
	var rg1=/^[^\\\/:\*\?"<>\|]+$/; 
	var rg2=/^\./; 
	var rg3=/^(nul|prn|con|lpt[0-9]|com[0-9])(\.|$)/i;
	return function isValid(fname){
		return rg1.test(fname)&&!rg2.test(fname)&&!rg3.test(fname);
	}
})();

/*PHPjs*/
function dirname(path) {
	return path.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '');
}
function microtime(get_as_float) {
	var now = new Date().getTime() / 1000;
	var s = parseInt(now, 10);

	return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
}

/*Default Functions*/
function setCookie(c_name,value,exdays){var exdate=new Date();exdate.setDate(exdate.getDate() + exdays);var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());document.cookie=c_name + "=" + c_value;}
function getCookie(c_name){var c_value = document.cookie;var c_start = c_value.indexOf(" " + c_name + "=");if (c_start == -1){c_start = c_value.indexOf(c_name + "=");}if (c_start == -1){c_value = null;}else{c_start = c_value.indexOf("=", c_start) + 1;var c_end = c_value.indexOf(";", c_start);if (c_end == -1){c_end = c_value.length;}c_value = unescape(c_value.substring(c_start,c_end));}return c_value;}
function delCookie(name){document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';}