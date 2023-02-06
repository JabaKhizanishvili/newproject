<div class="sticky">
					<aside class="app-sidebar bg-dark">

						<div class="main-sidemenu mt-4">
							<div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"/></svg></div>
                            <ul class="side-menu">
                                @php
                                    use App\Helpers\Helper;
                                    $configData = Helper::applClasses();
                                @endphp
                                @if(!empty($menuData[0]) && isset($menuData[0]))
                                    @foreach ($menuData[0]->menu as $menu)
                                        @if(isset($menu->navheader))
                                            <li class="navigation-header">
                                                <a class="navigation-header-text">{{ __('admin.'.$menu->navheader) }}</a>
                                                <i class="navigation-header-icon material-icons"></i>
                                            </li>
                                        @else
                                            @php
                                                $custom_classes="";
                                                if(isset($menu->class))
                                                {
                                                $custom_classes = $menu->class;
                                                }
                                            @endphp
                                        <div class="slide" {{(request()->is($menu->url.'*')) ? 'active' : '' }}">
                                            <a
                                            @if(isset($menu->submenu))
                                            data-bs-toggle="sub-slide" href="javascript:void(0);"
                                            @endif
                                            @if(!empty($configData['activeMenuColor'])) {{'style=background:none;box-shadow:none;'}} @endif
                                            href="@if(($menu->url)==='javascript:void(0)'){{$menu->url}} @else{{locale_route($menu->url)}} @endif"
                                            class="{{$custom_classes}} {{ (request()->route()->getName() === $menu->url) ? 'active '.$configData['activeMenuColor'] : ''}}side-menu__item"
                                              >
                                              <i class="material-icons"></i>
                                                    <span class="menu-title">{{ $menu->name}}</span>
                                                    @if(isset($menu->tag))
                                                        <span class="{{$menu->tagcustom}}">{{$menu->tag}}</span>
                                                    @endif
                                                    @if(isset($menu->submenu)) <i class="angle fe fe-chevron-down"></i> @else <i class="angle fe fe-chevron-right"></i> @endif
                                            </a>
                                            @if(isset($menu->submenu))
                                            <ul class="sub-slide-menu">
                                            @foreach ($menu->submenu as $v)
                                                  <li style="cursor:pointer"><a class="sub-side-menu__item"  href={{locale_route($v->url)}}>{{$v->name}}</a></li>
                                            @endforeach
                                            </ul>
                                        </div>
                                            @endif

                                            @endif

                                    @endforeach
                                @endif
							</ul>
							<div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg></div>
						</div>
					</aside>
				</div>
