<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Models\Courseyears;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Status;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $user = Auth::user();

        $selectedYear = $request->input('year', now()->year);

        $courses = DB::table('courses as c')
            ->leftJoin('courseyears as cy', 'c.course_id', '=', 'cy.course_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
            ->where('cy.user_id', $user->user_id)
            ->where('cy.year', $selectedYear)
            ->select(
                'c.course_id',
                'c.course_name',
                's.startprompt',
                's.generated',
                's.downloaded',
                's.success'
            )
            ->get();

        
         return view('notification', compact('courses'));
    }
}
