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
use App\Models\User;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        // $selectedYear = $request->input('year', now()->year);

        $courses = DB::table('courses as c')
            ->leftJoin('courseyears as cy', 'c.course_id', '=', 'cy.course_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
            ->leftJoin('users as u', 'cy.user_id', '=', 'u.user_id')
            // ->where('cy.year', $selectedYear)
            ->whereNotNull('cy.user_id')
            ->select(
                'cy.course_id',
                'c.course_name_th',
                'u.name',
                'cy.term',
                'cy.year',
                'cy.TQF',
                's.startprompt',
                's.generated',
                's.downloaded',
                's.success',
                's.ref_id'
            )
            ->get();

        //เอาเฉพาะที่ยังไม่ครบ 100%
        $filtered = $courses->filter(function ($item) {
            $stepCount = collect([
                $item->startprompt,
                $item->generated,
                $item->downloaded,
                $item->success
            ])->filter()->count();

            $progress = ($stepCount / 4) * 100;

            return $progress < 100;
        });

        // return view('notification', compact('courses'));
        return view('notification', ['courses' => $filtered]);
    }
}
