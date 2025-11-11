#!/bin/bash

compose install --no-dev --no-progress -a

service nginx reload