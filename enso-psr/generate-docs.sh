#!/bin/bash

docker run --rm -v $(pwd):/src phpdoc/phpdoc -d /src/enso-psr  -i vendor -t ./docs