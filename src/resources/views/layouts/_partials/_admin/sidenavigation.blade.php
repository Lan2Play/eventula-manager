<div class="offcanvas-md offcanvas-start sidebar col-md-3 col-lg-2 p-0" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel" data-bs-scroll="false" style="top: 48px; min-height: calc(100vh - 48px);">
	<div class="offcanvas-header d-md-none">
		<h5 class="offcanvas-title text-light" id="sidebarMenuLabel">Admin Navigation</h5>
		<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body p-0">
		<ul class="navbar-nav side-nav ps-2">
				<li class="nav-item {{ Request::is('admin') ? 'active' : '' }}"> <a class="nav-link" href="/admin"><i
							class="fa fa-dashboard fa-fw"></i> Dashboard</a>
				</li>
				<li class="nav-item {{ Request::is('admin/events') ? 'active' : '' }}"> <a class="nav-link"
						href="/admin/events"><i class="fa fa-book fa-fw"></i> Events</a>
					<ul class="navbar-nav ms-5">
						@foreach (Helpers::getEvents('DESC', 5, true, "events_page") as $event)
						<li class="nav-item">
							<a class="nav-link" href="/admin/events/{{ $event->slug }}"> {{ $event->display_name }} @if ($event->status!= 'PUBLISHED') - {{ $event->status }} @endif
							</a>
						</li> @endforeach
					</ul>
					<!-- /.nav-second-level -->
				</li>
				<li class="nav-item {{ Request::is('admin/news') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/news"><i class="fa fa-comment fa-fw"></i> News</a>
				</li>
				<li class="nav-item {{ Request::is('admin/users') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/users"><i class="fa fa-user fa-fw"></i> Users</a>
				</li>
				<li class="nav-item {{ Request::is('admin/polls') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/polls"><i class="fa fa-list fa-fw"></i> Polls</a>
				</li>
				<li class="nav-item {{ Request::is('admin/venues') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/venues"><i class="fa fa-building fa-fw"></i> Venues</a>
				</li>

				<li class="nav-item {{ Request::is('admin/gallery') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/gallery"><i class="fa fa-camera fa-fw"></i> Gallery</a>
				</li>
				<li class="nav-item {{ Request::is('admin/help') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/help"><i class="fa fa-question fa-fw"></i> Help</a>
				</li>
				<li class="nav-item {{ Request::is('admin/games') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/games"><i class="fa fa-gamepad fa-fw"></i> Games</a>
				</li>
				<li class="nav-item {{ Request::is('admin/matchmaking') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/matchmaking"><i class="fa fa-list-ol fa-fw"></i>
						Matchmaking</a>
				</li>
				<li class="nav-item {{ Request::is('admin/purchases') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/purchases"><i class="fa fa-credit-card fa-fw"></i>
						Purchases</a>
				</li>
				<li class="nav-item {{ Request::is('admin/settings') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/settings"><i class="fa fa-book fa-fw"></i> Settings</a>
				</li>
				<li class="nav-item {{ Request::is('admin/mailing') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/mailing"><i class="fas fa-envelope fa-fw"></i> Mailing</a>
				</li>
				<li class="nav-item {{ Request::is('admin/credit') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/credit"><i class="fa fa-refresh fa-fw" aria-hidden="true"></i>
						Credit System (Beta)</a>
				</li>
				<li class="nav-item {{ Request::is('admin/shop') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/shop"><i class="fa fa-shopping-cart fa-fw" aria-hidden="true"></i>
						Shop
						(Beta)</a>
				</li>
				@if (Settings::isShopEnabled())
				<li class="nav-item {{ Request::is('admin/orders') ? 'active' : '' }}">
					<a class="nav-link" href="/admin/orders"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i>
						Orders (Beta)</a>
				</li>
				@endif
 		</ul>
 	</div>
 </div>