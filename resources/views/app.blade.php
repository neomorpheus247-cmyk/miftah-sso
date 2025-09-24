
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <div style="padding: 20px; text-align: center; color: #666;">
            Loading application...
            <br><small>If this message persists, check browser console for errors</small>
        </div>
    </div>
    <script>
        // Debug info
        console.log('Vue app should be mounting...');
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
    </script>
</body>
</html>
