# Recuperación de contraseña

El flujo es:

1. El usuario solicita recuperación en `/forgot-password`.
2. Laravel envía el enlace mediante el mailer configurado.
3. El usuario abre `/reset-password/{token}`.
4. La nueva contraseña debe cumplir la política de complejidad.
5. Tras un cambio válido, vuelve a `/login` con un mensaje de confirmación.

La respuesta al solicitar el enlace es deliberadamente genérica, incluso cuando el correo no está registrado.

