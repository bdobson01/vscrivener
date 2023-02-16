#!/bin/sh
tools=`dirname $0`
rawnum=1
num=1
section=URL
cd $1
while IFS= read -r line; do
    if [ ! -z "$line" ]; then
        echo $line | grep ^:raw: 2>&1 >/dev/null
        if [ $? -eq 0 ]; then
            mla=`echo $line | awk -F":raw:" '{ print $2; }'`
            echo \[\^RAW${rawnum}\]: $mla
            rawnum=$((rawnum + 1))
        else
            echo $line | grep ^:section: 2>&1 >/dev/null
            if [ $? -eq 0 ]; then
                # echo "Section read from file: $line"
                section=`echo $line | awk '{ print $2 }'`
                #echo $section
                num=1
            else
                mla=`php ${tools}/citation.php $line`
                echo \[\^${section}${num}\]: $mla
                num=$((num + 1))
            fi
        fi
    fi
done < $2 > /tmp/citations.$$
cd ..
footnotes=`grep footnotes.md file_order.txt`
while IFS= read -r line; do
    echo $line | grep CITATIONS_AUTO_ADDED_BELOW 2>&1 >/dev/null
    if [ $? -ne 0 ]; then
        echo $line
    else
        echo $line
        break
    fi
done < $footnotes > /tmp/footnotes.$$
cat /tmp/footnotes.$$ > $footnotes
echo >> $footnotes
cat /tmp/citations.$$ >> $footnotes
rm /tmp/footnotes.$$ /tmp/citations.$$
