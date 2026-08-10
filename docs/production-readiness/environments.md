# Entornos

Local usa SQLite, mail log y debug permitido. Testing usa base aislada, storage fake y mail/queue fake. Staging debe usar MySQL, `APP_DEBUG=false`, datos anonimizados, storage privado y mail sandbox. Producción usa MySQL, HTTPS, cache/queue persistentes, backups y sin seeders demo.
