@extends ('layouts.admin-default')

@section ('page_title', 'News')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">News</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/news/">News</a>
			</li>
			<li class="breadcrumb-item active">
				{{ $newsArticle->title }}
			</li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-12 col-sm-8">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-pencil fa-fw"></i> Edit {{ $newsArticle->title }}
			</div>
			<div class="card-body">
				{!! Html::form('POST', '/admin/news/' . $newsArticle->slug)->acceptsFiles() !!}
					<div class="mb-3">
						{!! Html::label('Title', 'title') !!}
						{!! Html::text('title', $newsArticle->title)->id('title')->class('form-control') !!}
					</div>
					<div class="mb-3">
						{!! Html::label('Article', 'article') !!}
						{!! Html::textarea('article', $newsArticle->article)->class('form-control wysiwyg-editor') !!}
					</div>
					<div class="mb-3">
						{!! Html::label('Tags', 'tags') !!}<small> - Separate with a comma</small>
						{!! Html::text('tags', $newsArticle->getTags())->class('form-control') !!}
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
				{!! Html::form()->close() !!}
				<hr>
				{!! Html::form('POST', '/admin/news/' . $newsArticle->slug)->attribute('onsubmit', 'return ConfirmDelete()') !!}
					{!! Html::hidden('_method', 'DELETE') !!}
					<button type="submit" class="btn btn-danger btn-block">Delete</button>
				{!! Html::form()->close() !!}
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-4">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-users fa-fw"></i> Stats
			</div>
			<div class="card-body">
				<!-- // TODO -->
				To do
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-comments fa-fw"></i> Comments
			</div>
			<div class="card-body">
				@foreach ($comments as $comment)
					@include ('layouts._partials._news.comment-warnings')
					@include ('layouts._partials._news.comment')
				@endforeach
				{{ $comments->links() }}
			</div>
		</div>
	</div>
</div>

@endsection