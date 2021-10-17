<?php 

declare(strict_types=1);

namespace AndersBjorkland\MaintenanceExtension\Controller;

use AndersBjorkland\MaintenanceExtension\Extension;
use AndersBjorkland\MaintenanceExtension\Service\OpcacheStatusParser;
use Bolt\Configuration\Config;
use Bolt\Extension\ExtensionController;
use Bolt\Extension\ExtensionRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CacheController extends ExtensionController
{
    protected $registry;
    protected $extensionConfig;

    public function __construct(Config $config, ExtensionRegistry $registry) {
        parent::__construct($config);
        $this->registry = $registry;
    }

    /**
     * @Route("/extensions/maintenance/cache/clear", name="maintenance_extension_cache_clear")
     */
    public function clearCache(): Response
    {
        /**
         * @var Extension
         */
        $extension = $this->registry->getExtension(Extension::class);
        $this->extensionConfig = $extension->getConfig();
        
        $role = $this->extensionConfig->get('access_level');
        $role = $role ?? 'ROLE_ADMIN';

        $this->denyAccessUnlessGranted($role);

        $status = false;
        if (function_exists('opcache_reset')) {
            $status = opcache_reset();
        }

        return $this->json(["opcache" => ["cleared" => $status]]);
    }

    /**
     * @Route("/extensions/maintenance/cache/status", name="maintenance_extension_cache_status")
     */
    public function cacheStatus(): Response
    {
        /**
         * @var Extension
         */
        $extension = $this->registry->getExtension(Extension::class);
        $this->extensionConfig = $extension->getConfig();
        
        $role = $this->extensionConfig->get('access_level');
        $role = $role ?? 'ROLE_ADMIN';
        $this->denyAccessUnlessGranted($role);

        $opcache = OpcacheStatusParser::getArray();
        $response["opcache"] = $opcache;

        return $this->json($response);
    }
}