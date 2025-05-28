# Testar sem alterações
php artisan subscriptions:check-expired --dry-run

# Executar verificação real
php artisan subscriptions:check-expired

# Verificação com dias personalizados
php artisan subscriptions:check-expired --days=14,7,3,1


# Executar em loop infinito (verificação a cada hora)
php artisan scheduler:run-subscriptions --daemon

# Com intervalo personalizado (a cada 30 minutos)
php artisan scheduler:run-subscriptions --daemon --interval=1800


# Editar crontab
crontab -e

# Adicionar linha (executa todos os dias às 09:00)
0 9 * * * cd /caminho/para/seu/projeto && php artisan subscriptions:daily-check

# Ou executar a cada hora
0 * * * * cd /caminho/para/seu/projeto && php artisan subscriptions:check-expired