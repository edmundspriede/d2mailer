<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class d2mailer {

    /**
     * settings from module
     */
    public $fromEmail;
    public $fromName;
    public $smtp_host;
    public $smtp_port;
    public $error = false;

    public function __construct() {
        
        /**
         * load config from module
         */
        $module = Yii::app()->getModule('d2mailer');
        $this->fromEmail = $module->fromEmail;
        $this->fromName = $module->fromName;
        $this->smtp_host = $module->smtp_host;
        $this->smtp_port = $module->smtp_port;
    }

    /**
     * send email to user by smtp
     * @param int $user_id
     * @param string $subject
     * @param string $message
     * @param string $from_email
     * @param string $from_name
     * @return boolean/string false on error, string on success
     */
    public function sendMailToUser($user_id, $subject, $message, $from_email = false, $from_name = false) {

        $this->error = false;
        
        if(!$from_email){
            $from_email = $this->fromEmail;
        }

        if(!$from_name){
            $from_name = $this->fromName;
        }
        
        $user = User::model()->findbyPk($user_id);

        //get profile
        $profile = Profile::model()->findByAttributes(['person_id' => $this->pprs_id]);
        if (!$user) {
            $this->error = Yii::t('D2mailerModule.errors', 'Can not found user');
            return false;
        }

        $user_full_name = $user->profile->first_name . ' ' . $user->profile->last_name;
        
        //validate
        if (empty($user->email)) {
            $this->error = Yii::t('D2mailerModule.errors', 'User don\'t have email: ')
                    . $user_full_name;
            return false;
        }

        //create message
        $swiftMessage = Swift_Message::newInstance($subject);
        $swiftMessage->setBody($message, 'text/html');
        $swiftMessage->setFrom($from_email, $from_name);
        $swiftMessage->setTo($user->email, $user_full_name);

        /** 
         * Create the Mailer and Send
         * @link http://swiftmailer.org/docs/sending.html
         */
        
        // Create the Transport
        $transport = Swift_SmtpTransport::newInstance($this->smtp_host, $this->smtp_port);
  
        //create Mauler and send
        if(!Swift_Mailer::newInstance($transport)->send($swiftMessage)){
            $this->error = Yii::t('D2personModule.model', 'Can not send email to ')
                    . $user_full_name . ' '
                    . $profile->user->email;
            return false;
            
        }

        return $user_full_name . ' ' . $profile->user->email;
    }

}
