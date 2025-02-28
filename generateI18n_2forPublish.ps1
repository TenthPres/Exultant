$pharFile = "wp-cli.phar"
$compareDt = (Get-Date).AddDays(-1)

if (!(test-path $pharFile -newerThan $compareDt))
{
    Write-Output "Downloading wp-cli as it was not found or is more than a day old..."
    Invoke-WebRequest https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -OutFile $pharFile
}

Remove-Item "i18n/*.json"
Remove-Item "i18n/*.mo"
Remove-Item "i18n/*.php"

php .\wp-cli.phar i18n make-json i18n --no-purge
php .\wp-cli.phar i18n make-mo i18n
php .\wp-cli.phar i18n make-php i18n

Get-ChildItem -Path . -Filter 'i18n\Exultant-*.json' | Rename-Item -NewName { $_.Name -replace '^Exultant-', '' }
Get-ChildItem -Path . -Filter 'i18n\Exultant-*.mo' | Rename-Item -NewName { $_.Name -replace '^Exultant-', '' }
Get-ChildItem -Path . -Filter 'i18n\Exultant-*.php' | Rename-Item -NewName { $_.Name -replace '^Exultant-', '' }

xcopy /E /Y /I i18n build\i18n