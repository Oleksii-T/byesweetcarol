<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FeedbackStatus;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackBan;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.feedbacks.index');
        }

        $feedbacks = Feedback::when($request->status !== null, function ($q) {
            $q->where('status', request()->status);
        });

        return Feedback::dataTable($feedbacks);
    }

    public function show(Request $request, Feedback $feedback)
    {
        return view('admin.feedbacks.show', compact('feedback'));
    }

    public function spamByIp(Request $request, Feedback $feedback)
    {
        Feedback::query()
            ->where('ip', $feedback->ip)
            ->update([
                'status' => FeedbackStatus::SPAM,
            ]);

        FeedbackBan::firstOrCreate(
            [
                'type' => 'ip',
                'value' => $feedback->ip,
            ],
            [
                'action' => 'spam',
            ]
        );

        return $this->jsonSuccess('Feedbacks banned by IP');
    }

    public function update(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'status' => ['required'],
        ]);

        $feedback->update($data);

        if ($request->ajax()) {
            return $this->jsonSuccess('Feedback updated successfully!');
        }

        return redirect()->back();
    }

    public function read(Request $request, Feedback $feedback)
    {
        $feedback->update([
            'is_read' => true,
        ]);

        return redirect()->back();
    }

    public function destroy(Request $request, Feedback $feedback)
    {
        $feedback->delete();

        return $this->jsonSuccess('Feedback updated successfully');
    }
}
