@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' ' . $newsArticle->title)

@section ('content')

<div class="container pt-1">

	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>
			{{ $newsArticle->title }}
		</h1>
	</div>
	@include ('layouts._partials._news.long')

	<div class="row">
		<div class="col-12 col-sm-6 col-md-8">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h3>
				@lang('news.comments')
				</h3>
			</div>
			@foreach ($newsArticle->comments->reverse() as $comment)
				@if ($comment->approved || (Auth::user() && Auth::user()->getAdmin()) || (Auth::user() && Auth::id() == $comment->user_id))
					@include ('layouts._partials._news.comment-warnings')
					@include ('layouts._partials._news.comment')
					@if (Auth::user() && $comment->reports->pluck('user_id')->contains(Auth::id()))
						<div class="alert alert-danger">
							@lang('news.comment_reported')
						</div>
					@endif
					<hr>
					<br>
				@endif
			@endforeach
		</div>
		<div class="col-12 col-sm-6 col-md-4">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h3>
				@lang('news.post_comment')
				</h3>
			</div>
			@if (Auth::user())
				{!! Html::form('POST', '/news/' . $newsArticle->slug . '/comments') !!}
					<div class="mb-3">
						{!! Html::textarea('comment', '')->id('comment')->class('form-control')->rows(4)->placeholder(__('news.post_comment')) !!}
					</div>
					<button type="submit" class="btn btn-secondary">@lang('news.submit')</button>
				{!! Html::form()->close() !!}
			@else
				<p>@lang('news.login_to_post_comment')</p>
			@endif
		</div>
	</div>
</div>

@endsection
