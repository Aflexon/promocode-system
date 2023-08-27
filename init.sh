#!/bin/bash

sh ./workspace.sh composer install
docker compose up -d --build
sh ./workspace.sh php ./seeds/generate-promocodes.php
