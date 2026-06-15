#!/bin/bash
# Copy supervisor config if not already present (for local dev hot reload)
if [ ! -f /etc/supervisor/conf.d/supervisord.conf ]; then
	cp /var/www/html/buddyscript/docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
fi
# Start Supervisor in the foreground so it manages all processes
exec /usr/bin/supervisord -n
