<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * แสดงรายการแจ้งเตือนทั้งหมด
     */
    public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);

        return view('pages.notifications.index', compact('notifications'));
    }

    /**
     * อ่านแจ้งเตือน 1 รายการ
     * - mark as read
     * - redirect ไป url ที่แนบมากับ notification
     */
    public function read(string $id)
    {
        $user = Auth::user();

        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // redirect ไปหน้าที่แนบมากับ notification
        $url = $notification->data['url'] ?? url('/');

        return redirect($url);
    }

    /**
     * อ่านทั้งหมด (optional แต่แนะนำ)
     */
    public function readAll()
    {
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'อ่านการแจ้งเตือนทั้งหมดแล้ว');
    }
}
