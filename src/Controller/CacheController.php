<?php 

declare(strict_types=1);

namespace AndersBjorkland\MaintenanceExtension\Controller;

use AndersBjorkland\MaintenanceExtension\Extension;
use AndersBjorkland\MaintenanceExtension\Service\OpcacheStatus;
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
     * @Route("/extensions/maintenance/", name="maintenance_extension_clear")
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

        $errors = [];
        $opcache = [];
        $status = OpcacheStatus::getArray();
        if (function_exists('opcache_reset')) {
            $status["cleared"] = opcache_reset();
        }
        $opcache = $status;
        
        $response = [];
        $response["opcache"] = $opcache;

        if (count($errors) > 0) {
            $response["errors"] = $errors;
        }

        return $this->json($response);
    }
}