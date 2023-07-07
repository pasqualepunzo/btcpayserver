@echo off

:: Update Service Worker
call npm run update-sw

:start
yii serve 0.0.0.0 -p 80

