#!/bin/sh -e
lessc=/usr/bin/lessc
lessDir=less/
output=../www/css

mkdir -p $output
nice -n 9 $lessc -x $lessDir/build.less > $output/styles.min.css