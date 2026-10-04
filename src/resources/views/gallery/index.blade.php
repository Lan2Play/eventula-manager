@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . __('gallery.gallery'))

@section ('content')

<div class="container pt-1">
    <div class="pb-2 mt-4 mb-4 border-bottom">
        <h1>@lang('gallery.gallery')</h1>
    </div>
    <div class="row card-deck">
        @foreach ($albums as $album)
		<div class="align-items-stretch mb-4 col-md-4 col-sm-6 col-12">
    		<div class="card h-100 d-flex flex-column">
        		<div class="w-100 d-flex align-items-center justify-content-center overflow-hidden pt-2" style="height: 150px;">
            		<a href="/gallery/{{ $album->slug }}" class="d-block w-100 h-100">
						@if (isset($album->album_cover_id) && trim($album->album_cover_id) != '')
							<img src="{{ $album->getAlbumCoverPath() }}.webp" alt="Album Cover" class="img-fluid mx-auto d-block" style="object-fit: contain; max-height: 100%; max-width: 100%;">
						@else
							<img src="http://placehold.it/600x300" alt="Placeholder Image" class="img-fluid mx-auto d-block" style="object-fit: contain; max-height: 100%; max-width: 100%;">
						@endif
					</a>
				</div>
				<div class="card-body">
					<h4 class="card-title">{{ $album->name }}</h4>
					<div class="card-subtitle mb-2 text-muted" style="min-height: 1.5em;">
						@if ($album->event)
							@lang('gallery.event') {{ $album->event->display_name }}
						@endif
					</div>
					<p class="card-text">{{ $album->description }}</p>
				</div>
				<div class="card-footer d-flex justify-content-between align-items-center">
					<a href="/gallery/{{ $album->slug }}" class="btn btn-primary">@lang('gallery.view_album')</a>
					<span class="text-muted">@lang('gallery.date') {{ $album->created_at->format(__('date.date_short')) }}</span>
				</div>
			</div>
			</div>
        @endforeach
    </div>
</div>

@endsection
