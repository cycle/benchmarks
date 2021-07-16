#!/bin/bash

cd benchmarks/v1
[ ! -d "vendor" ] && composer install || composer du &> /dev/null

cd ../../benchmarks/v2
[ ! -d "vendor" ] && composer install || composer du &> /dev/null

cd ../../

declare -a PROJECTS=(
  "benchmarks/v1"
  "benchmarks/v2"
)

CONFIG=$PWD/phpbench.json
BENCHMARKS_PATHS=tests/Benchmarks
REPORT=aggregate

for PROJECT in "${PROJECTS[@]}"
do
  ./vendor/bin/phpbench run --working-dir=$PROJECT --bootstrap=bootstrap.php --report=$REPORT --config=$CONFIG $BENCHMARKS_PATHS
done
