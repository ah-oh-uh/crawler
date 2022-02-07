<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body>
            <form method="POST">
                @csrf
                <input type="url" name="url" placeholder="Enter URL"/>
                <input type="number" name="maxPages" min="1" max="10" value="6"/>
                <button type="submit">Crawl</button>
            </form>
            
    </body>
</html>
