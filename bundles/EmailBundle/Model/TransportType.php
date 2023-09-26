<?php

namespace Autoborna\EmailBundle\Model;

class TransportType
{
    const TRANSPORT_ALIAS = 'transport_alias';

    const FIELD_HOST     = 'field_host';
    const FIELD_PORT     = 'field_port';
    const FIELD_USER     = 'field_user';
    const FIELD_PASSWORD = 'field_password';
    const FIELD_API_KEY  = 'field_api_key';

    /**
     * @var array
     */
    private $transportTypes = [
        'autoborna.transport.amazon'       => 'autoborna.email.config.mailer_transport.amazon',
        'autoborna.transport.amazon_api'   => 'autoborna.email.config.mailer_transport.amazon_api',
        'autoborna.transport.elasticemail' => 'autoborna.email.config.mailer_transport.elasticemail',
        'gmail'                         => 'autoborna.email.config.mailer_transport.gmail',
        'autoborna.transport.mandrill'     => 'autoborna.email.config.mailer_transport.mandrill',
        'autoborna.transport.mailjet'      => 'autoborna.email.config.mailer_transport.mailjet',
        'smtp'                          => 'autoborna.email.config.mailer_transport.smtp',
        'autoborna.transport.postmark'     => 'autoborna.email.config.mailer_transport.postmark',
        'autoborna.transport.sendgrid'     => 'autoborna.email.config.mailer_transport.sendgrid',
        'autoborna.transport.pepipost'     => 'autoborna.email.config.mailer_transport.pepipost',
        'autoborna.transport.sendgrid_api' => 'autoborna.email.config.mailer_transport.sendgrid_api',
        'sendmail'                      => 'autoborna.email.config.mailer_transport.sendmail',
        'autoborna.transport.sparkpost'    => 'autoborna.email.config.mailer_transport.sparkpost',
    ];

    /**
     * @var array
     */
    private $showHost = [
        'smtp',
    ];

    /**
     * @var array
     */
    private $showPort = [
        'smtp',
        'autoborna.transport.amazon',
    ];

    /**
     * @var array
     */
    private $showUser = [
        'autoborna.transport.mailjet',
        'autoborna.transport.sendgrid',
        'autoborna.transport.pepipost',
        'autoborna.transport.elasticemail',
        'autoborna.transport.amazon',
        'autoborna.transport.amazon_api',
        'autoborna.transport.postmark',
        'gmail',
        // smtp is left out on purpose as the auth_mode will manage displaying this field
    ];

    /**
     * @var array
     */
    private $showPassword = [
        'autoborna.transport.mailjet',
        'autoborna.transport.sendgrid',
        'autoborna.transport.pepipost',
        'autoborna.transport.elasticemail',
        'autoborna.transport.amazon',
        'autoborna.transport.amazon_api',
        'autoborna.transport.postmark',
        'gmail',
        // smtp is left out on purpose as the auth_mode will manage displaying this field
    ];

    /**
     * @var array
     */
    private $showApiKey = [
        'autoborna.transport.sparkpost',
        'autoborna.transport.mandrill',
        'autoborna.transport.sendgrid_api',
    ];

    /**
     * @var array
     */
    private $showAmazonRegion = [
        'autoborna.transport.amazon',
        'autoborna.transport.amazon_api',
    ];

    /**
     * @param $serviceId
     * @param $translatableAlias
     * @param $showHost
     * @param $showPort
     * @param $showUser
     * @param $showPassword
     * @param $showApiKey
     */
    public function addTransport($serviceId, $translatableAlias, $showHost, $showPort, $showUser, $showPassword, $showApiKey)
    {
        $this->transportTypes[$serviceId] = $translatableAlias;

        if ($showHost) {
            $this->showHost[] = $serviceId;
        }

        if ($showPort) {
            $this->showPort[] = $serviceId;
        }

        if ($showUser) {
            $this->showUser[] = $serviceId;
        }

        if ($showPassword) {
            $this->showPassword[] = $serviceId;
        }

        if ($showApiKey) {
            $this->showApiKey[] = $serviceId;
        }
    }

    /**
     * @return array
     */
    public function getTransportTypes()
    {
        return $this->transportTypes;
    }

    /**
     * @return string
     */
    public function getServiceRequiresHost()
    {
        return $this->getString($this->showHost);
    }

    /**
     * @return string
     */
    public function getServiceRequiresPort()
    {
        return $this->getString($this->showPort);
    }

    /**
     * @return string
     */
    public function getServiceRequiresUser()
    {
        return $this->getString($this->showUser);
    }

    /**
     * @return string
     */
    public function getServiceDoNotNeedAmazonRegion()
    {
        $tempTransports     = $this->transportTypes;

        $transports               = array_keys($tempTransports);
        $doNotRequireAmazonRegion = array_diff($transports, $this->showAmazonRegion);

        return $this->getString($doNotRequireAmazonRegion);
    }

    /**
     * @return string
     */
    public function getServiceDoNotNeedUser()
    {
        // The auth_mode data-show-on will handle smtp
        $tempTransports = $this->transportTypes;
        unset($tempTransports['smtp']);

        $transports       = array_keys($tempTransports);
        $doNotRequireUser = array_diff($transports, $this->showUser);

        return $this->getString($doNotRequireUser);
    }

    public function getServiceDoNotNeedPassword()
    {
        // The auth_mode data-show-on will handle smtp
        $tempTransports = $this->transportTypes;
        unset($tempTransports['smtp']);

        $transports       = array_keys($tempTransports);
        $doNotRequireUser = array_diff($transports, $this->showPassword);

        return $this->getString($doNotRequireUser);
    }

    /**
     * @return string
     */
    public function getServiceRequiresPassword()
    {
        return $this->getString($this->showPassword);
    }

    /**
     * @return string
     */
    public function getServiceRequiresApiKey()
    {
        return $this->getString($this->showApiKey);
    }

    /**
     * @return string
     */
    public function getSmtpService()
    {
        return '"smtp"';
    }

    /**
     * @return string
     */
    public function getAmazonService()
    {
        return $this->getString($this->showAmazonRegion);
    }

    /**
     * @return string
     */
    public function getMailjetService()
    {
        return '"autoborna.transport.mailjet"';
    }

    /**
     * @return string
     */
    private function getString(array $services)
    {
        return '"'.implode('","', $services).'"';
    }
}
