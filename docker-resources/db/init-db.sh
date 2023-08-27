#!/bin/bash
set -e

dbs=("$DB_NAME" "${DB_NAME}_test")

for db in "${dbs[@]}";
do
  echo "Creating $db";
  mysql -u root -p"$MYSQL_ROOT_PASSWORD" --execute \
  "CREATE DATABASE IF NOT EXISTS ${db};
  GRANT ALL PRIVILEGES ON ${db}.* TO '$MYSQL_USER'@'%';
  USE ${db};
  CREATE TABLE IF NOT EXISTS \`promocodes\` (
    \`code\` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    \`user_id\` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
    \`received_at\` timestamp NULL DEFAULT NULL,
    \`ip\` varchar(45) DEFAULT NULL,
    PRIMARY KEY (\`code\`),
    UNIQUE KEY \`idx_user\` (\`user_id\`),
    KEY \`idx_ip\` (\`ip\`),
    KEY \`idx_received_at\` (\`received_at\`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;"
done


echo "** Finished creating default DB and users"
