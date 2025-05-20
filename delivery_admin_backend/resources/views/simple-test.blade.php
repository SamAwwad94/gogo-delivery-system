<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Vue Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
            <h1 class="text-2xl font-bold mb-4 text-center">Simple Vue Test</h1>
            
            <!-- Vue App Container -->
            <div id="simple-test-app"></div>
            
            <div class="mt-4 text-center text-gray-500 text-sm">
                This is a simple test to verify that Vue is working correctly.
            </div>
        </div>
    </div>
    
    @vite('resources/js/simple-test.js')
</body>
</html>
