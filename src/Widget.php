<?php 

declare(strict_types=1);

namespace AndersBjorkland\MaintenanceExtension;

use AndersBjorkland\MaintenanceExtension\Service\OpcacheStatusParser;
use Bolt\Widget\BaseWidget;
use Bolt\Widget\Injector\AdditionalTarget;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\TwigAwareInterface;

class Widget extends BaseWidget implements TwigAwareInterface
{
    protected $name;
    protected $target;
    protected $priority;
    protected $template;
    protected $zone;

    public function __construct() {
        $this->name = "AndersBjorkland MaintenanceExtension";
        $this->target = AdditionalTarget::WIDGET_BACK_DASHBOARD_ASIDE_TOP;
        $this->priority = 100;
        $this->template = '@andersbjorkland-maintenanceextension/widget.html.twig';
        $this->zone = RequestZone::BACKEND;
    }

    public function run(array $params = []): ?string
    {
        return parent::run(['opcache' => OpcacheStatusParser::getArray()]);
    }
}