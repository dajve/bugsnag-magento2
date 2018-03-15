<?php
/**
 * Client for BugSnag
 * Extending from bugsnag-php (https://github.com/bugsnag/bugsnag-php)
 *
 * @author Josh Carter <josh@interjar.com>
 */
namespace Interjar\BugSnag\Bugsnag;

use Bugsnag\Client as BugSnag_Client;
use Bugsnag\Report;
use Interjar\BugSnag\Helper\Config;
use Magento\Framework\App\Request\Http;

class Client extends BugSnag_Client
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Client constructor
     * Get API KEY from env.php and create instance of BugSnag_Client
     *
     * @param Config $config
     * @param Http $request
     */
    public function __construct(
        Config $config,
        Http $request
    )
    {
        $this->config = $config->getConfiguration();
        if($this->config) {
            parent::__construct($this->config, null, parent::makeGuzzle());

            \Bugsnag\Handler::register($this);

            $this->registerCallback(function (Report $report) use ($request) {
                $report->addMetaData([
                    'request' => [
                        'base_path' => $request->getBasePath(),
                        'base_url' => $request->getBaseUrl(),
                        'client_ip' => $request->getClientIp(),
                        'controller_name' => $request->getControllerName(),
                        'distro_base_url' => $request->getDistroBaseUrl(),
                        'front_name' => $request->getFrontName(),
                        'full_action_name' => $request->getFullActionName(),
                        'host' => $request->getHttpHost(),
                        'is_ajax' => $request->isAjax(),
                        'params' => $request->getParams(),
                        'path_info' => $request->getPathInfo(),
                        'method' => $request->getMethod(),
                        'protocol' => $request->getScheme(),
                        'request_string' => $request->getRequestString(),
                        'route' => $request->getRouteName(),
                        'uri' => $request->getRequestUri(),
                        'user_params' => $request->getUserParams()
                    ]
                ]);
            });
        }
    }
}
