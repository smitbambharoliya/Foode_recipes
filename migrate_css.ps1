$templateDir = "d:\xamppa\Foode_recipes\templates"
$publicCssDir = "d:\xamppa\Foode_recipes\public\css"

if (!(Test-Path $publicCssDir)) {
    New-Item -ItemType Directory -Force -Path $publicCssDir
}

$files = Get-ChildItem -Path $templateDir -Recurse -Filter *.html.twig

foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw
    
    if ($content -match '(?s)<style>(.*?)</style>') {
        $cssContent = $matches[1].Trim()
        
        if (![string]::IsNullOrWhiteSpace($cssContent)) {
            # Create a unique name based on relative path
            $relPath = $file.FullName.Substring($templateDir.Length + 1)
            $cssFileName = ($relPath -replace '\\', '_') -replace '\.html\.twig$', '.css'
            
            $cssFilePath = Join-Path $publicCssDir $cssFileName
            Set-Content -Path $cssFilePath -Value $cssContent -Encoding UTF8
            
            # Replace the style block
            $linkTag = "<link rel=""stylesheet"" href=""{{ asset('css/$cssFileName') }}"">"
            $newContent = $content -replace '(?s)<style>.*?</style>', $linkTag
            Set-Content -Path $file.FullName -Value $newContent -Encoding UTF8
            
            Write-Host "Migrated CSS for $($file.Name) to $cssFileName"
        }
    }
}
