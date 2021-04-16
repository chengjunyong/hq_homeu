<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $subject = "";
      $template = "emails.default";
      $data = array();
      $files = array();

      if(isset($this->data['files']))
      {
        $files = $this->data['files'];
      }

      $email = $this->from('support@homeumsd.com', 'Homeu(U) Sdh Bhd')->subject($subject)->view($template)->with('data', $data);
      
      if(count($files) > 0)
      {
        foreach($files as $file)
        {
          // $contents = Storage::get('public/'.$file);
          $email = $email->attach('storage/'.$file);
        }
      }

      return $email;
    }
}
