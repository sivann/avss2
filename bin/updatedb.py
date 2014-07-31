#!/usr/bin/python

#>>> sline
#['music', 'artists', 'E', 'elliot smith', 'xo', 'elliott smith - a question mark - 12.mp3']
POS_ARTIST=3 #counting from 0
dirprefix='/MMROOT/audio'

import sqlite3
import sys
import codecs
import string
import os
import glob
import time

#reload(sys)
#sys.setdefaultencoding('utf-8')

flistfn=sys.argv[1]


s_conn = sqlite3.connect('../data/avss.db')
scur = s_conn.cursor()
	

def parseLine(line):
	line=os.path.normpath(line.strip())
	sline=line.split('/')
	gentype=sline[0]
	ftype=sline[1]

	if gentype == 'music' and ftype == 'artists':
		processArtist(line,sline)
	else:
		processGeneric(line,sline)

#process a generic track (no artist,album etc)
def processGeneric(line,sline):
	try:
		mtime=int(os.path.getmtime(dirprefix+'/'+line))
	except os.error as e:
		print 'getmtime: line (%s) :%s' % (line,e)
		raise

	try:
		directory='/'.join(sline[0:-1])
		scur.execute('INSERT INTO track (mtime,filename,directory) VALUES  (?,?,?)',(mtime,sline[-1],directory))
	except sqlite3.Error as e:
		print 'ERROR: processGeneric: SQLite: %s, line: [%s]' % (e,line)



def processArtist(line,sline):
	#add artist if needed
	checkAddArtist(line,sline)

	#if it's track of an album
	if isAlbumTrack(line,sline): 
		checkAddAlbum(line,sline)
		addAlbumTrack(line,sline)
	#if it's track of an artist, outside the album
	else:
		addArtistTrack(line,sline)


#add artist if not exist
def checkAddArtist(line,sline):
	artistdir='/'.join(sline[0:POS_ARTIST+1])
	try:
		#scur.execute("SELECT count(*) from artist where directory=:artistdir",{'artistdir':artistdir.decode('utf-8')})
		scur.execute("SELECT count(*) from artist where directory=?",[artistdir])
	except sqlite3.Error as e:
		print artistdir
		print 'ERROR: checkAddArtist: SQLite: %s, line: [%s]' % (e,line)
		raise
	result=scur.fetchone()
	nrows=result[0]

	#add artist
	if nrows==0:
		name=u''
		discography=u''
		biography=u''
		info=u''
		image_filenames=''
		pp = os.getcwd() #first enter dir to avoid mixing non-unicode with unicode filenames (directory , .bio files)

		#info
		try:
			os.chdir(dirprefix+'/'+artistdir)
			#files=glob.glob(dirprefix+'/'+artistdir+"/.*.info")
			files=glob.glob(".*.info")
		except:
			print dirprefix+'/'+artistdir+"/.*.info"
			raise

		if files:
			infofn=files[0]
			#with codecs.open(infofn,'r',encoding='utf-8') as x: info = x.read()
			with codecs.open(infofn) as x: info = x.read()
			name=[elem for elem in info.splitlines() if 'Artist' in elem][0].split(':')[1]
			info=info.decode('iso-8859-1').encode('utf-8')
			info=unicode(info,'utf-8')

			name=name.decode('iso-8859-1').encode('utf-8')
			name=unicode(name,'utf-8')

		#bio
		files=glob.glob(".*.bio")
		if files:
			biofn=files[0]
			with open(biofn) as x: biography = x.read()
			biography=biography.decode('iso-8859-1').encode('utf-8')
			biography=unicode(biography,'utf-8')

		#discography
		files=glob.glob(".*.disc")
		if files:
			discfn=files[0]
			with open(discfn) as x: discography = x.read()
			discography=discography.decode('iso-8859-1').encode('utf-8')
			discography=unicode(discography,'utf-8')

		#photos
		files=glob.glob("*photo.jpg") + glob.glob("*photo.png")
		files=[os.path.basename(x) for x in files]
		image_filenames=','.join(files)

		try:
			scur.execute('INSERT INTO artist (directory, info, biography, discography, name, image_filenames) VALUES (?,?,?,?,?,?)', (artistdir,info,biography,discography,name,image_filenames))
		except sqlite3.Error as e:
			print artistdir
			print 'ERROR: checkAddArtist: SQLite: %s, line: [%s]' % (e,line)
			raise
		os.chdir(pp)
		s_conn.commit()

def checkAddAlbum(line,sline):
	artistdir='/'.join(sline[0:POS_ARTIST+1])
	albumdir='/'.join(sline[0:-1])
	scur.execute('SELECT count(*) from album where directory=?',[albumdir])
	result=scur.fetchone()
	nrows=result[0]

	#add album
	if nrows==0:
		#first make sure artist exists
		checkAddArtist(line,sline)

		scur.execute('SELECT id from artist where directory=?',[artistdir])
		result=scur.fetchone()
		artistid=result[0]

		#photos
		files=glob.glob(dirprefix+'/'+artistdir+"/*photo.jpg") + glob.glob(dirprefix+'/'+artistdir+"/*photo.png")
		files=[os.path.basename(x) for x in files]
		image_filenames=','.join(files)

		scur.execute('INSERT INTO album (directory, artistid, image_filenames) VALUES (?,?,?)',
				(albumdir,artistid,image_filenames))
		s_conn.commit()

		pass


#Add track which belongs to an album
def addAlbumTrack(line,sline):
	mtime=int(os.path.getmtime(dirprefix+'/'+line))

	albumdir= '/'.join(sline[0:-1])
	#albumdir='/'.join(sline[0:POS_ARTIST+2])

	scur.execute('SELECT id from album where directory=?',[albumdir])
	result=scur.fetchone()
	albumid=result[0]

	artistdir='/'.join(sline[0:POS_ARTIST+1])
	scur.execute('SELECT id from artist where directory=?',[artistdir])
	result=scur.fetchone()
	artistid=result[0]

	try:
		scur.execute('INSERT INTO track (mtime,filename,directory,artistid,albumid) VALUES  (?,?,?,?,?)',(mtime,sline[-1],albumdir,albumid,artistid))
	except sqlite3.IntegrityError as e:
		print 'WARNING: addAlbumTrack: SQLite: %s, line: [%s]' % (e,line)
	except sqlite3.Error as e:
		print 'ERROR: addAlbumTrack: SQLite: %s, line: [%s]' % (e,line)
		raise




def addArtistTrack(line,sline):
	#p=(dirprefix+'/'+line).decode('utf-8')
	p=(dirprefix+'/'+line)
	mtime=int(os.path.getmtime(p))
	trackdir= '/'.join(sline[0:-1])

	artistdir='/'.join(sline[0:POS_ARTIST+1])
	scur.execute('SELECT id from artist where directory=?',[artistdir])
	result=scur.fetchone()
	artistid=result[0]

	try:
		scur.execute('INSERT INTO track (mtime,filename,directory,artistid) VALUES  (?,?,?,?)',(mtime,sline[-1],trackdir,artistid))
	except sqlite3.IntegrityError as e:
		print 'WARNING: addArtistTrack: SQLite: %s, line: [%s]' % (e,line)
	except sqlite3.Error as e:
		print 'ERROR: addArtistTrack: SQLite: %s, line: [%s]' % (e,line)
		raise



def isAlbumTrack(line,sline):
	if len(sline)<(POS_ARTIST + 3):
		return True
	return False


f = open(flistfn)

for line in f:
	#line=line.decode('iso-8859-1').encode('utf-8')
	line=unicode(line,'utf-8')
	parseLine(line)


s_conn.commit()
s_conn.close()
f.close()
