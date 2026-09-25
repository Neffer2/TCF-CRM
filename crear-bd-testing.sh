#!/usr/bin/env bash
# Crea (o recrea) la base tfc_crm_testing como copia de la base local de pruebas tfc_crm, para php artisan test.
# Solo local: MySQL propio en el puerto 3325 (ver iniciar-local.sh). No toca ninguna otra base.
set -euo pipefail
MYSQL="${MYSQL:-/opt/homebrew/opt/mysql@9.6/bin/mysql}"
MYSQLDUMP="${MYSQLDUMP:-/opt/homebrew/opt/mysql@9.6/bin/mysqldump}"
CONN=(-h127.0.0.1 -P3325 -uroot)
ORIGEN="${1:-tfc_crm}"
DESTINO="${2:-tfc_crm_testing}"
case "$DESTINO" in *_testing) ;; *) echo "El destino debe terminar en _testing"; exit 1;; esac
# Se recrea desde cero: si no, las tablas que ya no existen en el origen (p. ej. roles_user, renombrada a roles) quedarían de la copia anterior.
"$MYSQL" "${CONN[@]}" -e "DROP DATABASE IF EXISTS \`$DESTINO\`; CREATE DATABASE \`$DESTINO\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
"$MYSQLDUMP" "${CONN[@]}" --single-transaction --routines --no-tablespaces --set-gtid-purged=OFF "$ORIGEN" | sed -E 's/DEFINER=`[^`]+`@`[^`]+`//g' | "$MYSQL" "${CONN[@]}" "$DESTINO"
echo "Lista: $DESTINO es copia de $ORIGEN. Ahora: php artisan test"
