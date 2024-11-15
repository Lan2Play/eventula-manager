<div class="news-post">
	<h2 class="news-post-title"><a href="/news/{{ $newsArticle->slug }}">{{ $newsArticle->title }}</a></h2>
	<br>
	{!! strip_tags(substr($newsArticle->article, strpos($newsArticle->article, "<p"), strpos($newsArticle->article, "</p>")+4)) !!}
	<br><br>
	<p><a href="/news/{{ $newsArticle->slug }}">@lang('layouts.read_more')</a></p>
	<hr>
	<div class="row">
		<div class="col-12 col-sm-6">
			<div class="row">
				<div class="col-12">
					@lang('layouts.share'):
					@include ('layouts._partials._news.sharer')
					
				</div>
				<div class="col-12">
					@lang('layouts.tags'):
					@foreach ($newsArticle->tags as $tag)
						<small><a href="{{ url('/news/tags')}}/{{ $tag->slug }}">{{ $tag->tag }}</a>,</small>
					@endforeach
				</div>
			</div>
		</div>
		<div class="col-12 col-sm-6">
			<!-- // TODO - add user account public pages -->
			<p class="news-post-meta float-end">{{ date('F d, Y', strtotime($newsArticle->created_at)) }} by

				@if(isset($newsArticle->user->username))
					<a href="#">{{ $newsArticle->user->username }}</a>
				@else
					@lang('news.unknownuser')
				@endif


				<span class="d-none d-sm-block"> | @lang('layouts.comments'): {{ $newsArticle->comments->count() }}</span></p>
		</div>
	</div>
</div><br>