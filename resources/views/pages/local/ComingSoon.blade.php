@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
          <div class="row justify-content-center">
               <div class="col-12">
                  <div class="min-vh-100 content-center">
                     <div class="maintenance-page text-center">
                        <img src="{{asset('coming_soon.png')}}" alt="maintenance" width="256px" />
                        <h5 class="maintenance-page__title">We are currently performing maintenance</h5>
                        <p class="fw-500">We're making the system more awesome.We'll be back shortly.</p>
                     </div>
                  </div>
               </div>
            </div>
    </div>
@endsection

