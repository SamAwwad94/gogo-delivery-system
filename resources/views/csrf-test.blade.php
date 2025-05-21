<!DOCTYPE html>
<html>
<head>
    <title>CSRF Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>CSRF Token Test</h1>
    
    <div>
        <p>Current CSRF Token: <code>{{ csrf_token() }}</code></p>
    </div>
    
    <form action="{{ route('settings.updateLogos') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <p>Form CSRF Token: <input type="text" value="{{ csrf_token() }}" readonly style="width: 300px;"></p>
        <input type="file" name="site_logo">
        <button type="submit">Test Submit</button>
    </form>
</body>
</html>
