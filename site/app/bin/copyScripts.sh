#!/bin/sh -e

# MAKE DIRECTORY
mkdir -p ../www/scripts

# COPY SCRIPT FILES OVER TO WEB ROOT
# cp -Rf scripts/*.php ../www/scripts
cd scripts
find . -name '*.php' | cpio -updm ../../www/scripts