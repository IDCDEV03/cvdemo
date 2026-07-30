<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CheckVehicles : ระบบปฏิบัติการพนักงานขับรถราชการ</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v3.0.0/css/line.css">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon.png') }}">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
    <main class="main-content">
        <div class="admin" style="background-image:url({{ asset('assets/img/admin-bg-light.png') }});">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-8">
                        <div class="edit-profile">
                            <div class="edit-profile__logos">
                                <img class="dark" src="{{ asset('assets/img/logo-1.png') }}" height="100%" alt="">
                                <img class="light" src="{{ asset('assets/img/logo-1.png') }}" height="100%" alt="">
                            </div>
                            <div class="card border-0">
                                <div class="card-header">
                                    <div class="edit-profile__title">
                                        <label class="fs-20">ลงทะเบียนระบบ Check Vehicles</label>
                                    </div>
                                </div>
                                <div class="card-body">
                    @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                                    <form action="{{route('register.store')}}" method="POST">
                                        @csrf
                                        <div class="edit-profile__body">

                                            <div class="form-group mb-20">
                                                <label for="prefix" class="fs-18 form-label">คำนำหน้า</label>
                                                <select name="prefix" id="prefix" class="form-select" required>
                                                    <option selected disabled>-- เลือกคำนำหน้า --</option>
                                                    <option value="นาย">นาย</option>
                                                    <option value="นางสาว">นางสาว</option>
                                                    <option value="นาง">นาง</option>
                                                </select>

                                            </div>

                                       <div class="mb-3">
                    <label for="first_name" class="form-label">ชื่อ</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>

                                     
                <!-- นามสกุล -->
                <div class="mb-3">
                    <label for="last_name" class="form-label">นามสกุล</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" required>
                </div>
<div class="border-top my-3"></div>
                <!-- username -->
                <div class="mb-3">
                    <label for="email" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                      <div id="username-feedback" class="text-danger mt-1 small"></div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="agency_id" class="form-label">สังกัดหน่วยงาน</label>
                  <select id="agency_id" name="agency_id" placeholder="พิมพ์ชื่อหน่วยงาน.." autocomplete="off">
                        <option value="">-- เลือกหน่วยงาน --</option>
                        @foreach ($agencies as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                        @endforeach
                    </select>
                </div>

                                            <div
                                                class="admin__button-group button-group d-flex pt-1 justify-content-md-start justify-content-center">
                                                <button type="submit"
                                                    class="btn btn-primary btn-default w-100 btn-squared text-capitalize lh-normal px-50 signIn-createBtn ">
                                                    ลงทะเบียน
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.min.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
  new TomSelect('#agency_id', {
    create: false,
    sortField: {
      field: 'text',
      direction: 'asc'
    }
  });
</script>

<script>
    const usernameInput = document.getElementById('username');
    const feedback = document.getElementById('username-feedback');
    const form = document.querySelector('form');

    let isUsernameAvailable = false;

    usernameInput.addEventListener('blur', function () {
        const username = this.value.trim();

        if (username.length > 0) {
            fetch("{{ route('username.check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ username: username })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    feedback.textContent = 'Username นี้ถูกใช้ไปแล้ว กรุณาใช้ชื่ออื่น';
                    isUsernameAvailable = false;
                } else {
                    feedback.textContent = '';
                    isUsernameAvailable = true;
                }
            });
        } else {
            feedback.textContent = '';
            isUsernameAvailable = false;
        }
    });

    form.addEventListener('submit', function (e) {
        if (!isUsernameAvailable) {
            e.preventDefault();
            feedback.textContent = 'กรุณาตรวจสอบชื่อผู้ใช้ก่อนลงทะเบียน';
            usernameInput.focus();
        }
    });
</script>

</body>

</html>
