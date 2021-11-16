#!/bin/bash
# script to list all TODOs of GDO.

find . -type f -name '*.php' -print0 | while IFS= read -d $'\0' -r php_file; do
	msg=$(grep --ignore-case --only-matching -h -E -e '@TODO (.*)' --exclude-dir .git --exclude='*GDO_TODO*' --exclude '*gdo_todo*' "$php_file" | sed 's/@TODO\s*/- [ ] /g' | sort | uniq)
	[ -n "$msg" ] && echo -e "# ${php_file:2}:\\n$msg\\n\\n"
done
