#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"

# THREADS: number of parallel executing  processes
# default: THREADS=0 (unlimited)
THREADS=0
if [ $# -gt 0 ]; then
  THREADS=$1
fi;

XARGS_OPTIONS="-P $THREADS -0 -I {}"
if [ $(uname -s) == "FreeBSD" ]; then
        XARGS_OPTIONS="-S 1024 -R 2 $XARGS_OPTIONS"
fi

echo "Updating all repos in parallel threads."

find . -type d -iname '.git' -print0 | xargs $XARGS_OPTIONS bash -c "cd \"{}\"/../ && OUT=\"\$(echo \"{}\" | cut -f 3 -d '/')\" && echo -e \"-----------------------------\nupdating repo [ \\\"\$(pwd)\\\" ]:\" >> /tmp/git_pull_\$OUT && LANG=en_GB LC_ALL=en_GB git pull &>> /tmp/git_pull_\$OUT && git submodule update --recursive --remote &>> /tmp/git_pull_\$OUT  && cat /tmp/git_pull_\$OUT && rm /tmp/git_pull_\$OUT "
