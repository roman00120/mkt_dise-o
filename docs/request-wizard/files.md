# Archivos

Se aceptan PDF, imágenes, documentos, presentaciones, MP4, MOV y ZIP; no ejecutables. La validación de aplicación permite hasta 25 MB por archivo, pero el PHP local validado tiene `upload_max_filesize=2M` y `post_max_size=8M`; por tanto, el límite efectivo local actual es 2 MB por archivo. No se modificó `php.ini`. Para habilitar 25 MB en un entorno futuro se deben elevar ambos valores y revisar el proxy web.

Los archivos se guardan en el disk privado `local`, bajo un directorio UUID. Nunca se expone el path interno ni se usa el nombre original como nombre almacenado. Sólo el propietario puede eliminar archivos de un borrador.
