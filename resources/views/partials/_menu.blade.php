 @php
     use App\Enums\Role;
     $role = Auth::user()->role;
 @endphp
 <div class="sidebar__menu-group">
     <ul class="sidebar_nav">

         <li class="menu-title mt-30">
             @if ($role === Role::Agency)
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <div class="border-top my-3"></div>
                 <span>เมนูสำหรับหน่วยงาน</span>
             @elseif ($role === Role::User)
                 @php
                     $agent_id = Auth::user()->company_code;
                     $agent = DB::table('supply_datas')
                         ->select('supply_name')
                         ->where('sup_id', '=', $agent_id)
                         ->first();
                 @endphp
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <span><i class="far fa-building"></i> {{ $agent->supply_name }} </span>
                 <div class="border-top my-3"></div>
                 <span><i class="fas fa-bars"></i> เมนูสำหรับผู้ใช้งาน</span>
             @elseif ($role === Role::Inspector)                
                 <span>ระบบตรวจมาตรฐานรถ</span>                
                 <div class="border-top my-3"></div>
                 <span><i class="fas fa-bars"></i> เมนูสำหรับผู้ใช้งาน</span>
             @elseif ($role === Role::Manager)
                 @php
                     $agent_id = Auth::user()->company_code;
                     $agent = DB::table('supply_datas')
                         ->select('supply_name')
                         ->where('sup_id', '=', $agent_id)
                         ->first();
                 @endphp
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <span><i class="far fa-building"></i> {{ $agent->supply_name }} </span>
                 <div class="border-top my-3"></div>
                 <span>เมนูสำหรับผู้จัดการ BU</span>
             @elseif ($role === Role::Staff)
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <div class="border-top my-3"></div>
                 <span>เมนูสำหรับบุคลากร</span>
             @elseif ($role === Role::Company)
             @php
                 $company_name = Auth::user()->name;
             @endphp
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <span><i class="far fa-building"></i> {{ $company_name }} </span>
                 <div class="border-top my-3"></div>
                 <span>เมนูสำหรับหน่วยงาน</span>
             @elseif ($role === Role::Supply)
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <div class="border-top my-3"></div>
                 <span>เมนูสำหรับบุคลากร</span>
             @endif
         </li>


         @if ($role === Role::Agency)
             <li>
                 <a href="{{ route('agency.index') }}" class="">
                     <span class="nav-icon uil uil-create-dashboard"></span>
                     <span class="menu-text">หน้าหลัก</span>
                 </a>

             </li>
             <li>
                 <a href="{{ route('agency.main') }}" class="">
                     <span class="nav-icon uil uil-megaphone"></span>
                     <span class="menu-text">ประกาศ</span>
                     <span class="badge badge-success menuItem rounded-circle">3</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('agency.manager_list') }}" class="">
                     <span class="nav-icon uil uil-users-alt"></span>
                     <span class="menu-text">รายชื่อหัวหน้า</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('agency.user_list') }}" class="">
                     <span class="nav-icon uil uil-users-alt"></span>
                     <span class="menu-text">รายชื่อเจ้าหน้าที่</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('agency.veh_list', ['id' => Auth::id()]) }}" class="">
                     <span class="nav-icon uil uil-truck"></span>
                     <span class="menu-text">รายการทะเบียนรถ</span>
                 </a>
             </li>
             <li class="has-child">
                 <a href="#" class="">
                     <span class="nav-icon far fa-list-alt"></span>
                     <span class="menu-text">แบบฟอร์ม</span>
                     <span class="toggle-icon"></span>
                 </a>
                 <ul>
                     <li>
                         <a href="{{ route('agency.form_list') }}">รายการฟอร์ม</a>
                     </li>
                     <li>
                         <a href="{{ route('agency.create_form') }}">สร้างฟอร์ม</a>
                     </li>

                 </ul>
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
         @elseif ($role === Role::User)
             <li>
                 <a href="{{ route('local.home') }}" class="">
                     <span class="nav-icon uil uil-create-dashboard"></span>
                     <span class="menu-text">หน้าหลัก</span>

                 </a>
             </li>


             <li>
                 <a href="{{ route('user.announce') }}" class="">
                     <span class="nav-icon uil uil-megaphone"></span>
                     <span class="menu-text">ประกาศ</span>

                 </a>
             </li>
             <li>
                 <a href="{{ route('user.chk_list') }}" class="">
                     <span class="nav-icon uil uil-file-copy-alt"></span>
                     <span class="menu-text">รายการตรวจรถ</span>
                     <span class="toggle-icon"></span>
                 </a>

             <li>
                 <a href="{{ route('coming_soon') }}" class="">
                     <span class="nav-icon uil-file-edit-alt"></span>
                     <span class="menu-text">ประวัติแจ้งซ่อม</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('coming_soon') }}" class="">
                     <span class="nav-icon uil uil-file-plus-alt"></span>
                     <span class="menu-text">ประวัติการขอเบิก</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('user.doc_list') }}" class="">
                     <span class="nav-icon uil-file-edit-alt"></span>
                     <span class="menu-text">บันทึกข้อความ</span>
                 </a>
             </li>


             </li>
             <li>
                 <a href="{{ route('user.profile') }}" class="">
                     <span class="nav-icon uil uil-user"></span>
                     <span class="menu-text">บัญชีผู้ใช้</span>
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
         @elseif ($role === Role::Inspector)
             <li>
                 <a href="{{route('ins-dashboard')}}" class="">
                     <span class="nav-icon uil uil-create-dashboard"></span>
                     <span class="menu-text">หน้าหลัก</span>

                 </a>
             </li>

             <li>
                 <a href="{{route('vehicles.index')}}" class="">
                     <span class="nav-icon uil uil-car"></span>
                     <span class="menu-text">รายการรถ</span>
                 </a>
             </li>

             <li>
                 <a href="{{route('vehicles.create')}}" class="">
                     <span class="nav-icon uil uil-plus-circle"></span>
                     <span class="menu-text">ลงทะเบียนรถใหม่</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('ins.profile') }}" class="">
                     <span class="nav-icon uil uil-user"></span>
                     <span class="menu-text">แก้ไขโปรไฟล์</span>
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
         @elseif ($role === Role::Manager)
             <li>
                 <a href="{{ route('manager.index') }}" class="">
                     <span class="nav-icon uil uil-create-dashboard"></span>
                     <span class="menu-text">หน้าหลัก</span>

                 </a>
             </li>

             <li>
                 <a href="#" class="">
                     <span class="nav-icon uil uil-megaphone"></span>
                     <span class="menu-text">ประกาศ</span>
                     <span class="badge badge-info menuItem rounded-circle">2</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('manager.company_list', ['id' => $agent_id]) }}" class="">
                     <span class="nav-icon uil uil-building"></span>
                     <span class="menu-text">รายการบริษัทฯ</span>
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
         @elseif ($role === Role::Staff)
             <li>
                 <a href="{{ route('staff.index') }}" class="">
                     <span class="nav-icon uil uil-truck"></span>
                     <span class="menu-text">หน้าหลัก</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('staff.veh_list') }}" class="">
                     <span class="nav-icon uil uil-truck"></span>
                     <span class="menu-text">รายการทะเบียนรถ</span>
                 </a>
             </li>
             <li class="has-child">
                 <a href="#" class="">
                     <span class="nav-icon far fa-list-alt"></span>
                     <span class="menu-text">แบบฟอร์ม</span>
                     <span class="toggle-icon"></span>
                 </a>
                 <ul>
                     <li>
                         <a href="{{ route('staff.form_list') }}">รายการฟอร์ม</a>
                     </li>
                     <li>
                         <a href="{{ route('staff.form_new') }}">สร้างฟอร์ม</a>
                     </li>

                 </ul>
             </li>

             <li>
                 <a href="{{ route('staff.supplies.index') }}" class="">
                     <span class="nav-icon uil uil-building"></span>
                     <span class="menu-text">จัดการ Supply</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('staff.insurance.index') }}" class="">
                     <span class="nav-icon uil uil-shield-check"></span>
                     <span class="menu-text">บริษัทประกันภัย</span>
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
         @elseif ($role === Role::Company)
             <li>
                 <a href="{{ route('company.index') }}" class="">
                     <span class="nav-icon uil uil-home-alt"></span>
                     <span class="menu-text">หน้าหลัก</span>
                 </a>
             </li>
             
             <li>
                 <a href="{{ route('company.supplies.index') }}" class="">
                     <span class="nav-icon uil uil-truck"></span>
                     <span class="menu-text">รายการบริษัทในเครือ</span>
                 </a>
             </li>
           

             <li>
                 <a href="{{route('company.vehicles.list')}}" class="">
                     <span class="nav-icon uil uil-truck"></span>
                     <span class="menu-text">รายการรถ</span>
                 </a>
             </li>

             <li>
                 <a href="{{route('company.vehicles.inform')}}" class="">
                     <span class="nav-icon uil uil-check-circle"></span>
                     <span class="menu-text">รายการตรวจรถ</span>
                 </a>
             </li>

             <li>
                 <a href="{{route('drivers.index')}}" class="">
                     <span class="nav-icon uil uil-user-md"></span>
                     <span class="menu-text">รายการพนักงานขับรถ</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('company.profile') }}" class="">
                     <span class="nav-icon uil uil-user"></span>
                     <span class="menu-text">แก้ไขบัญชีผู้ใช้</span>
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
         @elseif ($role === Role::Supply)
             <li>
                 <a href="{{ route('supply.home') }}" class="">
                     <span class="nav-icon uil uil-home-alt"></span>
                     <span class="menu-text">หน้าหลัก</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('supply.veh_list') }}" class="">
                     <span class="nav-icon uil uil-truck"></span>
                     <span class="menu-text">รายการรถ</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('supply.chk_list') }}" class="">
                     <span class="nav-icon uil uil-check-circle"></span>
                     <span class="menu-text">รายการตรวจรถ</span>
                 </a>
             </li>

             <li>
                 <a href="{{ route('supply.inspector_list', ['id' => Auth::user()->user_id]) }}" class="">
                     <span class="nav-icon uil uil-user-md"></span>
                     <span class="menu-text">รายการช่างตรวจ</span>
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
         @endif



     </ul>
 </div>
