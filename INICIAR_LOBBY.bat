@echo off
setlocal
cd /d "%~dp0"
title Lobby 1.0 - Servidor local
where php >nul 2>nul
if errorlevel 1 (
  echo PHP nao encontrado. Instale o PHP e adicione ao PATH.
  echo Consulte o arquivo LEIA-ME.md para instrucoes.
  pause
  exit /b 1
)
echo.
echo Iniciando Lobby em http://127.0.0.1:8085
 echo Deixe esta janela aberta enquanto usa o projeto.
echo Para encerrar, pressione Ctrl+C.
echo.
php artisan serve --host=127.0.0.1 --port=8085
if errorlevel 1 (
  echo.
  echo Nao foi possivel iniciar. Veja o erro acima.
  echo Se a porta estiver ocupada, consulte LEIA-ME.md.
  pause
)
