<?php

/**
 * PHPmServer - Client Class
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  (C) 2020,2023 Vitex Software
 */

namespace mServer;

use Ease\Atom;
use Ease\Functions;
use Ease\Molecule;
use Lightools\Xml\XmlException;
use Lightools\Xml\XmlLoader;
use Riesenia\Pohoda;

/**
 * Stormware's Pohoda mServer's client class.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class Client extends \Ease\Sand
{
    use \Ease\RecordKey;

    /**
     * Curl Handle.
     *
     * @var resource
     */
    private $curl = null;

    /**
     * We Connect to server by default
     * @var boolean
     */
    public $offline = false;

    /**
     * Override cURL timeout
     * @var int seconds
     */
    public $timeout = null;

    /**
     * Body data  for next curl POST operation
     *
     * @var string
     */
    public $postFields = null;

    /**
     * Enable Curl Compress ?
     * @var boolean
     */
    public $compress = true;

    /**
     * Raw Content of last curl response
     *
     * @var string
     */
    public $lastCurlResponse;

    /**
     * HTTP Response code of last request
     *
     * @var int
     */
    public $lastResponseCode = null;

    /**
     * Informace o poslední HTTP chybě.
     *
     * @var string
     */
    public $lastCurlError = null;

    /**
     * Informace o posledním HTTP requestu.
     *
     * @var mixed
     */
    public $curlInfo;

    /**
     * Array of errors
     *
     * @var array
     */
    public $errors = [];

    /**
     * Response stats live here
     *
     * @var array
     */
    public $responseStats = [];

    /**
     * @var array of Http headers attached with every request
     */
    public $defaultHttpHeaders = [
        'STW-Application' => 'PHPmServer',
        'Accept' => 'application/xml',
        'Content-Type' => 'application/xml'
    ];

    /**
     * [protocol://]Server[:port]
     * @var string
     */
    public $url = null;

    /**
     * REST API Username
     * @var string
     */
    public $user = null;

    /**
     * REST API Password
     * @var string
     */
    public $password = null;

    /**
     * My Company identification ID
     * @var string
     */
    protected $ico = null;

    /**
     * XML Response Processor
     * @var Pohoda
     */
    protected $pohoda;

    /**
     * Response holder
     * @var Response
     */
    protected $response = null;

    /**
     * Current Object's agenda
     * @var string
     */
    public $agenda = null;

    /**
     * Request XML helper
     * @var Pohoda\Agenda
     */
    public $requestXml = null;

    /**
     * Where to find current record name.
     * @var string column name or path in array address:company
     */
    public $nameColumn = null;

    /**
     * Path to teporary XML file
     * @var string|null
     */
    public $xmlCache = null;

    /**
     * mServer client class
     *
     * @param mixed $init    default record id or initial data. See processInit()
     * @param array $options Connection settings and other options override
     */
    public function __construct($init = null, $options = [])
    {
        parent::setObjectName();
        $this->setUp($options);
        $this->curlInit();
        Pohoda::$encoding = 'UTF-8';
        $this->reset();
        if (!empty($init)) {
            $this->processInit($init);
        }
    }

    /**
     * Prepare XML processing engine
     */
    public function reset()
    {
        $this->dataReset();
        $this->pohoda = new Pohoda($this->ico);
        $this->pohoda->setApplicationName(Functions::cfg('APP_NAME', 'PHPmPohoda'));
        $this->xmlCache = sys_get_temp_dir() . '/phpmPohoda_' . Functions::randomString() . '.xml';
        $this->pohoda->open($this->xmlCache, microtime(), 'generated by PHPmPohoda');
        if ($this->debug) {
            $this->addStatusMessage('PHPmPohoda XMLCache: ' . $this->xmlCache, 'debug');
        }
    }

    public function setObjectName($forceName = '')
    {
        parent::setObjectName(($this->getMyKey() ? $this->getMyKey() . '@' : '') . \Ease\Logger\Message::getCallerName($this));
    }

    /**
     * Process and use initial value
     *
     * @param mixed $init
     */
    public function processInit($init)
    {
        if (is_integer($init)) {
            $this->loadFromPohoda($init);
        } elseif (is_array($init)) {
            $this->takeData($init);
        } elseif (preg_match('/\.(json|xml|csv)/', $init)) {
            $this->takeData($this->getPohodaData((($init[0] != '/') ? $this->evidenceUrlWithSuffix($init) : $init)));
        } else {
            $this->loadFromPohoda($init);
        }
    }

    /**
     * Add Info about used user, server and libraries
     *
     * @param string $prefix banner prefix text
     * @param string $suffix banner suffix text
     */
    public function logBanner($prefix = null, $suffix = null)
    {
        parent::logBanner(
            $prefix,
            'mServer ' . str_replace('://', '://' . $this->user . '@', $this->url) . ' PHPmServer v' . self::libVersion() .
                $suffix
        );
    }

    /**
     * SetUp Object to be ready for work
     *
     * @param array $options Object Options ( user,password,authSessionId
     *                                        company,url,agenda,
     *                                        debug,
     *                                        filter,ignore404
     *                                        timeout,companyUrl,ver,throwException
     */
    public function setUp($options = [])
    {
        $this->setupProperty($options, 'ico', 'POHODA_ICO');
        $this->setupProperty($options, 'url', 'POHODA_URL');
        $this->setupProperty($options, 'user', 'POHODA_USERNAME');
        $this->setupProperty($options, 'password', 'POHODA_PASSWORD');
        $this->setupProperty($options, 'timeout', 'POHODA_TIMEOUT');
        $this->setupProperty($options, 'compress', 'POHODA_COMPRESS');
        if (isset($options['agenda'])) {
            $this->setAgenda($options['agenda']);
        }

        if (array_key_exists('instance', $options)) {
            $this->setInstance($options['instance']);
        }

        if (array_key_exists('application', $options)) {
            $this->setApplication($options['application']);
        }

        if (array_key_exists('duplicity', $options)) {
            $this->setCheckDuplicity($options['duplicity']);
        }

        $this->setupProperty($options, 'debug', 'POHODA_DEBUG');
    }

    /**
     * Set Authentification
     *
     * @return boolean
     */
    public function setAuth()
    {
        $this->defaultHttpHeaders['STW-Authorization'] = 'Basic ' . base64_encode($this->user . ':' . $this->password);
        return strlen($this->user) && strlen($this->password);
    }

    /**
     * Set Instance http header
     *
     * @param string $instance
     */
    public function setInstance(string $instance)
    {
        $this->defaultHttpHeaders['STW-Instance'] = $instance;
    }

    /**
     * Set Application http header
     *
     * @param string $application
     */
    public function setApplication(string $application)
    {
        $this->defaultHttpHeaders['STW-Application'] = $application;
    }

    /**
     * Set "Check Duplicity" http header enabler
     *
     * @param bool $flag
     */
    public function setCheckDuplicity(bool $flag)
    {
        if ($flag) {
            $this->defaultHttpHeaders['STW-Check-Duplicity'] = 'true';
        } else {
            unset($this->defaultHttpHeaders['STW-Check-Duplicity']);
        }
    }

    /**
     * Inicializace CURL
     *
     * @return boolean Online Status
     */
    public function curlInit()
    {
        if ($this->offline === false) {
            $this->curl = \curl_init(); // create curl resource
            \curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); // return content as a string from curl_exec
            \curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true); // follow redirects
            \curl_setopt($this->curl, CURLOPT_HTTPAUTH, true); // HTTP authentication
            \curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false); // for Self-Signed certificates
            \curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            \curl_setopt($this->curl, CURLOPT_VERBOSE, ($this->debug === true)); // For debugging
            if (!is_null($this->timeout)) {
                \curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
            }
            if ($this->compress === true) {
                \curl_setopt($this->curl, CURLOPT_ENCODING, "gzip");
            }
            \curl_setopt($this->curl, CURLOPT_USERAGENT, 'mServerPHP  v' . self::libVersion() . ' https://github.com/VitexSoftware/PHP-Pohoda-Connector');
        }
        return !$this->offline && $this->setAuth();
    }

    /**
     * Prepare data to send
     *
     * @param string $data
     */
    public function setPostFields($data)
    {
        if ($this->debug) {
            $tmpfile = sys_get_temp_dir() . '/' . time() . '.xml';
            file_put_contents($tmpfile, $data);
            $this->addStatusMessage('request: ' . $tmpfile, 'debug');
            //system('netbeans ' . $tmpfile);
        }
        $this->postFields = $data;
    }

    /**
     * Perform HTTP request
     *
     * @param string $url    Request URL
     * @param string $method HTTP Method GET|POST
     *
     * @return int HTTP Response CODE
     */
    public function doCurlRequest($url, $method, $format = null)
    {
        \curl_setopt($this->curl, CURLOPT_URL, $url);
        \curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        \curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postFields);
        $httpHeaders = $this->defaultHttpHeaders;
        array_walk($httpHeaders, function (&$value, $header) {
            $value = $header . ': ' . $value;
        });
        \curl_setopt($this->curl, CURLOPT_HTTPHEADER, $httpHeaders);
        $this->lastCurlResponse = \curl_exec($this->curl);
        $this->curlInfo = \curl_getinfo($this->curl);
        $this->curlInfo['when'] = microtime();
        $this->lastResponseCode = $this->curlInfo['http_code'];
        $this->lastCurlError = \curl_error($this->curl);
        if (strlen($this->lastCurlError)) {
            $this->addStatusMessage(
                sprintf(
                    'Curl Error (HTTP %d): %s',
                    $this->lastResponseCode,
                    $this->lastCurlError
                ),
                'error'
            );
        }
        if ($this->debug) {
            $tmpName = sys_get_temp_dir() . '/response' . time() . '.xml';
            file_put_contents($tmpName, $this->lastCurlResponse);
            $this->addStatusMessage('request: ' . $tmpName, 'debug');
            //system('netbeans ' . $tmpName);
        }
        return $this->lastResponseCode;
    }

    /**
     * Funkce, která provede I/O operaci a vyhodnotí výsledek.
     *
     * @param string $urlSuffix část URL za identifikátorem firmy.
     * @param string $method    HTTP/REST metoda
     *
     * @return boolean request commit status
     */
    public function performRequest($urlSuffix = '', $method = 'POST')
    {
        $this->responseStats = [];
        $this->errors = [];
        if (preg_match('/^http/', $urlSuffix)) {
            $url = $urlSuffix;
        } elseif (strlen($urlSuffix) && ($urlSuffix[0] == '/')) {
            $url = $this->url . $urlSuffix;
        } else {
            $url = $this->url;
        }
        return $this->processResponse($this->doCurlRequest($url, $method));
    }

    /**
     * Response processing handler
     *
     * @param int $httpCode
     *
     * @return boolean
     */
    public function processResponse($httpCode)
    {

        switch ($httpCode) {
            case 400:
                $this->addStatusMessage(_('400: Bad request'), 'error');
                // "Požadavek nemůže být vyřízen, poněvadž byl syntakticky nesprávně zapsán"
                break;
            case 401:
                $this->addStatusMessage(_('401: Unauthorized'), 'error');
                //"Používán tam, kde je vyžadována autentifikace, ale nebyla zatím provedena". V tomto případě se jedná o problém, kdy buď v HTTP požadavku chybí autentizační údaje nebo daný uživatel není v programu POHODA vytvořen.
                break;
            case 403:
                $this->addStatusMessage(_('403: Forbidden'), 'error');
                //"Požadavek byl legální, ale server odmítl odpovědět". Například se jedná o problém, kdy daný uživatel nemá právo na otevření účetní jednotky v programu POHODA.
                break;
            case 404:
                $this->addStatusMessage(_('404: Not found'), 'error');
                //„Požadovaný dokument nebyl nalezen“. Jedná se o problém, kdy byla chybně zadaná URL cesta k mServeru. Například se jedná o problém, kdy v URL adrese není uvedena cesta k umístění na serveru "/XML". Příklad správně zadné URL: 192.168.0.1:444/xml
                break;
            case 405:
                $this->addStatusMessage(_('405: Method not allowed'), 'error');
                //„Požadavek byl zavolán na zdroj s metodou, kterou nepodporuje. Například se jedná o službu, na kterou se odesílají data metodou POST a někdo se je místo toho pokusí odeslat metodou GET.“
                break;
            case 408:
                $this->addStatusMessage(_('408 : Request Timeout'), 'error');
                //„Vypršel čas vyhrazený na zpracování požadavku“
                break;
            case 500:
                $this->addStatusMessage(_('500: Internal server error'), 'error');
                //„Při zpracovávání požadavku došlo k blíže nespecifikované chybě“
                break;
            case 502:
                $this->addStatusMessage(_('502: Bad Gateway'), 'error');
                //„Proxy server nebo brána obdržely od serveru neplatnou odpověď“
                break;
            case 503:
                $this->addStatusMessage(_('503: Service unavailable'), 'error');
                //„Služba je dočasně nedostupná“
                break;
            case 504:
                $this->addStatusMessage(_('504: Gateway Timeout'), 'error');
                //„Proxy server nedostal od cílového serveru odpověď v daném čase“
                break;
            case 505:
                $this->addStatusMessage(_('505: HTTP Version Not Supported'), 'error');
                //„Server nepodporuje verzi protokolu HTTP použitou v požadavku“
                break;
            default:
                $this->response = new Response($this);
//                if ($this->response->isOk() === false) {
                if ($this->response->getNote()) {
                    $this->addStatusMessage($this->response->getNote(), 'error');
                }
                foreach ($this->response->messages as $type => $messages) {
                    foreach ($messages as $message) {
                        $this->addStatusMessage($message['state'] . ' ' . $message['errno'] . ': ' . $message['note'] . (array_key_exists('XPath', $message) ? ' (' . $message['XPath'] . ')' : ''), $type);
                    }
                }
//                }
                break;
        }

        return $this->response->isOk();
    }

    /**
     * Check mServer availbilty
     *
     * @return boolean
     */
    public function isOnline()
    {
        $this->responseStats = [];
        $this->errors = [];
        return ($this->doCurlRequest($this->url . '/status', 'POST') === 200) &&
                str_contains($this->lastCurlResponse, 'Response from POHODA mServer');
    }

    /**
     * Use data in object
     *
     * @param array   $data  raw document data
     */
    public function takeData($data)
    {
        parent::takeData($data);
        $created = $this->create($this->getData());
        $this->setObjectName();
        return $created;
    }

    /**
     * Create Agenda document using given data
     *
     * @param array $data
     */
    public function create($data)
    {
        $this->requestXml = $this->pohoda->create($data);
        return empty($this->requestXml) ? 0 : 1;
    }

    /**
     * Insert prepared record to Pohoda
     *
     * @param array $data extra data
     *
     * @return int
     */
    public function addToPohoda($data = [])
    {
        if (!empty($data)) {
            $this->takeData($data);
        }
        if (method_exists($this->requestXml, 'addActionType')) {
            $this->requestXml->addActionType('add'); // "add", "add/update", "update", "delete"
        }
        $this->pohoda->addItem(2, $this->requestXml);
        return 1;
    }

    public function commit()
    {
        $this->pohoda->close();
        $this->setPostFields(file_get_contents($this->xmlCache));
        return $this->performRequest('/xml');
    }

    /**
     * Insert prepared record to Pohoda
     *
     * @param array $data extra data
     *
     * @return int
     */
    public function updateInPohoda($data = [], $filter = null)
    {
        if (!empty($data)) {
            $this->takeData($data);
        }
        if ($this->requestXml) {
            if (method_exists($this->requestXml, 'addActionType')) {
                // "add", "add/update", "update", "delete"
                $this->requestXml->addActionType('update', empty($filter) ? $this->filterToMe() : $filter);
            }
            $this->pohoda->addItem(2, $this->requestXml);
        }

        $this->setPostFields($this->pohoda->close());
        return $this->performRequest('/xml');
    }

    /**
     * Filter to select only "current" record
     *
     * @return array
     */
    public function filterToMe()
    {
        if ($this->nameColumn) {
            if (strstr($this->nameColumn, ':')) {
                $data = $this->getData();
                foreach (explode(':', $this->nameColumn) as $key) {
                    if (array_key_exists($data, $data)) {
                        $data = $data[$key];
                    } else {
                        throw new \Exception('Data Path ' . $this->nameColumn . 'does not exist');
                    }
                }
                $filter = [$key => $data];
            } else {
                $filter = [$this->nameColumn => $this->getDataValue($this->nameColumn)];
            }
        } else {
            $filter = [$this->getKeyColumn() => $this->getMyKey()];
        }
        return $filter;
    }

    /**
     * Obtain given fields from Pohoda
     *
     * @param array $columns    list of columns to obtain
     * @param array $conditions conditions to filter
     *
     * @return array
     */
    public function getColumnsFromPohoda($columns = ['id'], $conditions = [])
    {
        $this->requestXml = $this->pohoda->createListRequest(['type' => ucfirst($this->agenda)]);
        if (count($conditions)) {
            $this->requestXml->addFilter($conditions);
        }
        $this->pohoda->addItem(2, $this->requestXml);
        $xmlTmp = $this->pohoda->close();
        $this->setPostFields($this->xmlCache ? file_get_contents($this->xmlCache) : $xmlTmp);
        return $this->performRequest('/xml') ? $this->response->getAgendaData($this->agenda) : null;
    }

    /**
     * Load data from Pohoda
     *
     * @return mixed
     */
    public function loadFromPohoda($phid = null)
    {
        if (is_null($phid) === true) {
            $condition = [];
        } else {
            $condition = ['id' => $phid];
        }
        return $this->takeData($this->getColumnsFromPohoda(["*"], $condition)) ? $this->getMyKey() : null;
    }

    /**
     * Reconnect After unserialization
     */
    public function __wakeup()
    {
        $this->curlInit();
    }

    /**
     * Application version or "0.0.0" fallback
     *
     * @return string
     */
    public static function libVersion()
    {
        if (method_exists('Composer\InstalledVersions', 'getRootPackage')) {
            $package = \Composer\InstalledVersions::getRootPackage();
        } else {
            $package = [];
        }
        return array_key_exists('version', $package) ? $package['version'] : '0.0.0';
    }

    /**
     *
     */
    public function sendRequest($request)
    {
        $this->setPostFields($request);
        $this->performRequest('/xml');
        return $this->lastCurlResponse;
    }

    public function setAgenda($agenda)
    {
        $this->agenda = $agenda;
    }
}
