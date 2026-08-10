# Backups

`app:backup --all --verify` genera backup de DB y archivos privados, con manifest y SHA-256. En MySQL requiere `mysqldump`; en SQLite copia el archivo local.
