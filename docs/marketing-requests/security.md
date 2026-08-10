# Seguridad

El alcance se aplica en query y policy. Las mutaciones no usan GET. La búsqueda se restringe a columnas permitidas; el ordenamiento nunca acepta nombres arbitrarios. Las vistas escapan contenido de usuario y las descargas validan relación para evitar IDOR y path traversal.
