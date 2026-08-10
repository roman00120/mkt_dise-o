# Modelo de datos

`comments`, `comment_revisions`, `comment_mentions`, `comment_attachments`, `notifications` y `notification_preferences` son tablas separadas. `comments.commentable_type` no acepta tipos arbitrarios y las claves foráneas usan restricciones explícitas.
