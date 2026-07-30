<div class="sidebar__menu-group">
    <ul class="sidebar_nav">

        <li class="menu-title mt-30">
            <span>ระบบตรวจมาตรฐานรถ</span>
            <span><i class="fas fa-user-cog"></i> เจ้าหน้าที่ผู้ดูแล</span>
            <div class="border-top my-3"></div>
            <span><i class="fas fa-bars"></i> เมนู</span>
        </li>

        <li class="has-child {{ Request::is(app()->getLocale() . '/dashboards/*') ? 'open' : '' }}">
            <a href="#" class="{{ Request::is(app()->getLocale() . '/dashboards/*') ? 'active' : '' }}">
                <span class="nav-icon uil uil-create-dashboard"></span>
                <span class="menu-text">หน้าหลัก</span>
                <span class="toggle-icon"></span>
            </a>
            <ul>
                <li class="{{ Request::is(app()->getLocale() . '/dashboard') ? 'active' : '' }}"><a
                        href="{{ route('admin.dashboard')}}">Home</a></li>

            </ul>
        </li>

       <!-- <li>
            <a href="{{ route('admin.announce') }}" class="">
                <span class="nav-icon uil uil-megaphone"></span>
                <span class="menu-text">ประกาศ</span>
                <span class="badge badge-success menuItem rounded-circle">3</span>
            </a>
        </li>-->

        <li>
            <a href="{{ route('admin.cp_list') }}" class="">
                <span class="nav-icon uil uil-building"></span>
                <span class="menu-text">รายการบริษัท</span>

            </a>
        </li>

         <li>
                 <a href="#" class="nav-author__signout"
                     onclick="event.preventDefault(); document.getElementById('logout').submit();">
                     <span class="nav-icon uil uil-sign-out-alt"></span>
                     ออกจากระบบ
                 </a>

                 <form id="logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                     @csrf
                 </form>
             </li>


    </ul>
</div>
