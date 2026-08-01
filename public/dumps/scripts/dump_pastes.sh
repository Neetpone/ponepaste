#!/bin/sh
WORKDIR=/home/debian/dumps
PUBDIR=/srv/http/ponepaste.org/public/dumps
TODAY=$(date +'%d-%m-%Y')
YDAY=$(date -d 'yesterday 01:00' +'%d-%m-%Y')

cd $WORKDIR

php -f dump_pastes.php $TODAY/
tar -cvzf $TODAY.tar.gz $TODAY/
if [ -f $PUBDIR/$YDAY.tar.gz ]; then
  xdelta3 -e -s $PUBDIR/$YDAY.tar.gz $TODAY.tar.gz ${YDAY}_${TODAY}.xdelta3
  mv ${YDAY}_${TODAY}.xdelta3 $PUBDIR/
fi

rm -f $PUBDIR/*.tar.gz $PUBDIR/*.sha1
sha1sum $TODAY.tar.gz > $PUBDIR/$TODAY.sha1
mv $TODAY.tar.gz $PUBDIR/$TODAY.tar.gz
ln -s $PUBDIR/$TODAY.tar.gz $PUBDIR/latest.tar.gz

rm -rf $TODAY/ $PUBDIR/$YDAY.tar.gz
