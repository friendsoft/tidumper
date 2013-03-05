#!/bin/bash

cd `dirname $0`/.. && mkdir -p "vendor-doc" && cd vendor-doc && \
rm -f GetId3 && \
ln -s ../vendor/phansys/getid3/GetId3/Resources GetId3 && \
rm -f Guzzle.md && \
ln -s ../vendor/guzzle/guzzle/README.md Guzzle.md && \
rm -f Pimple.rst && \
ln -s ../vendor/pimple/pimple/README.md Pimple.rst && \
exit
