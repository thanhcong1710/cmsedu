<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 3/13/2018
 * Time: 9:28 AM
 */

namespace App\Models;
use App\Models\APICode;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Providers\UtilityServiceProvider as u;
use PHPMailer\PHPMailer\SMTP;

use function GuzzleHttp\json_decode;

class Mail
{
    private $email = 'noreply@cmsedu.vn';
    private $email_name = 'CMS';
    private $password = "xiab jcqq hndj eagd";
    private $port = 587;
    private $host = 'smtp.gmail.com';
    private $charSet = 'utf-8';

    /*
     * $from = ["address"=>"example@mail.com","name" => "sender's full name"]
     * $to = ["address"=>"example@mail.com","name" => "receiver's full name"]
     *
     * */
    public function sendSingleMail($to=[],$subject, $body,$arr_cc = [], $arr_att = [],$from =[]){
        DB::table('email_queues')->insert([
            'email_from' => json_encode($from),
            'email_to' => json_encode($to),
            'email_subject' => $subject,
            'email_body' => $body,
            'email_cc' => json_encode($arr_cc),
            'email_attack' => json_encode($arr_att),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }

    public function  processMail(){
        $list_email = u::query("SELECT * FROM email_queues WHERE `status`=0 AND `lock`=0 LIMIT 20");
        u::query("UPDATE email_queues SET `lock`=1 , add_queue_at ='".date('Y-m-d H:i:s')."' WHERE `status`=0 AND `lock`=0 LIMIT 20");
        foreach($list_email AS $email){
            $to=json_decode($email->email_to,true);
            $subject=$email->email_subject; 
            $body=$email->email_body; 
            $arr_cc = json_decode($email->email_cc,true);
            $arr_att = json_decode($email->email_attack,true);
            $email_from=json_decode($email->email_from,true);

            $code = APICode::SUCCESS;
            if (APP_ENV === "product" ||1==1){
                $mail = new PHPMailer();

                if(empty($to)){
                    $code = APICode::INVALID_MAIL_INFO;
                }else{
                    try{
                        $mail->isSMTP();
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        $mail->CharSet = $this->charSet;
                        $mail->Host = $this->host;
                        $mail->SMTPAuth = true;
                        $tmp_email_from = isset($email_from['email']) && $email_from['email']?$email_from['email'] :$this->email;
                        $tmp_email_password = isset($email_from['password']) && $email_from['password']?$email_from['password'] :$this->password;
                        $mail->Username =  $tmp_email_from;
                        $mail->Password = $tmp_email_password;
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = $this->port;
                        $mail->setFrom($tmp_email_from, $this->email_name);
                        $mail->addAddress($to['address'], $to['name']);
                        if(!empty($arr_cc)){
                            foreach($arr_cc AS $cc){
                                $mail->addCC($cc);
                            }
                        }
                        // Attachments
                        if(!empty($arr_att)){
                            foreach($arr_att AS $item) {
                                $item = (Object)$item;
                                $mail->addAttachment(DIRECTORY . DS . $item->url, $item->name); 
                            }
                        }

                        //Content
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $body;
                        $tmp= $mail->send();
                    }catch (Exception $exception){
                        $code = APICode::FAILURE_SENDING_MAIL;
                    }
                }
            }
            u::query("UPDATE email_queues SET 
                    `lock`=0,
                    `status`=1,
                    send_at = '".date('Y-m-d H:i:s')."',
                    send_code = '$code'
                WHERE id=$email->id");

        }

        return true;
    }

    /*
    * $from = ["address"=>"example@mail.com","name" => "sender's full name"]
    * $to = [
     *          ["address"=>"example@mail.com","name" => "receiver's full name"],
     *          ...
     *      ]
    *
    * */

    public function sendMultiMails($from=[], $to=[],$subject, $body){
        $code = APICode::SUCCESS;
        $mail = new PHPMailer();

        if(empty($from) || empty($to)){
            $code = APICode::INVALID_MAIL_INFO;
        }else{
            try{
                $mail->isSMTP();
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                $mail->CharSet = $this->charSet;
                $mail->Host = $this->host;
                $mail->SMTPAuth = true;
                $mail->Username = $this->email;
                $mail->Password = $this->password;
                $mail->SMTPSecure = 'tls';
                $mail->Port = $this->port;

                $mail->setFrom($from['address'], $from['name']);
                foreach ($to as $t){
                    $mail->addAddress($t['address'], $t['name']);
                }

                //Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;
                $mail->send();
            }catch (Exception $exception){
                $code = APICode::FAILURE_SENDING_MAIL;
            }
        }

        return $code;
    }
}