<?php

namespace App\Http\Controllers\Admin;

use App\Factories\TranslatorFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TranslateRequest;

class TranslateController extends Controller
{
    public function translate(TranslateRequest $request)
    {
        $data = $request->validated();
        $factory = new TranslatorFactory;
        $translator = $factory->make($data['translator']);
        $resultDto = $translator->make($data['text'], $data['language']);

        return $this->jsonSuccess('', $resultDto->result);
    }
}
