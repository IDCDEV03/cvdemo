<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
      @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
      @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
        }
        body, h1, h2, h3, h4, h5, h6, p, span, div, table, td, th, * {
            font-family: 'THSarabunNew', sans-serif !important;
        }
      
     
        /* 2. ตั้งค่าระยะขอบกระดาษใหม่ให้เหลือน้อยที่สุด (เพิ่มพื้นที่ใช้งาน) */
        @page {
            margin: 1cm 1.5cm; /* ขอบบน-ล่าง 1 ซม. / ขอบซ้าย-ขวา 1.5 ซม. */
        }

    
        body { 
            font-size: 13pt; /* ลดจาก 16pt เหลือ 13pt หรือ 14pt */
            line-height: 1.05; /* บีบช่องว่างระหว่างบรรทัดให้ชิดกันขึ้น */
            color: #000; 
        }

        /* 4. บีบตารางให้กระทัดรัดที่สุด */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 5px; /* ลดช่องว่างใต้ตาราง */
        }
        td, th { 
            border: 1px solid #000; 
            padding: 2px 4px; /* บีบความอ้วนของเซลล์ในตาราง */
            vertical-align: top;
        }

        /* 5. ย่อขนาดหัวข้อต่างๆ ไม่ให้กินพื้นที่ */
        h3, h4, h5 {
            margin-top: 5px;
            margin-bottom: 5px;
        }
        
        /* 6. ย่อขนาดรูปภาพใน Header/Footer (ถ้ามี) */
        img {
            max-height: 40px !important; /* บังคับไม่ให้รูปใหญ่เกินไปจนดันหน้ากระดาษล้น */
        }

      
    </style>
</head>
<body>
    <div class="header">
        {!! $reportTemplate->header_html !!}
    </div>

    <div class="content">
        <h3 style="margin-top: 20px;">รายละเอียดการตรวจสอบ</h3>
        @foreach($categories as $cat)
            @if(isset($results[$cat->category_id]))
                <div style="background: #eee; padding: 5px; font-weight: bold; margin-top: 10px; border: 1px solid #000;">
                    {{ $cat->chk_cats_name }}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>รายการตรวจสอบ</th>
                            <th width="15%">ผลการตรวจ</th>
                            <th width="30%">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results[$cat->category_id] as $res)
                            <tr>
                                <td align="center">{{ $loop->iteration }}</td>
                                <td>{{ $res->item_name }}</td>
                                <td align="center">
                                    {{ $res->result_status == '1' ? 'ปกติ' : ($res->result_status == '2' ? 'ไม่ปกติฯ' : 'ปรับปรุง') }}
                                </td>
                                <td>{{ $res->result_value }} {{ $res->user_comment }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </div>

    <div class="footer" style="margin-top: 30px;">
        {!! $reportTemplate->footer_html !!}
    </div>
</body>
</html>