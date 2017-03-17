<?php

namespace Deimos\Analytics;

class Analytics
{

    /**
     * @var string Google Analytics code
     */
    private $_code;

    /**
     * @var string Domain name we are requesting from
     */
    private $_domain;

    /**
     * @var string User Agent string for this request from CURL
     */
    private $_userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.98 Safari/537.36';

    /**
     * @var string cookie name
     */
    private $_cookie = 'phpAnalytics';

    /**
     * @var bool verbose output
     */
    private $_verbose = false;

    protected $resource;
    protected $action;
    protected $label;
    protected $value = 1;

    /**
     * Url for the google analytics gif
     *
     * http://code.google.com/intl/de-DE/apis/analytics/docs/tracking/ +
     *   gaTrackingTroubleshooting.html#gifParameters
     *
     * @var string url for the gif string at google
     */
    private $_urchin_url = 'http://www.google-analytics.com/__utm.gif';

    /**
     * Setup Analytics
     *
     * @param string $code   Google Analytics key
     * @param string $domain HTTP_HOST
     *
     * @return void
     */
    public function __construct($code, $domain)
    {
        $this->_code   = $code;
        $this->_domain = $domain;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function track()
    {
        if (!$this->resource || $this->action || $this->label)
        {
            throw new \InvalidArgumentException('Object|Action|Label not found');
        }

        $url = $this->_urchin_url . $this->params();

        $ch = curl_init();

        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT      => $this->_userAgent,
                CURLOPT_VERBOSE        => $this->_verbose,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_COOKIEFILE     => $this->_cookie
            )
        );

        $output = curl_exec($ch);
        curl_close($ch);

        return 0 === strpos($output, 'GIF89a');
    }

    protected function params()
    {
        $var_cookie = random_int(10000000, 99999999);     //random cookie number
        $var_utmp   = basename(__FILE__);
        $var_today  = time();                       //today

        return '?utmwv=1'               // Tracking code version
            . '&utmn=' . random_int(1000000000, 9999999999)       // Prevent caching random number
            . '&utmsr=-'               // Screen resolution
            . '&utmsc=-'               // Screen color depth
            . '&utmul=-'               // Browser language
            . '&utmje=0'               // Is browser Java-enabled
            . '&utmfl=-'               // Flash Version
            . '&utmdt=-'               // Page title, url encoded
            . '&utmhn=' . $this->_domain   // Host Name
            . '&utmp=' . $var_utmp    // page
            . '&utmr=' . $this->getCurrentUrl() // Referral, complete url
            . '&utmac=' . $this->_code   // Account code
            . '&utmt=event'            // Type of request
            // utme is an extensible parameter, used for the event data here
            . "&utme=" . rawurlencode("5($this->resource*$this->action*$this->label)($this->value):")
            . '&utmcc=__utma%3D' . $var_cookie . '.' . random_int(1000000000, 2147483647) . '.' . $var_today
            . '.' . $var_today . '.' . $var_today . '.2%3B%2B__utmb%3D'
            . $var_cookie . '%3B%2B__utmc%3D' . $var_cookie . '%3B%2B__utmz%3D'
            . $var_cookie . '.' . $var_today
            . '.2.2.utmccn%3D(direct)%7Cutmcsr%3D(direct)'
            . '%7Cutmcmd%3D(none)%3B%2B__utmv%3D'
            . $var_cookie . '.%3B'; // Cookie values are in this utmcc
    }

    /**
     * Get the current Url
     *
     * @return string current url
     */
    protected function getCurrentUrl()
    {
        $url = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
        $url .= '://' . $_SERVER['SERVER_NAME'];
        $url .= in_array($_SERVER['SERVER_PORT'], array('80', '443')) ? '' : ':' . $_SERVER['SERVER_PORT'];
        $url .= $_SERVER['REQUEST_URI'];

        return $url;
    }
}
