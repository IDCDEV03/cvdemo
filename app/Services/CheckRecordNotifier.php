<?php
namespace App\Services;

use App\Models\User;
use App\Notifications\NewCheckRecordConfirmed;
use Illuminate\Support\Facades\DB;

class CheckRecordNotifier
{
    public function notifySupplyOnConfirmed(array $chkRecordRow): void
    {
        // กันยิงซ้ำ: ถ้ามี notified_at แล้วไม่ต้องทำอะไร
        if (!empty($chkRecordRow['notified_at'])) {
            return;
        }

        // ผู้รับ: role = 'supply' ใน supply เดียวกัน
        $recipients = User::query()
            ->where('role', 'supply')
            ->where('agency_id', $chkRecordRow['agency_id'])
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        $payload = [
            'chk_id'     => $chkRecordRow['id'],
            'record_id'  => $chkRecordRow['record_id'],
            'veh_id'     => $chkRecordRow['veh_id'],
            'form_id'    => $chkRecordRow['form_id'],
            'agency_id'  => $chkRecordRow['agency_id'],
            'user_id'    => $chkRecordRow['user_id'],
            'created_at' => $chkRecordRow['created_at'],
            'url'        => route('form_report' , $chkRecordRow['record_id']),
            'message'    => "รหัสการตรวจ: {$chkRecordRow['record_id']}",
        ];

        foreach ($recipients as $user) {
            $user->notify(new NewCheckRecordConfirmed($payload));
        }

        // mark notified_at (ทำใน transaction จะปลอดภัย)
        DB::table('chk_records')
            ->where('id', $chkRecordRow['id'])
            ->whereNull('notified_at')
            ->update(['notified_at' => now()]);
    }
}
