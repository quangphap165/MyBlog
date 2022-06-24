<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequests\NewsletterRequest;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request)
    {
        return Newsletter::store($request);
    }
}
