<!DOCTYPE html>
<html lang="en" class="full-height" data-bs-theme="{{Colors::getTheme()}}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>
			@hasSection ('page_title')
				@yield('page_title') | {{ Settings::getOrgName() }}
			@else
				{{ Settings::getOrgTagline() }} | {{ Settings::getOrgName() }}
			@endif
		</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700&display=swap' rel='stylesheet' type='text/css' />
		<link href="/css/app.css" rel=stylesheet />
	</head>
	<body class="full-height">
		<div class="container pt-1">
			<div class="row">
				<div class="col-12">
					<picture>
						<source srcset="{{ Settings::getOrgLogo() }}.webp" type="image/webp">
						<source srcset="{{ Settings::getOrgLogo() }}" type="image/jpeg">
						<img class="img-fluid" src="{{ Settings::getOrgLogo() }}"/>
					</picture>
				</div>
			</div>
		</div>
		@yield ('content')
	</body>
</html>
