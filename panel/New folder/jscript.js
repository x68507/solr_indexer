
var curDir = '';
			
$(document).ready(function(){
	
	
	$('#uploadForm').dropzone({ 
		url: 'upload.php',
		paramName:'uploadFile',
		forceFallback:true,
		fallback:function(){
			$('#upload,#submit').removeClass('h');
		},
		dragenter:function(){
			$('#folders').addClass('gb');
		},
		dragleave:function(){
			$('#folders').removeClass('gb');
		}
	});

	dir();
	$(document).on('click','#folders a',function(e){
		setCookie('curDir',getCookie('curDir')+$(this).text());
		dir();
		return false;
		e.preventDefault();
	});
	if (getCookie('scan')==='true'){
		$('#scan').prop('checked',true);
	}
	$(document).on('click','#scan',function(){
		setCookie('scan',$('#scan').is(':checked'));
	});
});

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
    console.log('curDir before send',curDir)
	$.ajax({
		url:'listDir.php'
		,data:{'curDir':curDir}
		,dataType:'xml'
		,type:'POST'
		,success:function(data){
			curDir = $(data).find('curDir').text();
			setCookie('curDir',curDir);
			$('#curDir').html(curDir);
		
			$('#folders').html('');
			
			$('#folders').html('<div class="dir"><input type="checkbox" disabled="disabled"><a href="#">\\..</a></div>');
			$(data).find('dir').each(function(){
				$('#folders').append('<div class="dir"><input type="checkbox"><a href="#"><span class="name">\\'+$(this).text()+'</span></a></div>');
			})
			$(data).find('file').each(function(){
				$('#folders').append('<div class="file"><input type="checkbox"><span class="name">'+$(this).text()+'</span></div>');
			})
		}
		,error:function(e){
			console.log(e);
		}
	});
}

function rename(){
	if ($('#folders input[type="checkbox"]:checked').length==0){
		alert('Please select either a single folder or file by clicking the checkbox');
	}else if ($('#folders input[type="checkbox"]:checked').length>1){
		alert('You can only rename one file/folder at a time');
	}else{
		var c = $('#folders input[type="checkbox"]:checked');
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
	//var curDir = $('#curDir').text();
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
	var l = $('#folders input[type="checkbox"]:checked').length;
	if (l==0){
		alert('Please select a file/folder to delete');
		return false;
	}
	
	var ans = confirm('Are you sure you want to delete these files/folders?');
	if (ans){
		var obj = {};
		
		$('#folders input[type="checkbox"]:checked').each(function(dex){
			obj[dex] = $(this).closest('div').text();
		});
		
		var json = JSON.stringify(obj);
		$.ajax({
			url:'actions.php'
			,data:{'action':'del','json':json,'curDir':curDir}
			,dataType:'xml'
			,type:'POST'
			,success:function(data){
				console.log(data);
				
				dir();
			}
			,error:function(data){
				console.log(data);
				c('It appears one or more directories were not empty.  Please delete all files inside directory and try again<hr>');
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