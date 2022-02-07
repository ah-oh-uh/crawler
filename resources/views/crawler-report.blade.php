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
            <p><a href="/"><< Back</a></p>

			<h4>Summary</h4>
			<table>
				<tr>
					<td>Number of pages crawled</td>
					<td>{{ $stats['pages'] }}</td>
				</tr>
				<tr>
					<td>Number of a unique images</td>
					<td>{{ $links['image'] }}</td>
				</tr>
				<tr>
					<td>Number of unique internal links</td>
					<td>{{ $links['internal'] }}</td>
				</tr>
				<tr>
					<td>Number of unique external links</td>
					<td>{{ $links['external'] }}</td>
				</tr>
				<tr>
					<td>Average page load in seconds</td>
					<td>{{ $stats['page-load'] }}</td>
				</tr>
				<tr>
					<td>Average word count</td>
					<td>{{ $stats['words'] }}</td>
				</tr>
				<tr>
					<td>Average title length</td>
					<td>{{ $stats['title-length'] }}</td>
				</tr>
			</table>
			<h4>Pages</h4>
			<table>
				<tr>
					<th>URL</th><th>HTTP Status Code</th>
				</tr>
				@foreach ($pages as $page)
				<tr>
					<td>{{ $page['url'] }}</td>
					<td align="center">{{ $page['code'] }}</td>
				</tr>
				@endforeach
			</table>
    </body>
</html>
