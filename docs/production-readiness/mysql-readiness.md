# MySQL

Las conexiones usan `utf8mb4`, collation configurable y modo estricto. Las migraciones evitan enums nativos y dependen de tipos portables. La validación final de MySQL debe ejecutarse contra MySQL 8 aislado; no se utilizó una base productiva.
