<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackBan;
use Illuminate\Http\Request;

class FeedbackBanController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.feedback-bans.index');
        }

        $bans = FeedbackBan::query();

        return FeedbackBan::dataTable($bans);
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'type' => ['required', 'string'],
            'value' => ['required', 'string'],
            'action' => ['required', 'string'],
        ]);

        FeedbackBan::create($input);

        return $this->jsonSuccess('Feedback ban create successfully');
    }

    public function update(Request $request, FeedbackBan $feedbackBan)
    {
        $input = $request->validate([
            'type' => ['required', 'string'],
            'value' => ['required', 'string'],
            'action' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $feedbackBan->update($input);

        return $this->jsonSuccess('Feedback ban updated successfully');
    }

    public function destroy(Request $request, FeedbackBan $feedbackBan)
    {
        $feedbackBan->delete();

        return $this->jsonSuccess('Feedback ban deleted successfully');
    }
}
