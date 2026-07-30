<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พรีวิว: {{ $template->template_name }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <style>
        :root {
            --paper-bg: #ffffff;
            --app-bg: #f3f4f6; /* สีเทาอ่อนแบบ Apple/Modern Web */
            --primary-color: #5f63f2; /* สีหลัก Hexadash */
            --text-main: #333333;
        }

        body { 
            background-color: var(--app-bg); 
            font-family: 'Sarabun', sans-serif; 
            color: var(--text-main);
            padding-bottom: 60px;
        }
        
        /* 1. แถบเครื่องมือ (Modern Sticky Toolbar) */
        .preview-toolbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px); /* เอฟเฟกต์กระจก (Glassmorphism) */
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* 2. จำลองหน้ากระดาษ A4 (Soft Shadow & Rounded) */
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 40px auto;
            background: var(--paper-bg);
            /* เงาแบบหลายชั้นให้กระดาษดูลอยมีมิติสมจริง */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 12px; /* โค้งมนนิดๆ ให้น่ามองบนจอ */
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        /* 3. กล่องแทนที่ตาราง (Modern Placeholder) */
        .report-body-placeholder {
            flex-grow: 1;
            margin: 35px 0;
            border: 2px dashed rgba(95, 99, 242, 0.3);
            border-radius: 12px;
            background: linear-gradient(145deg, rgba(95,99,242,0.02) 0%, rgba(95,99,242,0.08) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: var(--primary-color);
            text-align: center;
            padding: 40px;
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 15px -3px rgba(95,99,242,0.2);
            margin-bottom: 20px;
        }

        /* จัดการตารางในเนื้อหา */
        table { width: 100% !important; border-collapse: collapse; }
        
        /* 4. สไตล์ตอนกด พิมพ์ (Print CSS) */
        @media print {
            body { background: none; padding: 0; }
            .preview-toolbar { display: none !important; }
            .a4-container { 
                box-shadow: none; 
                margin: 0; 
                width: 100%; 
                border-radius: 0; /* เอาความโค้งออกตอนพิมพ์ */
                padding: 10mm; 
            }
        }
    </style>
</head>
<body>

    <div class="preview-toolbar">
        <div class="container d-flex justify-content-between align-items-center" >
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center">
                    <i class="uil uil-file-alt" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">พรีวิว: {{ $template->template_name }}</h5>
                    <span class="badge bg-light text-primary border mt-1 fw-normal">โหมดจำลองกระดาษ A4</span>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button onclick="window.close()" class="btn btn-light border btn-squared px-4 hover-shadow">
                    ปิดหน้าต่าง
                </button>
                <button onclick="window.print()" class="btn btn-primary btn-squared px-4 shadow-sm hover-shadow">
                    <i class="uil uil-print me-1"></i> พิมพ์ / บันทึก PDF
                </button>
            </div>
        </div>
    </div>

    <div class="a4-container">
        
        <div class="report-header">
            {!! $previewHeader !!}
        </div>

        <div class="report-body-placeholder">
            <div class="icon-wrapper">
                <i class="uil uil-table" style="font-size: 2.5rem; color: var(--primary-color);"></i>
            </div>
            <h4 class="fw-bold mb-2">พื้นที่แสดงผลตารางรายการตรวจเช็ค</h4>
            <p class="text-muted mb-0" style="max-width: 400px;">
                ระบบจะนำรายการที่ตั้งค่าไว้ พร้อมรูปถ่ายและผลการตรวจสอบ มาแทรกในส่วนนี้โดยอัตโนมัติเมื่อมีการออกรายงานจริง
            </p>
        </div>

        <div class="report-footer">
            {!! $previewFooter !!}
        </div>

    </div>

</body>
</html>