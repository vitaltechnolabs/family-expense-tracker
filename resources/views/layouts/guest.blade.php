<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Built with ❤️ by <a href="https://vitaltechnolabs.com" target="_blank"
                    class="text-blue-600 hover:text-blue-800">Vital Technolabs LLP</a>.
            </p>
        </div>
    </div>
</body>

</html>