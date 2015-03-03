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
function setSearchOn(value) {
	searchon_val2text={
		'filename':'Filename',
		'directory':'Directory only',
		'style':'Style'
	}

	$('#searchon').val(value);
	$('#searchontxt').text(searchon_val2text[value]);
	$.cookie('searchon', value);
}

//player type
function getPlayerType() {
	return $('input[name=playertype]:checked','#playertype').val();
}

function setPlayerType(type) {
	disablePlayerTypeChange();

	if (type && !type.length)
		type='m3u';

	$.cookie('playertype', type);
	enablePlayerTypeChange();
}

function loadPlayerType() {
	//restore player preference
	if (type=$.cookie('playertype')) {
		$("input[name=playertype][value=" + type + "]",'#playertype').parent('label').button('toggle');
	}
}





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
	else {
		console.log(method+' play method not implemented');
	}
}


function enablePlayerTypeChange() {
		$('#playertype input[name=playertype]').on('change', function () {
			setPlayerType(this.value);
		});
}

function disablePlayerTypeChange() {
		$('#playertype input[name=playertype]').off('change');
}

function playAllSearchResults(caller) {
	var links=[];

	//create array of files
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
	var playertype=getPlayerType();
	playFiles(links,playertype,'keep');
}

//ready
$(function() {

	//search pulldown
	$('#searchOnFilename').click(function(e) {
		e.preventDefault();
		setSearchOn('filename');

	})
	$('#searchOnDirectory').click(function(e) {
		e.preventDefault();
		setSearchOn('directory');
	})
	$('#searchOnStyle').click(function(e) {
		e.preventDefault();
		setSearchOn('style');
	})

	//restore search preference
	if ($.cookie('searchon')) {
		setSearchOn($.cookie('searchon'));
	}


	loadPlayerType(); //call this before enablePlayerTypeChange (or you'll have an event loop)
	enablePlayerTypeChange();


	//PLAY ALL search results
	$('#playall_searchresults').click(function(e) {
		playAllSearchResults(this);
	});

  
}); //ready
