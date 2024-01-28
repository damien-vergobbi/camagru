# Nginx
  - http://localhost:80

# PHP my admin
  - http://localhost:8080
  - host: db (.env[DB_HOST])
  - user: root (default)
  - password: root (.env[DB_PASS])

# Docker
  - docker system prune -af
  - docker volume prune -f
  - docker-compose up -d --build