#!/bin/sh

echo "Start RoadRunner Service"

./rr serve -d 2>&1 1>> ./runtime/logs/road-runner.log &

