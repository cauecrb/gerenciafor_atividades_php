#!/usr/bin/env bash
set -e
cd "$(dirname "$0")/.."
php artisan serve --host=localhost --port=8000 &
PHP_PID=$!
npm run dev &
VITE_PID=$!
trap 'kill $PHP_PID $VITE_PID' INT TERM EXIT
echo "Backend http://127.0.0.1:8000"
echo "Frontend http://localhost:5173"
wait
