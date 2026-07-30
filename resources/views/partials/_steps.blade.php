@php
    $steps = [
        1 => 'กรอกข้อมูลรถ',
        2 => 'ตรวจตามหมวดหมู่',
        3 => 'สรุปผลการตรวจ',
    ];
@endphp


<div class="dm-steps mb-4">
    <ul class="nav">
        @foreach ($steps as $stepNum => $stepLabel)
            <li class="dm-steps__item {{ $currentStep == $stepNum ? 'active' : 'finish' }}">
                <div class="dm-steps__line"></div>
                <div class="dm-steps__content">
                    <span class="dm-steps__icon"><i class="la la-check"></i></span>
                    <span class="dm-steps__text"> {{ $stepNum }}. {{ $stepLabel }}</span>
                </div>
            </li>
        @endforeach

    </ul>
</div>
