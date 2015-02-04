<?php

class D2mailerModule extends CWebModule
{
    public $fromEmail;
    public $fromName;
    public $smtp_host;
    public $smtp_port;
	
    public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'd2mailer.components.*',
		));
	}

    

}