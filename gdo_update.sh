#!/bin/bash
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

SLEEP=0

if [ $# -gt 1 ]
  SLEEP=$1
fi

find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && 
pwd && LANG=en_GB LC_ALL=en_GB git pull && sleep $SLEEP && git submodule 
update --recursive --remote && sleep $SLEEP" \;
