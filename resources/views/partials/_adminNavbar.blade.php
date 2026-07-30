<nav class="navbar navbar-light">
    <div class="navbar-left">
        <div class="logo-area">
            <a class="navbar-brand" href="#">
                <img class="dark" src="{{ asset('assets/img/logo-1.png') }}" alt="svg" style="height: 40px; object-fit: contain;">
                <img class="light" src="{{ asset('assets/img/logo-1.png') }}" alt="img" style="height: 40px; object-fit: contain;">
            </a>
            <a href="#" class="sidebar-toggle">
                <img class="svg" src="{{ asset('assets/img/svg/align-center-alt.svg') }}" alt="img"></a>
        </div>
        
       
    </div>


  <div class="navbar-right">
        <ul class="navbar-right__menu">
          
           
            <li class="nav-author">
                <div class="dropdown-custom">
                    <a href="javascript:;" class="nav-item-toggle"><img
                            src="{{ asset('assets/img/author-nav.jpg') }}" alt="" class="rounded-circle">
                        @if (Auth::check())
                            <span class="nav-item__title">{{ Auth::user()->name }}<i
                                    class="las la-angle-down nav-item__arrow"></i></span>
                        @endif
                    </a>
                    <div class="dropdown-wrapper">
                        <div class="nav-author__info">
                            <div class="author-img">
                                <img src="{{ asset('assets/img/author-nav.jpg') }}" alt=""
                                    class="rounded-circle">
                            </div>
                            <div>
                                @if (Auth::check())
                                    <h6 class="text-capitalize">{{ Auth::user()->name }}</h6>
                                @endif
                                <span>UI Designer</span>
                            </div>
                        </div>
                        <div class="nav-author__options">
                            <ul>
                                <li>
                                    <a href="">
                                        <img src="{{ asset('assets/img/svg/user.svg') }}" alt="user"
                                            class="svg"> Profile</a>
                                </li>
                                <li>
                                    <a href="">
                                        <img src="{{ asset('assets/img/svg/settings.svg') }}" alt="settings"
                                            class="svg"> Settings</a>
                                </li>
                                
                            </ul>
                            <a href="#" class="nav-author__signout"
                                onclick="event.preventDefault(); document.getElementById('logout').submit();">
                                <img src="{{ asset('assets/img/svg/log-out.svg') }}" alt="log-out" class="svg">
                                ออกจากระบบ
                            </a>

                            <form id="logout" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="navbar-right__mobileAction d-md-none">
            <a href="#" class="btn-search">
                <img src="{{ asset('assets/img/svg/search.svg') }}" alt="search" class="svg feather-search">
                <img src="{{ asset('assets/img/svg/x.svg') }}" alt="x" class="svg feather-x">
            </a>
            <a href="#" class="btn-author-action">
                <img src="{{ asset('assets/img/svg/more-vertical.svg') }}" alt="more-vertical" class="svg"></a>
        </div>
    </div>

</nav>