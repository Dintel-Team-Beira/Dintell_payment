<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresa Necessária</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="max-w-md p-8 bg-white rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-gray-900">Empresa Necessária</h1>
            <p class="mt-4 text-gray-600">
                Você precisa estar associado a uma empresa para acessar o sistema.
            </p>
            <div class="mt-6">
                <a href="{{ route('logout') }}" 
                   class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Voltar ao Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>