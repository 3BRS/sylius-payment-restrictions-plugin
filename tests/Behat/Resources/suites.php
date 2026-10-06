<?php

declare(strict_types=1);

use Behat\Config\Config;

return (new Config())
    ->import([
        'suites/limit_paymet_method_by_shipping_method.php',
        'suites/limit_paymet_method_by_zone.php',
        'suites/set_shipping_method_restrictions_to_payment_method.php',
        'suites/set_zone_restrictions_to_payment_method.php',
    ]);
