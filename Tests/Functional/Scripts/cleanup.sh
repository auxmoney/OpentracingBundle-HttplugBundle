#!/bin/bash

# temp solution: otherwise fatals are thrown on symfony project creation step
git config --global user.email "you@example.com"
git config --global user.name "Your Name"


# clean any previously created testproject
rm -rf build/testproject
docker stop jaeger || true
docker rm jaeger || true
