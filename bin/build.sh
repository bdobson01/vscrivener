#!/bin/bash
pwd
#exit
tools=`dirname $0`
outputname=`basename $1`
cd $1
pwd
ls images
if [ -f file_order.txt ]; then
    mkdir -p ./output ./output/images
    unset GV_FILE_PATH
    unset SERVER_NAME
    pandoc -s `cat file_order.txt` --lua-filter ${tools}/graphviz.lua -f markdown -t html -o /tmp/${outputname}.html
    cat /tmp/${outputname}.html | sed s/\(Target\:\ .*\)//g | sed s/\TODO\:/\<b\>⭐TODO\:\<\\/b\>/g  > ./output/${outputname}.html
    cp images/* output/images
fi