# Controles de seguridad

- Login y logout usan sesión Laravel; al autenticar se regenera el ID de sesión.
- Logout invalida la sesión y regenera el token CSRF.
- Todas las mutaciones Blade usan CSRF.
- El login y la solicitud de recuperación tienen rate limiting.
- Los errores de login y recuperación no revelan si un correo existe.
- Las contraseñas se validan con mínimo 10 caracteres, mayúsculas, minúsculas, números y símbolos al resetearlas.
- Cookies de sesión son HttpOnly, `SameSite=Lax` y pueden marcarse `Secure` en HTTPS.
- En producción deben configurarse secretos fuera del repositorio, HTTPS y `SESSION_SECURE_COOKIE=true`.

