<div class="row">
	<div class="col-2">
		<img class="img-fluid rounded img-small news-post-comment-image" alt="{{ $comment->user->username }}'s Avatar" src="{{ $comment->user->avatar }}"/>
		<p class="news-post-comment-image-text">{{ $comment->user->username }}</p>
	</div>
	<div class="col-10">
		<p>{{ $comment->comment }}</p>
		<span class="text-muted"><small>@lang('layouts.posted_on'): {{ $comment->created_at }}</small></span>
		@if (Auth::user() && Auth::id() == $comment->user_id)
			<a href="" onclick="editComment('{{ $comment->comment }}', '{{ $comment->id }}')" data-bs-toggle="modal" data-bs-target="#editCommentModal">@lang('layouts.edit_comment')</a> /
		@endif
		@if (Auth::user() && (Auth::user()->getAdmin() || $comment->user_id == Auth::id()))
			@php
				$postUrl = "";
			@endphp
			@if (Auth::user()->getAdmin())
				@php
					$postUrl = "/admin";
				@endphp
			@endif
			<a href="{{ $postUrl }}/news/{{ $comment->newsArticle->slug }}/comments/{{ $comment->id }}/delete">
				@lang('layouts.delete_comment')
			</a>
		@endif
		@if ($comment->approved && $comment->reviewed)
			@if (Auth::user() && !$comment->reports->pluck('user_id')->contains(Auth::id()) && $comment->user_id != Auth::id())
				@if ((Auth::user() && (Auth::user()->getAdmin() || $comment->user_id == Auth::id())) || (Auth::user() && Auth::id() == $comment->user_id))
					/
				@endif
				<a href="/news/{{ $newsArticle->slug }}/comments/{{ $comment->id }}/report">
					@lang('layouts.report_comment')
				</a>
			@endif
		@endif
	</div>
</div>


<!-- Edit Comment Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1" role="dialog" aria-labelledby="editCommentModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editCommentModalLabel">@lang('layouts.edit_comment')</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			@if (Auth::user())
				{!! Html::form('POST', '/news/' . $newsArticle->slug . '/comments')->id('edit_comment_modal_form') !!}
					<div class="modal-body">
						<div class="mb-3">
							{!! Html::textarea('comment_modal', '')->id('comment_modal')->class('form-control')->rows('4')->placeholder(__('layouts.post_a_comment')) !!}
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-success">@lang('layouts.edit')</button>
						</div>
					</div>
				{!! Html::form()->close() !!}
			@else
				<div class="modal-body">
					<p>@lang('layouts.please_logon_for_comment')</p>
				</div>
			@endif
		</div>
	</div>
</div>

<script>
	function editComment(comment, comment_id)
	{
		console.log(comment_id);
		jQuery("#comment_modal").val(comment);
		jQuery("#edit_comment_modal_form").prop('action', '/news/{{ $newsArticle->slug }}/comments/' + comment_id);
	}
</script>