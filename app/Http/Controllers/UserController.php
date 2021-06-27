<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\jobFechted;
use Illuminate\Support\LazyCollection;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function jobs(User $user): LazyCollection
    {
        $jobs = DB::transaction(function () use ($user) {
            $query = $user->jobs()->where('is_completed', true);

            if ($user->id == 2)
                return $query->where('status', 1)->where('main', false);
            elseif ($user->id == 3)
                return $query->where('status', 2)->where('main', true);

            $query = $query->where('status', 0);

            return $query->get();
        });


        /* Lazy Collection and allows you to use the existing collection methods.
         * In this case, no query is being generated!.
         * The make() method will help to chain the entire section with other helper collections
         * we take $records with chunk($records) method
         */
        $jobs = LazyCollection::make(function () use ($jobs) {
            yield $jobs;
        })->each(function ($job) use ($user) {
            Mail::to($user->email)->send(jobFechted::getInstance('Fetched job id: ' . $job->id));
        });

        return $jobs;
    }
}
