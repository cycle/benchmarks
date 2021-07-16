@echo off
@setlocal
set EXEC_PATH=%~dp0
if "%PHP_COMMAND%" == "" set PHP_COMMAND=php
"%PHP_COMMAND%" "%EXEC_PATH%bench" %*
@endlocal
