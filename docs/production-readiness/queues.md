# Queues

La cola database requiere worker persistente con `php artisan queue:work --tries=3 --timeout=90`. Jobs fallidos se revisan desde el sistema admin o con `queue:failed`.
