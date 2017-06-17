#!/bin/bash

#This script scraps the page that lists the circonscriptions and sums up the numbers to give a summary.
#Big thanks to @ofa- 

curl -s https://collecte.mavoix.info/dons/accueil.html \
| awk -F '[<>]' '
{ gsub(" ", "") }
/Objectif:/ { total += $5 }
/progressbar/ { x+=1; next }
/span/ && x==1 { recu += $3 }
/span/ && x==2 { promis += $3; x=0 }
/Objectifatteint/ { atteint += 1 }
END {
	print "total=" percent(recu+promis) "%",
		"(" recu+promis "/" total ")",
		"reçu=" percent(recu) "%",
		"promis=" percent(promis) "%"
	print "circos bouclées=" atteint
}

function percent(x) { return int(x/total*100 + .5) }
'
