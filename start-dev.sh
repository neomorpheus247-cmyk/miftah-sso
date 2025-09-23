#!/bin/bash

# Set environment variables for Laravel
export APP_KEY="base64:+r1F3hqEyBH+FbV0bwVYuf21RYfS9c8mfC68G1Wg3ww="
export DB_CONNECTION=sqlite
export DB_DATABASE=/tmp/database.sqlite
export APP_ENV=local
export APP_DEBUG=true
export APP_URL=http://localhost:5000
export CACHE_STORE=file
export SESSION_DRIVER=file
export QUEUE_CONNECTION=database
export SESSION_SECURE_COOKIE=false
export SESSION_DOMAIN=null

# Ensure database exists
touch /tmp/database.sqlite

# Run Laravel server on port 8000 in background
php artisan serve --host=127.0.0.1 --port=8000 &

# Run Vite dev server on port 5173 in background  
npm run dev &

# Wait for servers to start
sleep 3

# Start nginx proxy to serve Laravel on port 5000
# Create nginx config
cat > /tmp/nginx.conf << EOF
events {
    worker_connections 1024;
}

http {
    upstream laravel {
        server 127.0.0.1:8000;
    }
    
    server {
        listen 5000;
        server_name _;
        
        location / {
            proxy_pass http://laravel;
            proxy_set_header Host \$host;
            proxy_set_header X-Real-IP \$remote_addr;
            proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto \$scheme;
        }
    }
}
EOF

# Start nginx
nginx -c /tmp/nginx.conf -g "daemon off;"