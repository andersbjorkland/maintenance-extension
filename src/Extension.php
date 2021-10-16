<?php 

declare(strict_types=1);

namespace AndersBjorkland\MaintenanceExtension;

use Bolt\Extension\BaseExtension;

class Extension extends BaseExtension
{

    public function getName(): string
    {
        return 'Maintenance Extension';
    }

    public function initialize($cli = false): void
    {
        $this->addWidget(new Widget());

        $this->addTwigNamespace('maintenance-extension');

    }

    public function install(): void
    {
        $this->getConfig();
    }
}