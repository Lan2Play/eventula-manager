<div class="news-post">
	{!! $newsArticle->article !!}
	<hr>
	<div class="row">
		<div class="col-12 col-sm-8">
			<div class="row">
				<div class="col-2">
				@lang('layouts.share'):
				</div>
				<div class="col-10">
					@include ('layouts._partials._news.sharer')
				</div>
				<div class="col-2">
					@lang('layouts.tags'):
				</div>
				<div class="col-10">
					@foreach ($newsArticle->tags as $tag)
						<a href="{{ url('/news/tags')}}/{{ $tag->slug }}">{{ $tag->tag }}</a>,
					@endforeach
				</div>
			</div>
		</div>
		<div class="col-12 col-sm-4">
			<!-- // TODO - add user account public pages -->
			<p class="news-post-meta float-end">{{ date('F d, Y', strtotime($newsArticle->created_at)) }} by
				@if(isset($newsArticle->user->username))
					<a href="#">{{ $newsArticle->user->username }}</a>
				@else
					@lang('news.unknownuser')
				@endif

			</p>
		</div>
	</div>
</div>