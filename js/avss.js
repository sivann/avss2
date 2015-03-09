var pmode=0; var pwind;
var currimg=0;
var jplayer1;

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
	if (type != 'web' ) {
		disableWebPlayer();
	}
	else {
		enableWebPlayer();
	}
	enablePlayerTypeChange();
}

function disableWebPlayer() {
	jplayer1.jPlayer("destroy"); 
	$('#jp_container_1').hide();
}

function enableWebPlayer() {
	$('#jp_container_1').show();
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
		var data = {order:order, files: files};
		var data_s = JSON.stringify(data);

		$('#theform').attr('action','php/getFiles_m3u.php.m3u');
		$('#theform #var1').val(data_s);
		$('#theform').submit();
		return;
	}
	else {
		jplayer_play(files[0]);
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

function playTrack(caller) {
	var links=[];

	//create object
	var href=$(caller).attr('href');
	var $uri=$(caller).uri();
	var params = $uri.search(true)
	var obj={};
	obj.params=params;
	obj.href=href;
	links.push(obj);
	//console.log($uri.path());

	//play 
	var playertype=getPlayerType();
	playFiles(links,playertype,'keep');
}


function playAllSearchResults(caller) {
	var links=[];

	//create array of files
	$('#searchresults a.audio_lnk').each(function(i,e) {
		var href=$(e).attr('href');
		var $uri=$(e).uri();
		var params = $uri.search(true)
		var obj={};
		obj.params=params;
		obj.href=href;
		links.push(obj);
	});

	//play 
	var playertype=getPlayerType();
	playFiles(links,playertype,'keep');
}

function playAllFiles(caller) {
	var links=[];

	//create array of files
	$('#filebrowser a.audio_lnk').each(function(i,e) {
		var href=$(e).attr('href');
		var $uri=$(e).uri();
		var params = $uri.search(true)
		var obj={};
		obj.params=params;
		obj.href=href;
		links.push(obj);
	});

	//play 
	var playertype=getPlayerType();
	playFiles(links,playertype,'keep');
}


function jplayer_play(file) {
	//doc here:http://www.jplayer.org/latest/developer-guide/#jPlayer-setMedia
	//m4a: "http://mute.netmode.ece.ntua.gr/avss2/index.php/stream.m3u?path=%2Fmusic%2Fartists%2FT%2Fthe+doors%2Fan+american+prayer&action=getfile&file=the+doors+-+angels+and+sailors+-+08.mp3"

	var url,fstr;
	url= "http://mute.netmode.ece.ntua.gr/avss2/index.php?action=getfile&file="+file.params.file+"&path="+file.params.path

	if (file.params.file)
		fstr=file.params.file;

	jplayer1.jPlayer("clearMedia");
	jplayer1.jPlayer("destroy"); //otherwise it doesn't change track.
	//https://groups.google.com/forum/#!topic/jplayer/NSzSXe5nPbE also here to destroy playlist
	jplayer1.jPlayer({
		ready: function () {
			$(this).jPlayer("setMedia", {
				title: fstr,
				m4a: url
			}).jPlayer('play');
			//jplayer1.jPlayer("setMedia", { m4a: url });
		},
		solution: "html,flash",
		swfPath: "js/",
		errorAlerts: true,
		supplied: "m4a, mp3, oga",
		cssSelectorAncestor: "#jp_container_1",
		preload: "none",
		useStateClassSkin: true,
		autoBlur: false,
		smoothPlayBar: true,
		keyEnabled: true,
		remainingDuration: true,
		toggleDuration: true
	});

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

	jplayer1=$("#jquery_jplayer_1");


	//PLAY ALL search results
	$('#playall_searchresults').click(function(e) {
		playAllSearchResults(this);
	});


	//PLAY ALL files (in browsing)
	$('#playall_files').click(function(e) {
		playAllFiles(this);
	});

	//PLAY track
	$('.play_track').click(function(e) {
		e.preventDefault();
		playTrack(this);
	});



	//hide or show web player on page load
	setPlayerType(getPlayerType());

	$('#koko').click(function(e) {
	console.log('clearMedia');
		$("#jquery_jplayer_1").jPlayer("clearMedia");
		$("#jquery_jplayer_1").jPlayer("destroy");
		console.log(jplayer1)
		return;
	});
	$("#jplayer_inspector_1").jPlayerInspector({jPlayer:jplayer1});
  
}); //ready
