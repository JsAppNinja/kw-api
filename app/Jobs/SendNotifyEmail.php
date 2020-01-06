<?php

namespace App\Jobs;

use App\Event;
use App\ApiUser;
use App\Subscriber;
use App\Jobs\Job;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotifyEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $email;
    protected $title;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $title, $message)
    {
        $this->email = $email;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        $mailer->send('emails.event_notify',
            [
                'content'=>$this->message,
                'email'=>$this->email, 
                'title'=> $this->title
            ], function ($m) {
            $m->from(env('MAIL_FROM'),env('MAIL_NAME'));
            $m->to($this->email);
            $m->subject($this->title);
        });
        return true;
    }
}
