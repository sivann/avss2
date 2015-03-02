var pmode=0; var pwind;
var currimg=0;

function playlist() {
   if (pmode==0 || pwind.closed==true) {
		pwind=window.open("","mp3_playlist","resizable,width=460,height=400,scrollbars=no,menubar=no,hotkeys=no");
		pmode++;
		//setTimeout('pwind.focus();',250);
	}
}

function rightString(str, sstr) {
   if (str.indexOf(sstr) == -1) {
      return "";
   } 
   else {
      return (str.substring(str.indexOf(sstr)+sstr.length, str.length));
   }
}

function addall() {
	var bigurl='',url;
	playlist();	
	for (i=0;i<100;i++) {
		idname="add" + i;
		k = document.getElementById(idname);
		if (!k) { continue; }
		url=rightString(k.getAttribute("href"),"path=");
		bigurl=bigurl+url+'@@';
	}
	//bigurl='\''+bigurl+'\'';
	document.addallform.ppath.value=bigurl;
	document.addallform.submit();
}

function previmage() {
    if (currimg>0)
        showimage(currimg-1);
}

function nextimage() {
    if (currimg<(Pictures.length-1))
        showimage(++currimg);
}

function showimage(imgno) {
	x2 = document.getElementById('photoimg');
	x2.src=Pictures[imgno];
	currimg=imgno
}

//pulldown search menu
function searchonGo(value) {
	searchon_val2text={
		'filename':'Filename',
		'directory':'Directory only',
		'style':'Style'
	}

	$('#searchon').val(value);
	$('#searchontxt').text(searchon_val2text[value]);
	$.cookie('searchon', value);
}

//ready
$(function() {

	//search pulldown
	$('#searchOnFilename').click(function(e) {
		e.preventDefault();
		searchonGo('filename');

	})
	$('#searchOnDirectory').click(function(e) {
		e.preventDefault();
		searchonGo('directory');
	})
	$('#searchOnStyle').click(function(e) {
		e.preventDefault();
		searchonGo('style');
	})

	//restore search preference
	if ($.cookie('searchon')) {
		searchonGo($.cookie('searchon'));
	}


	/*
	//SEARCH
	$('#searchfrm').submit(function(event) {
        jQuery.ajax({
			type: 'POST',
			cache: false,
			data: {searchstring: searchstring},
			url:    '?action=search&searchstring=michael', 
			success: function(data) {
				//history.pushState(null, null, '?action=search&string='+searchstring);
				$('#maincol').html(data);
				//https://developer.mozilla.org/en-US/docs/Web/Guide/API/DOM/Manipulating_the_browser_history
			}
        }); //ajax

	});
	*/


	//PLAY ALL search results
	$('#playall_searchresults').click(function(e) {
		var links=[];
		//create array 
		$('#searchresults a.audio_lnk').each(function(i,e) {
			var href=$(e).attr('href');
			var $uri=$(e).uri();
			var params = $uri.search(true)
			var obj={};
			obj.path=params.path;
			obj.file=params.file;

			links.push(obj);
		});

		//play 
		playFiles(links,'m3u','keep');
	});

	function playFiles(files,method,order) {
		//m3u: fill file list in hidden form <input> , and POST. 
		//Cannot do with AJAX because MIME handler is triggered by form's action.
		if (method == 'm3u') {
			var data= {order:order, files: files}
			var data_s;
			data_s=JSON.stringify(data),

			$('#theform').attr('action','php/getFiles_m3u.php.m3u');
			$('#theform #var1').val(data_s);
			$('#theform').submit();
			return;
		}

	}

  
});
