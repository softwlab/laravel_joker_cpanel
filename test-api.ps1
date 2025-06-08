$apiKey = "a8f0b0f4d008481a1fb14cb01de7e9fb156ef905d0e3e3dc1e1221cc14254df5"
$domain = "app.acessarchaveprime.com"
$url = "http://127.0.0.1:8000/api/public/domain_external/$domain"

$headers = @{
    "X-API-KEY" = $apiKey
    "Accept" = "application/json"
}

Write-Host "Testando API Pública com a URL: $url"
try {
    $response = Invoke-WebRequest -Uri $url -Headers $headers -Method GET -ErrorAction Stop
    Write-Host "Status: $($response.StatusCode)"
    Write-Host "Resposta:"
    $response.Content
}
catch [System.Net.WebException] {
    Write-Host "Erro:"
    Write-Host $_.Exception.Message
    if ($_.Exception.Response) {
        $responseStream = $_.Exception.Response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($responseStream)
        $responseBody = $reader.ReadToEnd()
        Write-Host "Corpo da resposta de erro:"
        Write-Host $responseBody
    }
}
