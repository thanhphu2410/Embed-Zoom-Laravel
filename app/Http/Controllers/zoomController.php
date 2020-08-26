<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use MacsiDigital\Zoom\Facades\Zoom;

class zoomController extends Controller
{
    public function createMeeting(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $validator = $request->validate([
            'password' => 'max:8',
            'date' => 'after:yesterday'
        ]);
        if(strtotime($request->date) == strtotime(date('Y-m-d')) && time() > strtotime($request->time)){
            return redirect('/zoom/meetings')->with('timeError', 'Time must be greater than '.date("h:i", time()));
        }
        $user = Zoom::user()->find('me');
        $meeting = Zoom::meeting()->make([
            'topic' => $request->topic,
            'type' => 2,
            'start_time' =>  new Carbon(date("Y-m-d", strtotime($request->date))." ".$request->time),
            'duration' => $request->duration,
            'password' => $request->password,
            'agenda' => $request->description,
            'settings' => [
                'host_video' => 0,
                'participant_video' => 0,
                'waiting_room' => 0,
                'join_before_host' => 0,
                'audio' => 'both',
                'auto_recording' => 'none',
                'approval_type' => 0,
                'mute_upon_entry' => 0
            ]
        ]);
        $user->meetings()->save($meeting);

        return redirect('/meetings-list');
    }

    public function meetingsList(Request $request)
    {
        $meetings = Zoom::user()->find('me')->meetings;
        return view('meetingList', compact('meetings'));
    }

    public function deleteMeeting($id)
    {
        $meeting = Zoom::user()->find('me')->meetings()->find($id);
        $meeting->delete();
        return redirect('/');
    }
}
